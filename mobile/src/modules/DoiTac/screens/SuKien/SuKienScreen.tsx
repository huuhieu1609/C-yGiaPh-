import React, {
  useEffect,
  useState,
} from "react";

import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  ScrollView,
  StatusBar,
  Platform,
  Modal,
  TextInput,
  FlatList,
} from "react-native";

import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function SuKienScreen() {
  const [events, setEvents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Bộ lọc danh sách
  const [searchQuery, setSearchQuery] = useState("");
  const [typeFilter, setTypeFilter] = useState<"Tất cả" | "Giỗ tổ" | "Họp họ" | "Cưới hỏi" | "Tang lễ">("Tất cả");

  // State cho Modal Form (Thêm/Sửa)
  const [showFormModal, setShowFormModal] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // Trường thông tin Form
  const [tieuDe, setTieuDe] = useState("");
  const [loai, setLoai] = useState<"Giỗ tổ" | "Họp họ" | "Cưới hỏi" | "Tang lễ">("Họp họ");
  const [ngayToChuc, setNgayToChuc] = useState("");
  const [diaDiem, setDiaDiem] = useState("");
  const [noiDung, setNoiDung] = useState("");

  useEffect(() => {
    loadEvents();
  }, []);

  const loadEvents = async () => {
    try {
      setLoading(true);
      const res = await api.get("/su-kien/get-data");
      if (Array.isArray(res.data)) {
        setEvents(res.data);
      } else if (res.data && Array.isArray(res.data.data)) {
        setEvents(res.data.data);
      } else {
        setEvents([]);
      }
    } catch (error: any) {
      console.log(error);
      Alert.alert("Lỗi", "Không thể tải danh sách sự kiện");
    } finally {
      setLoading(false);
    }
  };

  // Khởi tạo Form Thêm mới
  const openAddForm = () => {
    setIsEditing(false);
    setEditingId(null);
    setTieuDe("");
    setLoai("Họp họ");
    
    // Đặt ngày mặc định là ngày hôm nay YYYY-MM-DD
    const today = new Date().toISOString().split("T")[0];
    setNgayToChuc(today);
    
    setDiaDiem("");
    setNoiDung("");
    setShowFormModal(true);
  };

  // Khởi tạo Form Sửa
  const openEditForm = (eventItem: any) => {
    setIsEditing(true);
    setEditingId(eventItem.id);
    setTieuDe(eventItem.tieu_de || "");
    setLoai(eventItem.loai || "Họp họ");
    setNgayToChuc(eventItem.ngay_to_chuc || "");
    setDiaDiem(eventItem.dia_diem || "");
    setNoiDung(eventItem.noi_dung || "");
    setShowFormModal(true);
  };

  // Xóa sự kiện
  const xoaSuKien = async (id: number) => {
    Alert.alert(
      "Xác nhận xóa",
      "Bạn có chắc chắn muốn xóa sự kiện dòng tộc này không?",
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const eventObj = events.find(e => e.id === id);
              const res = await api.post("/su-kien/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa sự kiện dòng tộc: ${eventObj ? eventObj.tieu_de : `ID #${id}`}`,
                });
                
                Alert.alert("Thành công", "Đã xóa sự kiện");
                loadEvents();
              } else {
                Alert.alert("Thất bại", res.data.message || "Lỗi không xác định");
              }
            } catch (error: any) {
              Alert.alert("Lỗi", error.response?.data?.message || "Không thể kết nối đến máy chủ");
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  // Lưu Form Thêm / Sửa
  const luuSuKien = async () => {
    if (!tieuDe.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập tiêu đề sự kiện");
      return;
    }

    if (!ngayToChuc.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập ngày tổ chức (YYYY-MM-DD)");
      return;
    }

    try {
      setLoading(true);

      const payload = {
        id: editingId,
        tieu_de: tieuDe,
        loai: loai,
        ngay_to_chuc: ngayToChuc,
        dia_diem: diaDiem || null,
        noi_dung: noiDung || null,
      };

      let res;
      if (isEditing) {
        res = await api.post("/su-kien/update", payload);
      } else {
        res = await api.post("/su-kien/create", payload);
      }

      if (res.data.status) {
        // Ghi nhật ký hoạt động
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditing 
            ? `Cập nhật sự kiện: ${tieuDe} (ID: ${editingId})` 
            : `Tạo sự kiện dòng tộc mới: ${tieuDe}`,
        });

        // Tự động tạo thông báo khi thêm sự kiện mới
        if (!isEditing) {
          await api.post("/thong-bao/create", {
            tieu_de: `Sự kiện mới: ${tieuDe}`,
            noi_dung: `Kính báo gia tộc, sự kiện "${tieuDe}" (${loai}) sẽ được tổ chức vào ngày ${ngayToChuc} tại địa điểm: ${diaDiem || "Nhà thờ tổ dòng họ"}. ${noiDung ? `Chi tiết: ${noiDung}` : ""}`,
          }).catch(err => console.log("Không thể tự động tạo thông báo sự kiện:", err));
        }

        Alert.alert("Thành công", isEditing ? "Cập nhật sự kiện thành công" : "Tạo mới sự kiện thành công!");
        setShowFormModal(false);
        loadEvents();
      } else {
        Alert.alert("Lỗi", res.data.message || "Lưu dữ liệu thất bại");
      }
    } catch (error: any) {
      Alert.alert("Lỗi", error.response?.data?.message || "Lưu thất bại từ hệ thống");
    } finally {
      setLoading(false);
    }
  };

  // Lấy biểu tượng emoji tương ứng với từng loại sự kiện
  const getEventEmoji = (eventLoai: string) => {
    switch (eventLoai) {
      case "Giỗ tổ":
        return "🪔";
      case "Họp họ":
        return "👥";
      case "Cưới hỏi":
        return "💍";
      case "Tang lễ":
        return "🕯️";
      default:
        return "📅";
    }
  };

  // Lọc danh sách sự kiện ở màn hình chính
  const filteredList = events.filter((e) => {
    const q = searchQuery.toLowerCase();
    const textMatch =
      e.tieu_de?.toLowerCase().includes(q) ||
      e.dia_diem?.toLowerCase().includes(q) ||
      e.noi_dung?.toLowerCase().includes(q);
    
    let typeMatch = true;
    if (typeFilter !== "Tất cả") {
      typeMatch = e.loai === typeFilter;
    }

    return textMatch && typeMatch;
  });

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      {/* BACKGROUND DECORATIVE SPHERES */}
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      {/* HEADER */}
      <View style={styles.header}>
        <View>
          <Text style={styles.title}>Quản Lý Sự Kiện</Text>
          <Text style={styles.subTitle}>Lễ nghi dòng họ phả hệ</Text>
        </View>
        <TouchableOpacity
          style={styles.addBtn}
          onPress={openAddForm}
          activeOpacity={0.8}
        >
          <Ionicons name="add-outline" size={24} color="#0c0e12" />
        </TouchableOpacity>
      </View>

      {/* BỘ LỌC VÀ TÌM KIẾM */}
      <View style={styles.filterSection}>
        {/* THANH TÌM KIẾM */}
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={20} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm theo tiêu đề, địa điểm..."
            placeholderTextColor="#94A3B8"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>

        {/* TẮP LỌC LOẠI SỰ KIỆN */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.filterScroll}
        >
          <TouchableOpacity
            style={[styles.tag, typeFilter === "Tất cả" && styles.tagActive]}
            onPress={() => setTypeFilter("Tất cả")}
          >
            <Text style={[styles.tagText, typeFilter === "Tất cả" && styles.tagTextActive]}>
              Tất cả
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, typeFilter === "Giỗ tổ" && styles.tagActive]}
            onPress={() => setTypeFilter("Giỗ tổ")}
          >
            <Text style={[styles.tagText, typeFilter === "Giỗ tổ" && styles.tagTextActive]}>
              Giỗ tổ 🪔
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, typeFilter === "Họp họ" && styles.tagActive]}
            onPress={() => setTypeFilter("Họp họ")}
          >
            <Text style={[styles.tagText, typeFilter === "Họp họ" && styles.tagTextActive]}>
              Họp họ 👥
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, typeFilter === "Cưới hỏi" && styles.tagActive]}
            onPress={() => setTypeFilter("Cưới hỏi")}
          >
            <Text style={[styles.tagText, typeFilter === "Cưới hỏi" && styles.tagTextActive]}>
              Cưới hỏi 💍
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, typeFilter === "Tang lễ" && styles.tagActive]}
            onPress={() => setTypeFilter("Tang lễ")}
          >
            <Text style={[styles.tagText, typeFilter === "Tang lễ" && styles.tagTextActive]}>
              Tang lễ 🕯️
            </Text>
          </TouchableOpacity>
        </ScrollView>
      </View>

      {/* DANH SÁCH SỰ KIỆN */}
      {loading && events.length === 0 ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#FF9F43" />
        </View>
      ) : (
        <FlatList
          data={filteredList}
          keyExtractor={(item) => item.id.toString()}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.listContent}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="calendar-outline" size={60} color="#CBD5E1" />
              <Text style={styles.emptyText}>Không tìm thấy sự kiện nào</Text>
            </View>
          }
          renderItem={({ item }) => {
            const dateObj = new Date(item.ngay_to_chuc);
            const formattedDate = !isNaN(dateObj.getTime())
              ? dateObj.toLocaleDateString("vi-VN", { day: "2-digit", month: "2-digit", year: "numeric" })
              : item.ngay_to_chuc;

            return (
              <View style={styles.eventCard}>
                <View style={styles.eventEmojiContainer}>
                  <Text style={styles.eventEmoji}>{getEventEmoji(item.loai)}</Text>
                </View>

                <View style={styles.eventDetails}>
                  <View style={styles.eventHeaderRow}>
                    <View style={styles.loaiBadge}>
                      <Text style={styles.loaiBadgeText}>{item.loai}</Text>
                    </View>
                    <Text style={styles.eventDate}>📅 {formattedDate}</Text>
                  </View>

                  <Text style={styles.eventTitle}>{item.tieu_de}</Text>
                  
                  {item.dia_diem && (
                    <Text style={styles.eventLocation}>
                      📍 {item.dia_diem}
                    </Text>
                  )}
                  
                  {item.noi_dung && (
                    <Text style={styles.eventContent} numberOfLines={2}>
                      {item.noi_dung}
                    </Text>
                  )}
                </View>

                {/* HÀNH ĐỘNG NHANH */}
                <View style={styles.actionColumn}>
                  <TouchableOpacity
                    style={[styles.actionBtn, { backgroundColor: "rgba(255, 159, 67, 0.1)" }]}
                    onPress={() => openEditForm(item)}
                    activeOpacity={0.7}
                  >
                    <Ionicons name="pencil-outline" size={16} color="#D97706" />
                  </TouchableOpacity>
                  
                  <TouchableOpacity
                    style={[styles.actionBtn, { backgroundColor: "rgba(239, 68, 68, 0.08)" }]}
                    onPress={() => xoaSuKien(item.id)}
                    activeOpacity={0.7}
                  >
                    <Ionicons name="trash-outline" size={16} color="#EF4444" />
                  </TouchableOpacity>
                </View>
              </View>
            );
          }}
        />
      )}

      {/* MODAL FORM THÊM / SỬA SỰ KIỆN */}
      <Modal
        visible={showFormModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowFormModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.formContainer}>
            <View style={styles.modalIndicator} />
            
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {isEditing ? "CẬP NHẬT SỰ KIỆN" : "TẠO MỚI SỰ KIỆN"}
              </Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowFormModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.formScroll}>
              
              {/* TIÊU ĐỀ */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Tiêu đề sự kiện *</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="Nhập tiêu đề sự kiện (ví dụ: Giỗ tổ ngành 2...)"
                  placeholderTextColor="#94A3B8"
                  value={tieuDe}
                  onChangeText={setTieuDe}
                />
              </View>

              {/* LOẠI SỰ KIỆN TABS */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Loại sự kiện *</Text>
                <View style={styles.typeSelectorGrid}>
                  {(["Giỗ tổ", "Họp họ", "Cưới hỏi", "Tang lễ"] as const).map((t) => (
                    <TouchableOpacity
                      key={t}
                      style={[styles.typeSelectBtn, loai === t && styles.typeSelectBtnActive]}
                      onPress={() => setLoai(t)}
                    >
                      <Text style={styles.typeEmoji}>{getEventEmoji(t)}</Text>
                      <Text style={[styles.typeSelectBtnText, loai === t && styles.typeSelectBtnTextActive]}>
                        {t}
                      </Text>
                    </TouchableOpacity>
                  ))}
                </View>
              </View>

              {/* NGÀY TỔ CHỨC & ĐỊA ĐIỂM */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Ngày tổ chức (YYYY-MM-DD) *</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="YYYY-MM-DD"
                  placeholderTextColor="#94A3B8"
                  value={ngayToChuc}
                  onChangeText={setNgayToChuc}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Địa điểm tổ chức</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="Nhà thờ họ, từ đường, tư gia..."
                  placeholderTextColor="#94A3B8"
                  value={diaDiem}
                  onChangeText={setDiaDiem}
                />
              </View>

              {/* NỘI DUNG / MÔ TẢ CHI TIẾT */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Mô tả chi tiết sự kiện</Text>
                <TextInput
                  style={[styles.formInput, { height: 110, textAlignVertical: "top", paddingTop: 10 }]}
                  placeholder="Nhập chương trình tổ chức sự kiện, thời gian, lưu ý..."
                  placeholderTextColor="#94A3B8"
                  value={noiDung}
                  onChangeText={setNoiDung}
                  multiline={true}
                />
              </View>

              {/* NÚT LƯU */}
              <TouchableOpacity
                style={styles.saveBtn}
                onPress={luuSuKien}
                activeOpacity={0.8}
              >
                <Text style={styles.saveBtnText}>{isEditing ? "Cập Nhật" : "Tạo Mới"}</Text>
                <Ionicons name="save-outline" size={18} color="#0c0e12" style={{ marginLeft: 8 }} />
              </TouchableOpacity>

            </ScrollView>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2", // Nền cam đào sáng nhẹ
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
  
  // HEADER
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
    letterSpacing: 0.5,
  },
  subTitle: {
    fontSize: 12,
    fontWeight: "800",
    color: "#D97706",
    textTransform: "uppercase",
    letterSpacing: 1.2,
    marginTop: 4,
  },
  addBtn: {
    backgroundColor: "#FF9F43",
    width: 48,
    height: 48,
    borderRadius: 16,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.22,
    shadowRadius: 8,
    elevation: 4,
  },

  // BỘ LỌC VÀ TÌM KIẾM
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
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  searchInput: {
    flex: 1,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "700",
  },
  filterScroll: {
    paddingVertical: 4,
    alignItems: "center",
  },
  tag: {
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 12,
    paddingHorizontal: 14,
    paddingVertical: 8,
    marginRight: 8,
  },
  tagActive: {
    backgroundColor: "rgba(255, 159, 67, 0.08)",
    borderColor: "#FF9F43",
  },
  tagText: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "700",
  },
  tagTextActive: {
    color: "#D97706",
    fontWeight: "900",
  },

  // DANH SÁCH CARD SỰ KIỆN
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  listContent: {
    paddingHorizontal: 22,
    paddingBottom: 40,
  },
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 10,
    textAlign: "center",
  },
  eventCard: {
    flexDirection: "row",
    backgroundColor: "#ffffff",
    borderRadius: 22,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
  },
  eventEmojiContainer: {
    width: 52,
    height: 52,
    borderRadius: 16,
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    justifyContent: "center",
    alignItems: "center",
    marginRight: 14,
  },
  eventEmoji: {
    fontSize: 24,
  },
  eventDetails: {
    flex: 1,
  },
  eventHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 6,
  },
  loaiBadge: {
    backgroundColor: "rgba(217, 119, 6, 0.08)",
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  loaiBadgeText: {
    fontSize: 10,
    fontWeight: "800",
    color: "#D97706",
    textTransform: "uppercase",
  },
  eventDate: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "700",
  },
  eventTitle: {
    fontSize: 16,
    fontWeight: "800",
    color: "#1E293B",
    lineHeight: 22,
  },
  eventLocation: {
    fontSize: 12,
    color: "#D97706",
    fontWeight: "700",
    marginTop: 4,
  },
  eventContent: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 6,
    lineHeight: 18,
  },
  actionColumn: {
    flexDirection: "column",
    justifyContent: "flex-start",
    alignItems: "center",
    marginLeft: 10,
  },
  actionBtn: {
    width: 32,
    height: 32,
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
    marginVertical: 4,
  },

  // MODAL FORM LAYOUT
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.4)",
    justifyContent: "flex-end",
  },
  formContainer: {
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    height: "82%",
    paddingHorizontal: 22,
    paddingTop: 12,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
  },
  modalIndicator: {
    width: 44,
    height: 4,
    backgroundColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 2,
    alignSelf: "center",
    marginBottom: 16,
  },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 16,
  },
  modalTitle: {
    fontSize: 15,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.2,
  },
  closeBtn: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    justifyContent: "center",
    alignItems: "center",
  },
  formScroll: {
    paddingBottom: 40,
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
    height: 48,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "700",
  },
  typeSelectorGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
  },
  typeSelectBtn: {
    width: "48%",
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    paddingVertical: 10,
    marginBottom: 10,
  },
  typeSelectBtnActive: {
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    borderColor: "#FF9F43",
  },
  typeEmoji: {
    fontSize: 18,
    marginRight: 6,
  },
  typeSelectBtnText: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "700",
  },
  typeSelectBtnTextActive: {
    color: "#D97706",
    fontWeight: "900",
  },
  saveBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 20,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.2,
    shadowRadius: 10,
    elevation: 4,
  },
  saveBtnText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 16,
    textTransform: "uppercase",
    letterSpacing: 0.8,
  },
});
