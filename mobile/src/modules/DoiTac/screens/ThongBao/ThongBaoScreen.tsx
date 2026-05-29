import React, { useEffect, useState } from "react";
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
  ScrollView,
  Dimensions,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import api from "../../../../shared/services/api";

const { width: SCREEN_W } = Dimensions.get("window");

export default function ThongBaoScreen() {
  const [notifications, setNotifications] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  // Form Modal States
  const [showModal, setShowModal] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editId, setEditId] = useState<number | null>(null);
  const [formTieuDe, setFormTieuDe] = useState("");
  const [formNoiDung, setFormNoiDung] = useState("");

  useEffect(() => {
    loadNotifications();
  }, []);

  const loadNotifications = async () => {
    try {
      setLoading(true);
      const res = await api.get("/thong-bao/get-data");
      if (res.data.status) {
        const sorted = (res.data.data || []).sort((a: any, b: any) => b.id - a.id);
        setNotifications(sorted);
      }
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };

  const openCreateModal = () => {
    setIsEditing(false);
    setEditId(null);
    setFormTieuDe("");
    setFormNoiDung("");
    setShowModal(true);
  };

  const openEditModal = (item: any) => {
    setIsEditing(true);
    setEditId(item.id);
    setFormTieuDe(item.tieu_de || "");
    setFormNoiDung(item.noi_dung || "");
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!formTieuDe.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập tiêu đề thông báo.");
      return;
    }
    if (!formNoiDung.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập nội dung thông báo.");
      return;
    }
    try {
      setLoading(true);
      const payload = {
        id: editId,
        tieu_de: formTieuDe,
        noi_dung: formNoiDung,
      };

      const url = isEditing ? "/thong-bao/update" : "/thong-bao/create";
      const res = await api.post(url, payload);

      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết để tự động hiển thị trong Lịch Sử Thao Tác
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditing
            ? `Cập nhật thông báo gia tộc: ${formTieuDe}`
            : `Tạo thông báo gia tộc mới: ${formTieuDe}`,
        }).catch(err => console.log("Ghi nhật ký thất bại:", err));

        Alert.alert("Thành công", isEditing ? "Cập nhật thông báo thành công!" : "Tạo thông báo thành công!");
        setShowModal(false);
        loadNotifications();
      } else {
        Alert.alert("Thất bại", res.data.message || "Không thể lưu thông báo.");
      }
    } catch (err) {
      Alert.alert("Lỗi", "Không thể kết nối đến máy chủ.");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = (id: number, title: string) => {
    Alert.alert(
      "Xác nhận xóa",
      `Bạn có chắc muốn xóa thông báo "${title}" này không?`,
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/thong-bao/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động liên kết để tự động hiển thị trong Lịch Sử Thao Tác
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa thông báo gia tộc: ${title}`,
                }).catch(err => console.log("Ghi nhật ký thất bại:", err));

                Alert.alert("Thành công", "Đã xóa thông báo!");
                loadNotifications();
              } else {
                Alert.alert("Thất bại", res.data.message || "Không thể xóa.");
              }
            } catch (err) {
              Alert.alert("Lỗi", "Không thể kết nối máy chủ.");
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  const filteredNotifications = notifications.filter((item) => {
    const q = searchQuery.toLowerCase().trim();
    return !q || item.tieu_de?.toLowerCase().includes(q) || item.noi_dung?.toLowerCase().includes(q);
  });

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      {/* HEADER */}
      <View style={styles.header}>
        <TouchableOpacity style={styles.backBtn} onPress={() => router.back()}>
          <Ionicons name="arrow-back-outline" size={22} color="#0F172A" />
        </TouchableOpacity>
        <View style={styles.headerTitleWrap}>
          <Text style={styles.title}>Quản Lý Thông Báo</Text>
          <Text style={styles.subTitle}>Tin tức & thông tin dòng họ</Text>
        </View>
        <TouchableOpacity style={styles.addBtn} onPress={openCreateModal}>
          <Ionicons name="add-circle" size={24} color="#22C55E" />
        </TouchableOpacity>
      </View>

      {/* SEARCH */}
      <View style={styles.filterSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm kiếm thông báo..."
            placeholderTextColor="#94A3B8"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>
      </View>

      {/* CONTENT LIST */}
      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#FF9F43" />
        </View>
      ) : (
        <FlatList
          data={filteredNotifications}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="notifications-outline" size={56} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có thông báo dòng họ nào</Text>
            </View>
          }
          renderItem={({ item }) => {
            const dateStr = item.created_at
              ? new Date(item.created_at).toLocaleDateString("vi-VN", {
                  day: "2-digit",
                  month: "2-digit",
                  year: "numeric",
                  hour: "2-digit",
                  minute: "2-digit",
                })
              : "Vừa xong";
            return (
              <View style={styles.card}>
                <View style={styles.cardInfo}>
                  <View style={styles.cardHeaderRow}>
                    <Ionicons name="megaphone" size={16} color="#D97706" style={{ marginRight: 6 }} />
                    <Text style={styles.cardTitle} numberOfLines={1}>{item.tieu_de}</Text>
                  </View>
                  <Text style={styles.cardDesc}>{item.noi_dung}</Text>
                  <Text style={styles.cardTime}>🕒 {dateStr}</Text>
                </View>
                <View style={styles.cardActions}>
                  <TouchableOpacity style={styles.actionBtn} onPress={() => openEditModal(item)}>
                    <Ionicons name="pencil" size={16} color="#D97706" />
                  </TouchableOpacity>
                  <TouchableOpacity style={[styles.actionBtn, { marginLeft: 8 }]} onPress={() => handleDelete(item.id, item.tieu_de)}>
                    <Ionicons name="trash" size={16} color="#EF4444" />
                  </TouchableOpacity>
                </View>
              </View>
            );
          }}
        />
      )}

      {/* FORM MODAL */}
      <Modal visible={showModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.formContainer}>
            <View style={styles.formHeader}>
              <Text style={styles.formTitle}>{isEditing ? "Cập nhật thông báo" : "Thêm thông báo mới"}</Text>
              <TouchableOpacity onPress={() => setShowModal(false)}>
                <Ionicons name="close-circle" size={24} color="#94A3B8" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} style={{ paddingHorizontal: 16 }}>
              <Text style={styles.formLabel}>Tiêu đề thông báo <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: Họp hội đồng gia tộc cuối năm"
                placeholderTextColor="#94A3B8"
                value={formTieuDe}
                onChangeText={setFormTieuDe}
              />

              <Text style={styles.formLabel}>Nội dung chi tiết <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={[styles.formInput, { height: 100, textAlignVertical: "top", paddingTop: 8 }]}
                placeholder="Nhập nội dung thông báo gửi đến các thành viên..."
                placeholderTextColor="#94A3B8"
                multiline
                numberOfLines={4}
                value={formNoiDung}
                onChangeText={setFormNoiDung}
              />

              <View style={{ height: 16 }} />
            </ScrollView>

            <View style={styles.formFooter}>
              <TouchableOpacity style={styles.btnCancel} onPress={() => setShowModal(false)}>
                <Text style={styles.btnCancelText}>Hủy bỏ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnSave} onPress={handleSave}>
                <Text style={styles.btnSaveText}>Gửi thông báo</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: "#FFF8F2" },
  glowSphere: { position: "absolute", width: 320, height: 320, borderRadius: 160, opacity: 0.1 },
  glowGold: { backgroundColor: "#FFE3C2" },
  glowAmber: { backgroundColor: "#FFD5C2" },
  
  // Header
  header: {
    flexDirection: "row", alignItems: "center",
    paddingTop: Platform.OS === "ios" ? 60 : 40,
    paddingBottom: 12, paddingHorizontal: 22,
  },
  backBtn: {
    width: 38, height: 38, borderRadius: 12,
    backgroundColor: "rgba(255, 159, 67, 0.07)",
    justifyContent: "center", alignItems: "center",
  },
  headerTitleWrap: { flex: 1, marginLeft: 12 },
  title: { fontSize: 22, fontWeight: "900", color: "#0F172A" },
  subTitle: { fontSize: 11, fontWeight: "800", color: "#D97706", textTransform: "uppercase", letterSpacing: 0.8, marginTop: 2 },
  addBtn: { width: 38, height: 38, justifyContent: "center", alignItems: "center" },

  // Search
  filterSection: { paddingHorizontal: 22, marginBottom: 14 },
  searchBar: {
    flexDirection: "row", alignItems: "center", backgroundColor: "#ffffff",
    borderWidth: 1.5, borderColor: "rgba(255, 159, 67, 0.14)",
    borderRadius: 14, paddingHorizontal: 12, height: 46,
  },
  searchInput: { flex: 1, color: "#1E293B", fontSize: 13, fontWeight: "600" },
  loadingContainer: { flex: 1, justifyContent: "center", alignItems: "center" },
  listContent: { paddingHorizontal: 22, paddingBottom: 40 },
  emptyContainer: { alignItems: "center", justifyContent: "center", paddingVertical: 80, gap: 10 },
  emptyText: { fontSize: 13, color: "#94A3B8", fontWeight: "700" },

  // Card
  card: {
    backgroundColor: "#ffffff", borderRadius: 20, padding: 16, marginBottom: 12,
    borderWidth: 1.5, borderColor: "rgba(255, 159, 67, 0.08)",
    flexDirection: "row", alignItems: "center",
    shadowColor: "#FF9F43", shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.03, shadowRadius: 6, elevation: 2,
  },
  cardInfo: { flex: 1, marginRight: 8 },
  cardHeaderRow: { flexDirection: "row", alignItems: "center", marginBottom: 6 },
  cardTitle: { fontSize: 15, fontWeight: "800", color: "#1E293B", flex: 1 },
  cardDesc: { fontSize: 12, color: "#475569", fontWeight: "600", lineHeight: 18 },
  cardTime: { fontSize: 10, color: "#94A3B8", fontWeight: "600", marginTop: 8 },
  cardActions: { flexDirection: "row", alignItems: "center" },
  actionBtn: {
    width: 32, height: 32, borderRadius: 10,
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    justifyContent: "center", alignItems: "center",
  },

  // Modal form
  modalOverlay: { flex: 1, backgroundColor: "rgba(12,14,18,0.55)", justifyContent: "center", alignItems: "center" },
  formContainer: {
    width: SCREEN_W * 0.9, backgroundColor: "#ffffff", borderRadius: 22, paddingVertical: 18,
    borderWidth: 1.5, borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#000", shadowOffset: { width: 0, height: 6 }, shadowOpacity: 0.2, shadowRadius: 12, elevation: 10,
  },
  formHeader: { flexDirection: "row", justifyContent: "space-between", alignItems: "center", paddingHorizontal: 16, marginBottom: 14 },
  formTitle: { fontSize: 16, fontWeight: "800", color: "#0F172A" },
  formLabel: { fontSize: 10, fontWeight: "800", color: "#D97706", marginTop: 10, marginBottom: 4, textTransform: "uppercase", letterSpacing: 0.5 },
  formInput: {
    backgroundColor: "rgba(255, 159, 67, 0.03)", borderWidth: 1.5, borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 12, paddingHorizontal: 12, height: 42, color: "#1E293B", fontSize: 13, fontWeight: "600",
  },
  formFooter: {
    flexDirection: "row", borderTopWidth: 1, borderTopColor: "rgba(255, 159, 67, 0.08)",
    paddingTop: 12, paddingHorizontal: 16, marginTop: 14, gap: 10,
  },
  btnCancel: { flex: 1, backgroundColor: "#F1F5F9", height: 42, borderRadius: 12, alignItems: "center", justifyContent: "center" },
  btnCancelText: { fontSize: 13, fontWeight: "700", color: "#64748B" },
  btnSave: { flex: 2, backgroundColor: "#FF9F43", height: 42, borderRadius: 12, alignItems: "center", justifyContent: "center" },
  btnSaveText: { fontSize: 13, fontWeight: "800", color: "#0c0e12" },
});
