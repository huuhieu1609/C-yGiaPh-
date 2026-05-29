import React, {
  useEffect,
  useState,
} from "react";

import {
  View,
  Text,
  StyleSheet,
  ActivityIndicator,
  FlatList,
  TextInput,
  TouchableOpacity,
  StatusBar,
  Platform,
  Alert,
  Modal,
} from "react-native";

import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function DeXuatScreen() {
  const [proposals, setProposals] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Trạng thái bộ lọc
  const [statusFilter, setStatusFilter] = useState<"Tất cả" | "pending" | "approved" | "rejected">("pending");
  const [searchQuery, setSearchQuery] = useState("");

  // Modal phê duyệt / từ chối
  const [showNoteModal, setShowNoteModal] = useState(false);
  const [selectedId, setSelectedId] = useState<number | null>(null);
  const [actionType, setActionType] = useState<"approve" | "reject">("approve");
  const [note, setNote] = useState("");

  useEffect(() => {
    loadProposals();
  }, []);

  const loadProposals = async () => {
    try {
      setLoading(true);
      const res = await api.get("/de-xuat/get-data");
      if (res.data.status) {
        setProposals(res.data.data || []);
      }
    } catch (error) {
      console.log(error);
      Alert.alert("Lỗi", "Không thể tải danh sách đề xuất");
    } finally {
      setLoading(false);
    }
  };

  const xuLyPheDuyet = async () => {
    if (!selectedId) return;

    try {
      setLoading(true);
      const endpoint = actionType === "approve" ? "/de-xuat/approve" : "/de-xuat/reject";
      const payload = {
        id: selectedId,
        note: note || (actionType === "approve" ? "Đã phê duyệt thủ công" : "Từ chối đề xuất"),
      };

      const res = await api.post(endpoint, payload);
      if (res.data.status) {
        // Ghi nhận nhật ký liên kết
        const prop = proposals.find(p => p.id === selectedId);
        const actionVerb = actionType === "approve" ? "Phê duyệt" : "Từ chối";
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: `${actionVerb} đề xuất thay đổi gia phả (Loại: ${prop ? prop.type : "Chưa rõ"}, ID: #${selectedId})`,
        });

        Alert.alert("Thành công", actionType === "approve" ? "Đã phê duyệt đề xuất!" : "Đã từ chối đề xuất!");
        setShowNoteModal(false);
        loadProposals();
      } else {
        Alert.alert("Thất bại", res.data.message || "Lỗi xử lý");
      }
    } catch (error: any) {
      Alert.alert("Lỗi", error.response?.data?.message || "Lỗi kết nối hệ thống");
    } finally {
      setLoading(false);
    }
  };

  const openActionModal = (id: number, type: "approve" | "reject") => {
    setSelectedId(id);
    setActionType(type);
    setNote("");
    setShowNoteModal(true);
  };

  // Trích xuất dữ liệu chi tiết của đề xuất
  const renderProposalData = (data: any) => {
    if (!data) return null;
    let parsedData = data;
    if (typeof data === "string") {
      try {
        parsedData = JSON.parse(data);
      } catch {
        return <Text style={styles.dataDetail}>{data}</Text>;
      }
    }

    return (
      <View style={styles.dataContainer}>
        {Object.entries(parsedData).map(([key, val]: any) => {
          // Chỉ hiện thị các trường thông tin có giá trị và dễ hiểu
          if (!val || ["id", "chi_nhanh_id", "cha_id", "me_id", "spouse_of_id"].includes(key)) return null;
          
          let keyLabel = key;
          if (key === "ho_ten") keyLabel = "Họ tên";
          else if (key === "gioi_tinh") keyLabel = "Giới tính";
          else if (key === "nghe_nghiep") keyLabel = "Nghề nghiệp";
          else if (key === "trang_thai") keyLabel = "Trạng thái";
          else if (key === "ngay_sinh") keyLabel = "Ngày sinh";
          else if (key === "ngay_mat") keyLabel = "Ngày mất";
          else if (key === "email") keyLabel = "Email";

          return (
            <Text key={key} style={styles.dataDetail}>
              • <Text style={{ fontWeight: "800", color: "#475569" }}>{keyLabel}:</Text> {String(val)}
            </Text>
          );
        })}
      </View>
    );
  };

  const getProposalTypeLabel = (type: string) => {
    switch (type) {
      case "edit":
        return "Chỉnh sửa thành viên 📝";
      case "add_child":
        return "Thêm con mới 👶";
      case "add_spouse":
        return "Thêm vợ/chồng 💍";
      default:
        return "Thay đổi gia phả 🌳";
    }
  };

  const filteredProposals = proposals.filter((p) => {
    const q = searchQuery.toLowerCase();
    
    // Tìm kiếm
    const proposer = p.proposed_by?.ho_ten || p.proposed_by?.email || "";
    const member = p.thanh_vien?.ho_ten || "";
    const matchText = proposer.toLowerCase().includes(q) || member.toLowerCase().includes(q) || p.type?.toLowerCase().includes(q);
    
    // Trạng thái
    let matchStatus = true;
    if (statusFilter !== "Tất cả") {
      matchStatus = p.status === statusFilter;
    }

    return matchText && matchStatus;
  });

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      {/* HEADER */}
      <View style={styles.header}>
        <View>
          <Text style={styles.title}>Quản Lý Đề Xuất</Text>
          <Text style={styles.subTitle}>Duyệt yêu cầu chỉnh sửa gia phả</Text>
        </View>
        <TouchableOpacity style={styles.refreshBtn} onPress={loadProposals}>
          <Ionicons name="refresh-outline" size={22} color="#0c0e12" />
        </TouchableOpacity>
      </View>

      {/* TÌM KIẾM VÀ TABS LỌC */}
      <View style={styles.filterSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={20} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm theo người đề xuất, thành viên..."
            placeholderTextColor="#94A3B8"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>

        {/* TABS LỌC TRẠNG THÁI */}
        <View style={styles.tabRow}>
          <TouchableOpacity
            style={[styles.tab, statusFilter === "pending" && styles.tabActive]}
            onPress={() => setStatusFilter("pending")}
          >
            <Text style={[styles.tabText, statusFilter === "pending" && styles.tabTextActive]}>
              Đang chờ ⏳
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tab, statusFilter === "approved" && styles.tabActive]}
            onPress={() => setStatusFilter("approved")}
          >
            <Text style={[styles.tabText, statusFilter === "approved" && styles.tabTextActive]}>
              Đã duyệt ✅
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tab, statusFilter === "rejected" && styles.tabActive]}
            onPress={() => setStatusFilter("rejected")}
          >
            <Text style={[styles.tabText, statusFilter === "rejected" && styles.tabTextActive]}>
              Từ chối ❌
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tab, statusFilter === "Tất cả" && styles.tabActive]}
            onPress={() => setStatusFilter("Tất cả")}
          >
            <Text style={[styles.tabText, statusFilter === "Tất cả" && styles.tabTextActive]}>
              Tất cả
            </Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* DANH SÁCH ĐỀ XUẤT */}
      {loading && proposals.length === 0 ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#FF9F43" />
        </View>
      ) : (
        <FlatList
          data={filteredProposals}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="mail-outline" size={60} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có đề xuất chỉnh sửa nào</Text>
            </View>
          }
          renderItem={({ item }) => {
            const timeStr = item.created_at
              ? new Date(item.created_at).toLocaleDateString("vi-VN")
              : "Gần đây";
            const isPending = item.status === "pending";
            const isApproved = item.status === "approved";

            return (
              <View style={styles.proposalCard}>
                
                {/* DÒNG LOẠI ĐỀ XUẤT */}
                <View style={styles.proposalHeader}>
                  <View style={styles.typeBadge}>
                    <Text style={styles.typeBadgeText}>{getProposalTypeLabel(item.type)}</Text>
                  </View>
                  <Text style={styles.dateText}>📅 {timeStr}</Text>
                </View>

                {/* THÀNH VIÊN ĐÍCH (EDIT THÌ HIỂN THỊ) */}
                {item.thanh_vien && (
                  <Text style={styles.targetMember}>
                    Thành viên đích: <Text style={{ fontWeight: "900", color: "#1E293B" }}>{item.thanh_vien.ho_ten}</Text>
                  </Text>
                )}

                {/* BẢNG THÔNG TIN MỚI ĐỀ XUẤT */}
                <Text style={styles.dataTitle}>Thông tin đề xuất:</Text>
                {renderProposalData(item.data)}

                {/* NGƯỜI ĐỀ XUẤT */}
                <View style={styles.proposerRow}>
                  <Text style={styles.proposerText}>
                    👤 Người đề xuất: <Text style={{ fontWeight: "800", color: "#475569" }}>
                      {item.proposed_by ? (item.proposed_by.ho_ten || item.proposed_by.email) : "Thành viên"}
                    </Text>
                  </Text>
                </View>

                {/* BÁO CÁO PHẢN HỒI (NẾU CÓ CHÚ THÍCH PHÊ DUYỆT) */}
                {item.note && (
                  <View style={styles.noteBox}>
                    <Text style={styles.noteText}>
                      💬 Phản hồi: <Text style={{ fontWeight: "700" }}>{item.note}</Text>
                    </Text>
                  </View>
                )}

                {/* NÚT THAO TÁC (CHỈ HIỂN THỊ KHI PENDING) */}
                {isPending && (
                  <View style={styles.actionRow}>
                    <TouchableOpacity
                      style={[styles.actionBtn, styles.rejectBtn]}
                      onPress={() => openActionModal(item.id, "reject")}
                    >
                      <Ionicons name="close-circle-outline" size={18} color="#EF4444" style={{ marginRight: 6 }} />
                      <Text style={[styles.actionBtnText, { color: "#EF4444" }]}>Từ chối</Text>
                    </TouchableOpacity>

                    <TouchableOpacity
                      style={[styles.actionBtn, styles.approveBtn]}
                      onPress={() => openActionModal(item.id, "approve")}
                    >
                      <Ionicons name="checkmark-circle-outline" size={18} color="#ffffff" style={{ marginRight: 6 }} />
                      <Text style={[styles.actionBtnText, { color: "#ffffff" }]}>Phê duyệt</Text>
                    </TouchableOpacity>
                  </View>
                )}

                {/* TRẠNG THÁI HIỂN THỊ NẾU ĐÃ DUYỆT / TỪ CHỐI */}
                {!isPending && (
                  <View style={styles.statusFooter}>
                    <Ionicons
                      name={isApproved ? "checkmark-circle" : "close-circle"}
                      size={20}
                      color={isApproved ? "#22C55E" : "#EF4444"}
                    />
                    <Text style={[styles.statusFooterText, { color: isApproved ? "#22C55E" : "#EF4444" }]}>
                      {isApproved ? "Đã được phê duyệt" : "Đã bị từ chối"}
                    </Text>
                  </View>
                )}

              </View>
            );
          }}
        />
      )}

      {/* MODAL NHẬP GHI CHÚ PHÊ DUYỆT / TỪ CHỐI */}
      <Modal
        visible={showNoteModal}
        animationType="fade"
        transparent={true}
        onRequestClose={() => setShowNoteModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {actionType === "approve" ? "PHÊ DUYỆT ĐỀ XUẤT" : "TỪ CHỐI ĐỀ XUẤT"}
              </Text>
              <TouchableOpacity style={styles.closeBtn} onPress={() => setShowNoteModal(false)}>
                <Ionicons name="close" size={20} color="#64748B" />
              </TouchableOpacity>
            </View>

            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Ghi chú phản hồi (Note)</Text>
              <TextInput
                style={styles.formInput}
                placeholder="Nhập lý do hoặc lời phản hồi..."
                placeholderTextColor="#94A3B8"
                value={note}
                onChangeText={setNote}
                multiline={true}
              />
            </View>

            <TouchableOpacity
              style={[styles.saveBtn, actionType === "reject" && { backgroundColor: "#EF4444" }]}
              onPress={xuLyPheDuyet}
              activeOpacity={0.8}
            >
              <Text style={styles.saveBtnText}>
                {actionType === "approve" ? "Xác nhận Duyệt" : "Xác nhận Từ chối"}
              </Text>
            </TouchableOpacity>

          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2",
  },
  glowSphere: {
    position: "absolute",
    width: 320,
    height: 320,
    borderRadius: 160,
    opacity: 0.12,
  },
  glowGold: {
    backgroundColor: "#FFE3C2",
  },
  glowAmber: {
    backgroundColor: "#FFD5C2",
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingTop: Platform.OS === "ios" ? 60 : 40,
    paddingBottom: 16,
    paddingHorizontal: 22,
  },
  title: {
    fontSize: 26,
    fontWeight: "900",
    color: "#0F172A",
  },
  subTitle: {
    fontSize: 12,
    fontWeight: "800",
    color: "#D97706",
    textTransform: "uppercase",
    letterSpacing: 1.2,
    marginTop: 4,
  },
  refreshBtn: {
    backgroundColor: "#FF9F43",
    width: 48,
    height: 48,
    borderRadius: 16,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  filterSection: {
    paddingHorizontal: 22,
    marginBottom: 14,
  },
  searchBar: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.18)",
    borderRadius: 16,
    paddingHorizontal: 14,
    height: 50,
    marginBottom: 12,
  },
  searchInput: {
    flex: 1,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "700",
  },
  tabRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    padding: 4,
    borderRadius: 14,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.12)",
  },
  tab: {
    flex: 1,
    paddingVertical: 8,
    alignItems: "center",
    borderRadius: 10,
  },
  tabActive: {
    backgroundColor: "#ffffff",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
    elevation: 2,
  },
  tabText: {
    fontSize: 11,
    fontWeight: "700",
    color: "#64748B",
  },
  tabTextActive: {
    color: "#D97706",
    fontWeight: "900",
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  listContent: {
    paddingHorizontal: 22,
    paddingBottom: 40,
    paddingTop: 10,
  },
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 80,
  },
  emptyText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 10,
  },
  proposalCard: {
    backgroundColor: "#ffffff",
    borderRadius: 22,
    padding: 18,
    marginBottom: 16,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
  },
  proposalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.06)",
    paddingBottom: 10,
    marginBottom: 12,
  },
  typeBadge: {
    backgroundColor: "rgba(255, 159, 67, 0.08)",
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 8,
  },
  typeBadgeText: {
    fontSize: 11,
    fontWeight: "800",
    color: "#D97706",
  },
  dateText: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "700",
  },
  targetMember: {
    fontSize: 13,
    color: "#64748B",
    fontWeight: "700",
    marginBottom: 8,
  },
  dataTitle: {
    fontSize: 11,
    fontWeight: "900",
    color: "#D97706",
    textTransform: "uppercase",
    letterSpacing: 0.8,
    marginBottom: 6,
  },
  dataContainer: {
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderRadius: 14,
    padding: 12,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.08)",
    marginBottom: 12,
  },
  dataDetail: {
    fontSize: 13,
    color: "#475569",
    fontWeight: "600",
    lineHeight: 20,
  },
  proposerRow: {
    borderTopWidth: 1,
    borderTopColor: "rgba(255, 159, 67, 0.06)",
    paddingTop: 10,
    marginBottom: 10,
  },
  proposerText: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "700",
  },
  noteBox: {
    backgroundColor: "rgba(34, 197, 94, 0.04)",
    borderWidth: 1,
    borderColor: "rgba(34, 197, 94, 0.12)",
    borderRadius: 10,
    padding: 10,
    marginBottom: 14,
  },
  noteText: {
    fontSize: 12,
    color: "#15803D",
    fontWeight: "600",
  },
  actionRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 8,
  },
  actionBtn: {
    flex: 1,
    flexDirection: "row",
    height: 44,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 1.5,
  },
  rejectBtn: {
    borderColor: "rgba(239, 68, 68, 0.25)",
    backgroundColor: "rgba(239, 68, 68, 0.03)",
    marginRight: 10,
  },
  approveBtn: {
    borderColor: "#FF9F43",
    backgroundColor: "#FF9F43",
  },
  actionBtnText: {
    fontSize: 13,
    fontWeight: "900",
    textTransform: "uppercase",
    letterSpacing: 0.5,
  },
  statusFooter: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    marginTop: 8,
    paddingTop: 10,
    borderTopWidth: 1,
    borderTopColor: "rgba(255, 159, 67, 0.05)",
  },
  statusFooterText: {
    fontSize: 12,
    fontWeight: "800",
    marginLeft: 6,
    textTransform: "uppercase",
    letterSpacing: 0.5,
  },
  
  // MODAL FORM LAYOUT
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.3)",
    justifyContent: "center",
    alignItems: "center",
    padding: 24,
  },
  modalContent: {
    backgroundColor: "#ffffff",
    borderRadius: 28,
    width: "100%",
    maxWidth: 320,
    padding: 24,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.15,
    shadowRadius: 16,
    elevation: 10,
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 16,
  },
  modalTitle: {
    fontSize: 14,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.2,
  },
  closeBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: "rgba(255, 159, 67, 0.05)",
    justifyContent: "center",
    alignItems: "center",
  },
  formGroup: {
    marginBottom: 16,
  },
  formLabel: {
    fontSize: 11,
    fontWeight: "800",
    color: "#D97706",
    marginBottom: 8,
    textTransform: "uppercase",
    letterSpacing: 1,
  },
  formInput: {
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    paddingHorizontal: 14,
    height: 80,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "700",
    textAlignVertical: "top",
    paddingTop: 10,
  },
  saveBtn: {
    backgroundColor: "#22C55E",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 8,
    shadowColor: "#22C55E",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
    elevation: 3,
  },
  saveBtnText: {
    color: "#ffffff",
    fontWeight: "900",
    fontSize: 14,
    textTransform: "uppercase",
    letterSpacing: 0.8,
  },
});
