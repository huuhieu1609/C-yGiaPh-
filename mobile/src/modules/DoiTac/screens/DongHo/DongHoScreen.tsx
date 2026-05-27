import React, { useEffect, useState, useCallback } from "react";
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

export default function DongHoScreen() {
  const [activeTab, setActiveTab] = useState<"chi_nhanh" | "doi">("chi_nhanh");
  
  // Data lists
  const [branches, setBranches] = useState<any[]>([]);
  const [generations, setGenerations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  // Modals
  const [showBranchModal, setShowBranchModal] = useState(false);
  const [isEditingBranch, setIsEditingBranch] = useState(false);
  const [editBranchId, setEditBranchId] = useState<number | null>(null);
  const [formTenChi, setFormTenChi] = useState("");
  const [formDiaChiChi, setFormDiaChiChi] = useState("");

  const [showGenModal, setShowGenModal] = useState(false);
  const [isEditingGen, setIsEditingGen] = useState(false);
  const [editGenId, setEditGenId] = useState<number | null>(null);
  const [formTenDoi, setFormTenDoi] = useState("");
  const [formChiNhanhId, setFormChiNhanhId] = useState<number | null>(null);
  const [showBranchPicker, setShowBranchPicker] = useState(false);

  useEffect(() => {
    loadData();
  }, [activeTab]);

  const loadData = async () => {
    try {
      setLoading(true);
      if (activeTab === "chi_nhanh") {
        const res = await api.get("/chi-nhanh/get-data");
        if (res.data.status) {
          setBranches(res.data.data || []);
        }
      } else {
        // Tải cả đời và chi nhánh để liên kết hiển thị
        const [genRes, branchRes] = await Promise.all([
          api.get("/doi-toc-ho/get-data"),
          api.get("/chi-nhanh/get-data"),
        ]);
        if (genRes.data.status) {
          setGenerations(genRes.data.data || []);
        }
        if (branchRes.data.status) {
          setBranches(branchRes.data.data || []);
        }
      }
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };

  // ─── BRANCH CRUD ────────────────────────────────────────────────────────────
  const openCreateBranch = () => {
    setIsEditingBranch(false);
    setEditBranchId(null);
    setFormTenChi("");
    setFormDiaChiChi("");
    setShowBranchModal(true);
  };

  const openEditBranch = (item: any) => {
    setIsEditingBranch(true);
    setEditBranchId(item.id);
    setFormTenChi(item.ten_chi || "");
    setFormDiaChiChi(item.dia_chi || "");
    setShowBranchModal(true);
  };

  const handleSaveBranch = async () => {
    if (!formTenChi.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập tên chi nhánh.");
      return;
    }
    try {
      setLoading(true);
      const payload = {
        id: editBranchId,
        ten_chi: formTenChi,
        dia_chi: formDiaChiChi,
      };
      const url = isEditingBranch ? "/chi-nhanh/update" : "/chi-nhanh/create";
      const res = await api.post(url, payload);

      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditingBranch
            ? `Cập nhật chi nhánh dòng họ: ${formTenChi}`
            : `Thêm chi nhánh dòng họ mới: ${formTenChi}`,
        }).catch(err => console.log("Ghi nhật ký thất bại:", err));

        Alert.alert("Thành công", isEditingBranch ? "Cập nhật chi nhánh thành công!" : "Tạo chi nhánh thành công!");
        setShowBranchModal(false);
        loadData();
      } else {
        Alert.alert("Thất bại", res.data.message || "Không thể lưu.");
      }
    } catch (err) {
      Alert.alert("Lỗi", "Không thể kết nối máy chủ.");
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteBranch = (id: number, name: string) => {
    Alert.alert(
      "Xác nhận xóa",
      `Bạn có chắc muốn xóa chi nhánh "${name}" không? Mọi thông tin liên quan sẽ bị ảnh hưởng.`,
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/chi-nhanh/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động liên kết
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa chi nhánh dòng họ: ${name}`,
                }).catch(err => console.log("Ghi nhật ký thất bại:", err));

                Alert.alert("Thành công", "Đã xóa chi nhánh!");
                loadData();
              } else {
                Alert.alert("Thất bại", res.data.message || "Không thể xóa.");
              }
            } catch (err) {
              Alert.alert("Lỗi", "Lỗi máy chủ.");
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  // ─── GENERATION CRUD ────────────────────────────────────────────────────────
  const openCreateGen = () => {
    setIsEditingGen(false);
    setEditGenId(null);
    setFormTenDoi("");
    setFormChiNhanhId(branches.length > 0 ? branches[0].id : null);
    setShowGenModal(true);
  };

  const openEditGen = (item: any) => {
    setIsEditingGen(true);
    setEditGenId(item.id);
    setFormTenDoi(item.ten_doi || "");
    setFormChiNhanhId(item.chi_nhanh_id);
    setShowGenModal(true);
  };

  const handleSaveGen = async () => {
    if (!formTenDoi.trim()) {
      Alert.alert("Thiếu thông tin", "Vui lòng nhập tên đời.");
      return;
    }
    if (!formChiNhanhId) {
      Alert.alert("Thiếu thông tin", "Vui lòng chọn chi nhánh liên kết.");
      return;
    }
    try {
      setLoading(true);
      const payload = {
        id: editGenId,
        ten_doi: formTenDoi,
        chi_nhanh_id: formChiNhanhId,
      };
      const url = isEditingGen ? "/doi-toc-ho/update" : "/doi-toc-ho/create";
      const res = await api.post(url, payload);

      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditingGen
            ? `Cập nhật đời gia tộc: ${formTenDoi}`
            : `Tạo đời gia tộc mới: ${formTenDoi}`,
        }).catch(err => console.log("Ghi nhật ký thất bại:", err));

        Alert.alert("Thành công", isEditingGen ? "Cập nhật đời thành công!" : "Tạo đời gia tộc thành công!");
        setShowGenModal(false);
        loadData();
      } else {
        Alert.alert("Thất bại", res.data.message || "Không thể lưu.");
      }
    } catch (err) {
      Alert.alert("Lỗi", "Lỗi kết nối.");
    } finally {
      setLoading(false);
    }
  };

  const handleDeleteGen = (id: number, name: string) => {
    Alert.alert(
      "Xác nhận xóa",
      `Bạn có chắc muốn xóa đời "${name}" này khỏi dòng họ không?`,
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/doi-toc-ho/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động liên kết
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa đời gia tộc: ${name}`,
                }).catch(err => console.log("Ghi nhật ký thất bại:", err));

                Alert.alert("Thành công", "Đã xóa đời gia tộc!");
                loadData();
              } else {
                Alert.alert("Thất bại", res.data.message || "Không thể xóa.");
              }
            } catch (err) {
              Alert.alert("Lỗi", "Lỗi máy chủ.");
            } finally {
              setLoading(false);
            }
          },
        },
      ]
    );
  };

  // ─── SEARCH FILTER ──────────────────────────────────────────────────────────
  const filteredBranches = branches.filter((item) => {
    const q = searchQuery.toLowerCase().trim();
    return !q || item.ten_chi?.toLowerCase().includes(q) || item.dia_chi?.toLowerCase().includes(q);
  });

  const filteredGenerations = generations.filter((item) => {
    const q = searchQuery.toLowerCase().trim();
    return !q || item.ten_doi?.toLowerCase().includes(q) || item.chi_nhanh?.ten_chi?.toLowerCase().includes(q);
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
          <Text style={styles.title}>Quản Lý Dòng Họ</Text>
          <Text style={styles.subTitle}>Chi nhánh & đời dòng tộc</Text>
        </View>
        <TouchableOpacity
          style={styles.addBtn}
          onPress={activeTab === "chi_nhanh" ? openCreateBranch : openCreateGen}
        >
          <Ionicons name="add-circle" size={24} color="#22C55E" />
        </TouchableOpacity>
      </View>

      {/* TAB SELECTOR */}
      <View style={styles.tabBar}>
        <TouchableOpacity
          style={[styles.tabItem, activeTab === "chi_nhanh" && styles.tabItemActive]}
          onPress={() => { setSearchQuery(""); setActiveTab("chi_nhanh"); }}
        >
          <Ionicons name="git-network-outline" size={16} color={activeTab === "chi_nhanh" ? "#D97706" : "#64748B"} />
          <Text style={[styles.tabText, activeTab === "chi_nhanh" && styles.tabTextActive]}>Chi Nhánh ({branches.length})</Text>
        </TouchableOpacity>
        <TouchableOpacity
          style={[styles.tabItem, activeTab === "doi" && styles.tabItemActive]}
          onPress={() => { setSearchQuery(""); setActiveTab("doi"); }}
        >
          <Ionicons name="people-outline" size={16} color={activeTab === "doi" ? "#D97706" : "#64748B"} />
          <Text style={[styles.tabText, activeTab === "doi" && styles.tabTextActive]}>Đời Gia Tộc ({generations.length})</Text>
        </TouchableOpacity>
      </View>

      {/* SEARCH */}
      <View style={styles.filterSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={18} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder={activeTab === "chi_nhanh" ? "Tìm kiếm chi nhánh, địa chỉ..." : "Tìm kiếm đời, chi nhánh liên kết..."}
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
      ) : activeTab === "chi_nhanh" ? (
        <FlatList
          data={filteredBranches}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="git-network-outline" size={56} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có chi nhánh dòng họ nào</Text>
            </View>
          }
          renderItem={({ item }) => (
            <View style={styles.card}>
              <View style={styles.cardInfo}>
                <Text style={styles.cardTitle}>{item.ten_chi}</Text>
                <Text style={styles.cardSub}>📍 {item.dia_chi || "Chưa cập nhật địa chỉ"}</Text>
              </View>
              <View style={styles.cardActions}>
                <TouchableOpacity style={styles.actionBtn} onPress={() => openEditBranch(item)}>
                  <Ionicons name="pencil" size={16} color="#D97706" />
                </TouchableOpacity>
                <TouchableOpacity style={[styles.actionBtn, { marginLeft: 8 }]} onPress={() => handleDeleteBranch(item.id, item.ten_chi)}>
                  <Ionicons name="trash" size={16} color="#EF4444" />
                </TouchableOpacity>
              </View>
            </View>
          )}
        />
      ) : (
        <FlatList
          data={filteredGenerations}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="people-outline" size={56} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có đời gia tộc nào được tạo</Text>
            </View>
          }
          renderItem={({ item }) => (
            <View style={styles.card}>
              <View style={styles.cardInfo}>
                <Text style={styles.cardTitle}>{item.ten_doi}</Text>
                <Text style={styles.cardSub}>🌿 Thuộc chi nhánh: <Text style={{ color: "#D97706", fontWeight: "700" }}>{item.chi_nhanh?.ten_chi || "N/A"}</Text></Text>
              </View>
              <View style={styles.cardActions}>
                <TouchableOpacity style={styles.actionBtn} onPress={() => openEditGen(item)}>
                  <Ionicons name="pencil" size={16} color="#D97706" />
                </TouchableOpacity>
                <TouchableOpacity style={[styles.actionBtn, { marginLeft: 8 }]} onPress={() => handleDeleteGen(item.id, item.ten_doi)}>
                  <Ionicons name="trash" size={16} color="#EF4444" />
                </TouchableOpacity>
              </View>
            </View>
          )}
        />
      )}

      {/* BRANCH FORM MODAL */}
      <Modal visible={showBranchModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.formContainer}>
            <View style={styles.formHeader}>
              <Text style={styles.formTitle}>{isEditingBranch ? "Cập nhật chi nhánh" : "Thêm chi nhánh mới"}</Text>
              <TouchableOpacity onPress={() => setShowBranchModal(false)}>
                <Ionicons name="close-circle" size={24} color="#94A3B8" />
              </TouchableOpacity>
            </View>

            <View style={{ paddingHorizontal: 16 }}>
              <Text style={styles.formLabel}>Tên chi nhánh / Chi họ <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: Chi trưởng cụ cả"
                placeholderTextColor="#94A3B8"
                value={formTenChi}
                onChangeText={setFormTenChi}
              />

              <Text style={styles.formLabel}>Địa chỉ tổ quán</Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: Làng Sen, Nam Đàn, Nghệ An"
                placeholderTextColor="#94A3B8"
                value={formDiaChiChi}
                onChangeText={setFormDiaChiChi}
              />

              <View style={{ height: 16 }} />
            </View>

            <View style={styles.formFooter}>
              <TouchableOpacity style={styles.btnCancel} onPress={() => setShowBranchModal(false)}>
                <Text style={styles.btnCancelText}>Hủy bỏ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnSave} onPress={handleSaveBranch}>
                <Text style={styles.btnSaveText}>Lưu chi nhánh</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* GENERATION FORM MODAL */}
      <Modal visible={showGenModal} transparent animationType="slide">
        <View style={styles.modalOverlay}>
          <View style={styles.formContainer}>
            <View style={styles.formHeader}>
              <Text style={styles.formTitle}>{isEditingGen ? "Cập nhật đời gia tộc" : "Thêm đời mới"}</Text>
              <TouchableOpacity onPress={() => setShowGenModal(false)}>
                <Ionicons name="close-circle" size={24} color="#94A3B8" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} style={{ paddingHorizontal: 16 }}>
              <Text style={styles.formLabel}>Tên đời gia tộc <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TextInput
                style={styles.formInput}
                placeholder="Ví dụ: Đời thứ nhất (Đời F1)"
                placeholderTextColor="#94A3B8"
                value={formTenDoi}
                onChangeText={setFormTenDoi}
              />

              <Text style={styles.formLabel}>Chi nhánh gia đình liên kết <Text style={{ color: "#EF4444" }}>*</Text></Text>
              <TouchableOpacity style={styles.dropdown} onPress={() => setShowBranchPicker(true)}>
                <Text style={[styles.dropdownText, !formChiNhanhId && { color: "#94A3B8" }]}>
                  {formChiNhanhId 
                    ? branches.find(b => b.id === formChiNhanhId)?.ten_chi || "Chi nhánh đã chọn"
                    : "Chọn chi nhánh tổ tiên..."}
                </Text>
                <Ionicons name="chevron-down" size={16} color="#64748B" />
              </TouchableOpacity>
            </ScrollView>

            <View style={styles.formFooter}>
              <TouchableOpacity style={styles.btnCancel} onPress={() => setShowGenModal(false)}>
                <Text style={styles.btnCancelText}>Hủy bỏ</Text>
              </TouchableOpacity>
              <TouchableOpacity style={styles.btnSave} onPress={handleSaveGen}>
                <Text style={styles.btnSaveText}>Lưu đời tộc</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>

      {/* BRANCH SELECTOR OVERLAY */}
      <Modal visible={showBranchPicker} transparent animationType="fade">
        <TouchableOpacity style={styles.modalOverlay} activeOpacity={1} onPress={() => setShowBranchPicker(false)}>
          <View style={styles.selectorContainer}>
            <Text style={styles.selectorTitle}>Chọn chi nhánh liên kết</Text>
            <FlatList
              data={branches}
              keyExtractor={item => item.id.toString()}
              style={{ maxHeight: 220 }}
              renderItem={({ item }) => (
                <TouchableOpacity
                  style={styles.selectorItem}
                  onPress={() => {
                    setFormChiNhanhId(item.id);
                    setShowBranchPicker(false);
                  }}
                >
                  <Text style={styles.selectorName}>{item.ten_chi}</Text>
                  <Text style={styles.selectorSub}>📍 {item.dia_chi || "Chưa cập nhật địa chỉ"}</Text>
                </TouchableOpacity>
              )}
              ListEmptyComponent={
                <View style={{ alignItems: "center", paddingVertical: 20 }}>
                  <Text style={{ color: "#94A3B8" }}>Vui lòng thêm chi nhánh trước</Text>
                </View>
              }
            />
            <TouchableOpacity style={styles.selectorClose} onPress={() => setShowBranchPicker(false)}>
              <Text style={styles.selectorCloseText}>Đóng</Text>
            </TouchableOpacity>
          </View>
        </TouchableOpacity>
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

  // Tab bar
  tabBar: {
    flexDirection: "row", backgroundColor: "rgba(255, 159, 67, 0.05)",
    marginHorizontal: 22, borderRadius: 14, padding: 4, marginBottom: 12,
  },
  tabItem: {
    flex: 1, flexDirection: "row", alignItems: "center", justifyContent: "center",
    paddingVertical: 10, borderRadius: 10, gap: 6,
  },
  tabItemActive: { backgroundColor: "#ffffff", elevation: 2, shadowColor: "#D97706", shadowOpacity: 0.05, shadowRadius: 4 },
  tabText: { fontSize: 12, fontWeight: "700", color: "#64748B" },
  tabTextActive: { color: "#D97706" },

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
  cardSub: { fontSize: 11, color: "#64748B", fontWeight: "600", marginTop: 4 },
  cardActions: { flexDirection: "row", alignItems: "center" },
  actionBtn: {
    width: 32, height: 32, borderRadius: 10,
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    justifyContent: "center", alignItems: "center",
  },

  // Modal forms
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
  dropdown: {
    flexDirection: "row", alignItems: "center", backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5, borderColor: "rgba(255, 159, 67, 0.12)", borderRadius: 12,
    paddingHorizontal: 12, height: 42,
  },
  dropdownText: { flex: 1, fontSize: 13, fontWeight: "600", color: "#1E293B" },
  formFooter: {
    flexDirection: "row", borderTopWidth: 1, borderTopColor: "rgba(255, 159, 67, 0.08)",
    paddingTop: 12, paddingHorizontal: 16, marginTop: 14, gap: 10,
  },
  btnCancel: { flex: 1, backgroundColor: "#F1F5F9", height: 42, borderRadius: 12, alignItems: "center", justifyContent: "center" },
  btnCancelText: { fontSize: 13, fontWeight: "700", color: "#64748B" },
  btnSave: { flex: 2, backgroundColor: "#FF9F43", height: 42, borderRadius: 12, alignItems: "center", justifyContent: "center" },
  btnSaveText: { fontSize: 13, fontWeight: "800", color: "#0c0e12" },

  // Selector
  selectorContainer: {
    width: SCREEN_W * 0.8, backgroundColor: "#ffffff", borderRadius: 22, padding: 18,
    shadowColor: "#000", shadowOffset: { width: 0, height: 4 }, shadowOpacity: 0.15, shadowRadius: 10, elevation: 8,
  },
  selectorTitle: { fontSize: 15, fontWeight: "800", color: "#0F172A", marginBottom: 12, textAlign: "center" },
  selectorItem: { paddingVertical: 10, borderBottomWidth: 1, borderBottomColor: "rgba(255, 159, 67, 0.06)" },
  selectorName: { fontSize: 13, fontWeight: "700", color: "#1E293B" },
  selectorSub: { fontSize: 10, color: "#64748B", fontWeight: "600", marginTop: 2 },
  selectorClose: { backgroundColor: "#F1F5F9", paddingVertical: 10, borderRadius: 10, alignItems: "center", marginTop: 12 },
  selectorCloseText: { fontSize: 13, fontWeight: "700", color: "#64748B" },
});
