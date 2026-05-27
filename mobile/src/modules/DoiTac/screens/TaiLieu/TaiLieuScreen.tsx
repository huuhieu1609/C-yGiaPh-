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
  Linking,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import { router } from "expo-router";
import api from "../../../../shared/services/api";

const { width: SCREEN_W } = Dimensions.get("window");

export default function TaiLieuScreen() {
  const [documents, setDocuments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  // Form Modal States
  const [showModal, setShowModal] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editDocId, setEditDocId] = useState<number | null>(null);
  const [formTieuDe, setFormTieuDe] = useState("");
  const [formFilePath, setFormFilePath] = useState("");
  const [formMoTa, setFormMoTa] = useState("");

  useEffect(() => {
    loadDocuments();
  }, []);

  const loadDocuments = async () => {
    try {
      setLoading(true);
      const res = await api.get("/tai-lieu/get-data");
      if (res.data.status) {
        const sorted = (res.data.data || []).sort((a: any, b: any) => b.id - a.id);
        setDocuments(sorted);
      }
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };

  const openCreateDoc = () => {
    setIsEditing(false);
    setEditDocId(null);
    setFormTieuDe("");
    setFormFilePath("");
    setFormMoTa("");
    setShowModal(true);
  };

  const openEditDoc = (item: any) => {
    setIsEditing(true);
    setEditDocId(item.id);
    setFormTieuDe(item.tieu_de || "");
    setFormFilePath(item.file_path || "");
    setFormMoTa(item.mo_ta || "");
    setShowModal(true);
  };

  const handleSaveDoc = async () => {
    if (!formTieuDe.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập tiêu đề tài liệu.");
      return;
    }
    try {
      setLoading(true);
      const payload = {
        id: editDocId,
        tieu_de: formTieuDe,
        file_path: formFilePath,
        mo_ta: formMoTa,
      };

      const url = isEditing ? "/tai-lieu/update" : "/tai-lieu/create";
      const res = await api.post(url, payload);

      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditing
            ? `Cập nhật tài liệu phả hệ: ${formTieuDe}`
            : `Thêm tài liệu phả hệ mới: ${formTieuDe}`,
        }).catch(err => console.log("Ghi nhật ký thất bại:", err));

        Alert.alert("Thành công", isEditing ? "Cập nhật tài liệu thành công!" : "Tạo tài liệu thành công!");
        setShowModal(false);
        loadDocuments();
      } else {
        Alert.alert("Thất bại", res.data.message || "Không thể lưu tài liệu.");
      }
    } catch (err) {
      Alert.alert("Lỗi", "Không thể kết nối đến máy chủ.");
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteDoc = (id: number, title: string) => {
    Alert.alert(
      "Xác nhận xóa",
      `Bạn có chắc muốn xóa tài liệu phả hệ "${title}" không?`,
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/tai-lieu/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động liên kết
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa tài liệu phả hệ: ${title}`,
                }).catch(err => console.log("Ghi nhật ký thất bại:", err));

                Alert.alert("Thành công", "Đã xóa tài liệu phả hệ!");
                loadDocuments();
              } else {
                Alert.alert("Thất bại", res.data.message || "Không thể xóa.");
              }
            } catch (err) {
              Alert.alert("Lỗi", "Không thể xóa tài liệu.");
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  const handleViewFile = (url: string) => {
    if (!url) {
      Alert.alert("Lỗi", "Tài liệu này không có đường dẫn liên kết.");
      return;
    }
    Linking.canOpenURL(url).then((supported) => {
      if (supported) {
        Linking.openURL(url);
      } else {
        // Fallback open direct url
        Linking.openURL(url).catch(() => {
          Alert.alert("Lỗi", "Không thể mở tệp tài liệu này.");
        });
      }
    });
  };

  const filteredDocs = documents.filter((item) => {
    const q = searchQuery.toLowerCase().trim();
    return !q || item.tieu_de?.toLowerCase().includes(q) || item.mo_ta?.toLowerCase().includes(q);
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
          <Text style={styles.title}>Quản Lý Tài Liệu</Text>
          <Text style={styles.subTitle}>Văn bản cổ truyền gia tộc</Text>
        </View>
        <TouchableOpacity style={styles.addBtn} onPress={openCreateDoc}>
          <Ionicons name="add-circle" size={24} color="#22C55E" />
        </TouchableOpacity>
      </View>

      {/* SEARCH */}
      <View style={styles.filterSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm kiếm tiêu đề, nội dung tài liệu..."
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
          data={filteredDocs}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="document-text-outline" size={56} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có tài liệu dòng họ nào</Text>
            </View>
          }
          renderItem={({ item }) => (
            <View style={styles.card}>
              <View style={styles.cardInfo}>
                <Text style={styles.cardTitle}>{item.tieu_de}</Text>
                <Text style={styles.cardDesc} numberOfLines={2}>{item.mo_ta || "Không có mô tả chi tiết."}</Text>
                {item.file_path && (
                  <TouchableOpacity style={styles.fileLink} onPress={() => handleViewFile(item.file_path)}>
                    <Ionicons name="link-outline" size={13} color="#D97706" />
                    <Text style={styles.fileLinkText} numberOfLines={1}> {item.file_path}</Text>
                  </TouchableOpacity>
                )}
              </View>
              <View style={styles.cardActions}>
                <TouchableOpacity style={styles.actionBtn} onPress={() => openEditDoc(item)}>
                  <Ionicons name="pencil" size={16} color="#D97706" />
                </TouchableOpacity>
                <TouchableOpacity style={[styles.actionBtn, { marginLeft: 8 }]} onPress={() => handleDeleteDoc(item.id, item.tieu_de)}>
                  <Ionicons name="trash" size={16} color="#EF4444" />
                </TouchableOpacity>
              </View>
            </View>
          )}
        />
      )}

      {/* DOCUMENT FORM MODAL */}
      <Modal visible={showModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.formContainer}>
            <View style={styles.formHeader}>
              <Text style={styles.formTitle}>{isEditing ? "Cập nhật tài liệu" : "Thêm tài liệu mới"}</Text>
              <TouchableOpacity onPress={() => setShowModal(false)}>
                <Ionicons name="close-circle" size={24} color="#94A3B8" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} style={{ paddingHorizontal: 16 }}>
              <Text style={styles.formLabel}>Tiêu đề tài liệu <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: Sách phả hệ dòng họ Nguyễn"
                placeholderTextColor="#94A3B8"
                value={formTieuDe}
                onChangeText={setFormTieuDe}
              />

              <Text style={styles.formLabel}>Đường dẫn tệp / URL lưu trữ</Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: https://drive.google.com/..."
                placeholderTextColor="#94A3B8"
                value={formFilePath}
                onChangeText={setFormFilePath}
              />

              <Text style={styles.formLabel}>Mô tả chi tiết tài liệu</Text>
              <TextInput
                style={[styles.formInput, { height: 75, textAlignVertical: "top", paddingTop: 8 }]}
                placeholder="Mô tả nội dung phả hệ, nguồn gốc tài liệu, niên đại..."
                placeholderTextColor="#94A3B8"
                multiline
                numberOfLines={3}
                value={formMoTa}
                onChangeText={setFormMoTa}
              />

              <View style={{ height: 16 }} />
            </ScrollView>

            <View style={styles.formFooter}>
              <TouchableOpacity style={styles.btnCancel} onPress={() => setShowModal(false)}>
                <Text style={styles.btnCancelText}>Hủy bỏ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnSave} onPress={handleSaveDoc}>
                <Text style={styles.btnSaveText}>Lưu tài liệu</Text>
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
  cardInfo: { flex: 1 },
  cardTitle: { fontSize: 15, fontWeight: "800", color: "#1E293B" },
  cardDesc: { fontSize: 11, color: "#64748B", fontWeight: "600", marginTop: 4, lineHeight: 16 },
  fileLink: { flexDirection: "row", alignItems: "center", marginTop: 6, backgroundColor: "rgba(255, 159, 67, 0.05)", alignSelf: "flex-start", paddingHorizontal: 8, paddingVertical: 3, borderRadius: 6 },
  fileLinkText: { fontSize: 10, color: "#D97706", fontWeight: "700", maxWidth: 180 },
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
