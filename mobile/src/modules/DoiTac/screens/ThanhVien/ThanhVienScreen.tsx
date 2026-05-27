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
  Image,
  FlatList,
} from "react-native";

import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function ThanhVienScreen() {
  const [members, setMembers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Bộ lọc danh sách
  const [searchQuery, setSearchQuery] = useState("");
  const [genderFilter, setGenderFilter] = useState<"Tất cả" | "Nam" | "Nữ">("Tất cả");
  const [statusFilter, setStatusFilter] = useState<"Tất cả" | "Còn sống" | "Đã mất">("Tất cả");

  // State cho Modal Form (Thêm/Sửa)
  const [showFormModal, setShowFormModal] = useState(false);
  const [isEditing, setIsEditing] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // Trường thông tin Form
  const [hoTen, setHoTen] = useState("");
  const [email, setEmail] = useState("");
  const [gioiTinh, setGioiTinh] = useState("Nam");
  const [ngaySinh, setNgaySinh] = useState("");
  const [ngayMat, setNgayMat] = useState("");
  const [trangThai, setTrangThai] = useState("Còn sống");
  const [ngheNghiep, setNgheNghiep] = useState("");
  const [thongTinThem, setThongTinThem] = useState("");
  const [chaId, setChaId] = useState<number | null>(null);
  const [meId, setMeId] = useState<number | null>(null);
  const [spouseOfId, setSpouseOfId] = useState<number | null>(null);
  const [avatar, setAvatar] = useState("");

  // Search selector trong Form (Cha/Mẹ/Vợ/Chồng)
  const [showSelectorModal, setShowSelectorModal] = useState(false);
  const [selectorTarget, setSelectorTarget] = useState<"cha" | "me" | "spouse">("cha");
  const [selectorSearch, setSelectorSearch] = useState("");

  useEffect(() => {
    loadMembers();
  }, []);

  const loadMembers = async () => {
    try {
      setLoading(true);
      const res = await api.get("/thanh-vien/get-data");
      if (Array.isArray(res.data)) {
        setMembers(res.data);
      } else if (res.data && Array.isArray(res.data.data)) {
        setMembers(res.data.data);
      } else {
        setMembers([]);
      }
    } catch (error: any) {
      console.log(error);
      Alert.alert("Lỗi", "Không thể tải danh sách thành viên");
    } finally {
      setLoading(false);
    }
  };

  // Mở trình chọn quan hệ trong form
  const openFormSelector = (target: "cha" | "me" | "spouse") => {
    setSelectorTarget(target);
    setSelectorSearch("");
    setShowSelectorModal(true);
  };

  // Áp dụng chọn quan hệ
  const handleFormSelect = (id: number | null) => {
    if (selectorTarget === "cha") {
      setChaId(id);
    } else if (selectorTarget === "me") {
      setMeId(id);
    } else if (selectorTarget === "spouse") {
      setSpouseOfId(id);
    }
    setShowSelectorModal(false);
  };

  const getMemberNameById = (id: number | null) => {
    if (id === null) return "Không có";
    const found = members.find((m) => m.id === id);
    return found ? found.ho_ten : `Thành viên #${id}`;
  };

  // Khởi tạo Form Thêm mới
  const openAddForm = () => {
    setIsEditing(false);
    setEditingId(null);
    setHoTen("");
    setEmail("");
    setGioiTinh("Nam");
    setNgaySinh("");
    setNgayMat("");
    setTrangThai("Còn sống");
    setNgheNghiep("");
    setThongTinThem("");
    setChaId(null);
    setMeId(null);
    setSpouseOfId(null);
    setAvatar("");
    setShowFormModal(true);
  };

  // Khởi tạo Form Sửa
  const openEditForm = (member: any) => {
    setIsEditing(true);
    setEditingId(member.id);
    setHoTen(member.ho_ten || "");
    setEmail(member.email || "");
    setGioiTinh(member.gioi_tinh || "Nam");
    setNgaySinh(member.ngay_sinh || "");
    setNgayMat(member.ngay_mat || "");
    setTrangThai(member.trang_thai || "Còn sống");
    setNgheNghiep(member.nghe_nghiep || "");
    setThongTinThem(member.thong_tin_them || "");
    setChaId(member.cha_id ?? member.id_cha ?? null);
    setMeId(member.me_id ?? member.id_me ?? null);
    setSpouseOfId(member.spouse_of_id ?? null);
    setAvatar(member.avatar || "");
    setShowFormModal(true);
  };

  // Xóa thành viên
  const xoaThanhVien = async (id: number) => {
    Alert.alert(
      "Xác nhận xóa",
      "Bạn có chắc chắn muốn xóa thành viên này? Hành động này có thể làm thay đổi cây gia phả liên kết.",
      [
        { text: "Hủy", style: "cancel" },
        {
          text: "Xóa",
          style: "destructive",
          onPress: async () => {
            try {
              setLoading(true);
              const res = await api.post("/thanh-vien/delete", { id });
              if (res.data.status) {
                // Ghi nhật ký hoạt động liên kết
                const memberObj = members.find(m => m.id === id);
                await api.post("/nhat-ky-hoat-dong/create", {
                  hanh_dong: `Xóa thành viên: ${memberObj ? memberObj.ho_ten : `ID #${id}`}`,
                });
                
                Alert.alert("Thành công", "Đã xóa thành viên");
                loadMembers();
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

  // Đổi nhanh trạng thái Sống / Mất
  const toggleTrangThai = async (id: number) => {
    try {
      setLoading(true);
      const res = await api.post("/thanh-vien/status", { id });
      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết
        const memberObj = members.find(m => m.id === id);
        const prevStatus = memberObj ? memberObj.trang_thai : "Chưa rõ";
        const nextStatus = prevStatus === "Còn sống" ? "Đã mất" : "Còn sống";
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: `Đổi trạng thái: ${memberObj ? memberObj.ho_ten : `ID #${id}`} (${prevStatus} -> ${nextStatus})`,
        });

        Alert.alert("Thành công", "Đã chuyển đổi trạng thái thành công!");
        loadMembers();
      } else {
        Alert.alert("Thất bại", res.data.message || "Lỗi thay đổi trạng thái");
      }
    } catch (error: any) {
      Alert.alert("Lỗi", error.response?.data?.message || "Không thể kết nối máy chủ");
    } finally {
      setLoading(false);
    }
  };

  // Lưu Form Thêm / Sửa
  const luuThanhVien = async () => {
    if (!hoTen.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập họ tên thành viên");
      return;
    }

    try {
      setLoading(true);
      const activeBranchId =
        members[0]?.chi_nhanh_id || members[0]?.id_chi_nhanh || 1;

      const payload = {
        id: editingId,
        ho_ten: hoTen,
        email: email || null,
        gioi_tinh: gioiTinh,
        ngay_sinh: ngaySinh || null,
        ngay_mat: trangThai === "Đã mất" ? (ngayMat || null) : null,
        trang_thai: trangThai,
        nghe_nghiep: ngheNghiep || null,
        thong_tin_them: thongTinThem || null,
        
        cha_id: chaId,
        id_cha: chaId,
        me_id: meId,
        id_me: meId,
        spouse_of_id: spouseOfId,
        
        chi_nhanh_id: activeBranchId,
        id_chi_nhanh: activeBranchId,
        avatar: avatar || null,
      };

      let res;
      if (isEditing) {
        res = await api.post("/thanh-vien/update", payload);
      } else {
        res = await api.post("/thanh-vien/create", payload);
      }

      if (res.data.status) {
        // Ghi nhật ký hoạt động liên kết
        await api.post("/nhat-ky-hoat-dong/create", {
          hanh_dong: isEditing 
            ? `Cập nhật thành viên: ${hoTen} (ID: ${editingId})` 
            : `Thêm thành viên mới: ${hoTen}`,
        });

        Alert.alert("Thành công", isEditing ? "Cập nhật thành viên thành công" : "Thêm mới thành viên thành công!");
        setShowFormModal(false);
        loadMembers();
      } else {
        Alert.alert("Lỗi", res.data.message || "Lưu dữ liệu thất bại");
      }
    } catch (error: any) {
      Alert.alert("Lỗi", error.response?.data?.message || "Lưu thất bại từ hệ thống");
    } finally {
      setLoading(false);
    }
  };

  // Lọc danh sách thành viên ở màn hình chính
  const filteredList = members.filter((m) => {
    const q = searchQuery.toLowerCase();
    const nameMatch = m.ho_ten?.toLowerCase().includes(q) || m.nghe_nghiep?.toLowerCase().includes(q);
    
    let genderMatch = true;
    if (genderFilter !== "Tất cả") {
      genderMatch = m.gioi_tinh === genderFilter;
    }

    let statusMatch = true;
    if (statusFilter !== "Tất cả") {
      statusMatch = m.trang_thai === statusFilter;
    }

    return nameMatch && genderMatch && statusMatch;
  });

  // Lọc danh sách cho modal chọn quan hệ
  const selectorMembersFiltered = members.filter((m) => {
    if (isEditing && m.id === editingId) return false;
    
    const matchSearch = m.ho_ten?.toLowerCase().includes(selectorSearch.toLowerCase());
    if (!matchSearch) return false;

    if (selectorTarget === "cha") return m.gioi_tinh === "Nam";
    if (selectorTarget === "me") return m.gioi_tinh === "Nữ";

    return true; // Vợ/Chồng
  });

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      {/* TRANG TRÍ HSL SANG TRỌNG */}
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      {/* HEADER */}
      <View style={styles.header}>
        <View>
          <Text style={styles.title}>Quản Lý Thành Viên</Text>
          <Text style={styles.subTitle}>Dòng họ gia tộc phả hệ</Text>
        </View>
        <TouchableOpacity
          style={styles.addBtn}
          onPress={openAddForm}
          activeOpacity={0.8}
        >
          <Ionicons name="add-outline" size={24} color="#0c0e12" />
        </TouchableOpacity>
      </View>

      {/* TÌM KIẾM VÀ BỘ LỌC */}
      <View style={styles.filterSection}>
        {/* THANH TÌM KIẾM */}
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={20} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm theo tên hoặc nghề nghiệp..."
            placeholderTextColor="#94A3B8"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>

        {/* TẮP LỌC GIỚI TÍNH */}
        <ScrollView
          horizontal
          showsHorizontalScrollIndicator={false}
          contentContainerStyle={styles.filterScroll}
        >
          <TouchableOpacity
            style={[styles.tag, genderFilter === "Tất cả" && styles.tagActive]}
            onPress={() => setGenderFilter("Tất cả")}
          >
            <Text style={[styles.tagText, genderFilter === "Tất cả" && styles.tagTextActive]}>
              Giới tính: Tất cả
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, genderFilter === "Nam" && styles.tagActive]}
            onPress={() => setGenderFilter("Nam")}
          >
            <Text style={[styles.tagText, genderFilter === "Nam" && styles.tagTextActive]}>
              Nam ♂
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, genderFilter === "Nữ" && styles.tagActive]}
            onPress={() => setGenderFilter("Nữ")}
          >
            <Text style={[styles.tagText, genderFilter === "Nữ" && styles.tagTextActive]}>
              Nữ ♀
            </Text>
          </TouchableOpacity>

          <View style={styles.divider} />

          {/* TẮP LỌC TRẠNG THÁI */}
          <TouchableOpacity
            style={[styles.tag, statusFilter === "Tất cả" && styles.tagActive]}
            onPress={() => setStatusFilter("Tất cả")}
          >
            <Text style={[styles.tagText, statusFilter === "Tất cả" && styles.tagTextActive]}>
              Trạng thái: Tất cả
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, statusFilter === "Còn sống" && styles.tagActive]}
            onPress={() => setStatusFilter("Còn sống")}
          >
            <Text style={[styles.tagText, statusFilter === "Còn sống" && styles.tagTextActive]}>
              Còn sống
            </Text>
          </TouchableOpacity>
          <TouchableOpacity
            style={[styles.tag, statusFilter === "Đã mất" && styles.tagActive]}
            onPress={() => setStatusFilter("Đã mất")}
          >
            <Text style={[styles.tagText, statusFilter === "Đã mất" && styles.tagTextActive]}>
              Đã mất
            </Text>
          </TouchableOpacity>
        </ScrollView>
      </View>

      {/* DANH SÁCH THÀNH VIÊN */}
      {loading && members.length === 0 ? (
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
              <Ionicons name="people-outline" size={60} color="#CBD5E1" />
              <Text style={styles.emptyText}>Không tìm thấy thành viên gia phả nào</Text>
            </View>
          }
          renderItem={({ item }) => {
            const isMale = item.gioi_tinh === "Nam";
            const isAlive = item.trang_thai === "Còn sống";
            
            return (
              <View style={styles.memberCard}>
                <Image
                  source={{ uri: item.avatar || "https://avatar.iran.liara.run/public" }}
                  style={[
                    styles.avatar,
                    { borderColor: isMale ? "rgba(2, 132, 199, 0.35)" : "rgba(236, 72, 153, 0.35)" },
                  ]}
                />
                
                <View style={styles.memberDetails}>
                  <Text style={styles.memberName}>{item.ho_ten}</Text>
                  <Text style={styles.memberJob}>
                    {item.gioi_tinh} • {item.nghe_nghiep || "Chưa cập nhật nghề"}
                  </Text>
                  {item.email && <Text style={styles.memberEmail}>{item.email}</Text>}
                  
                  <View style={styles.badgeRow}>
                    <View
                      style={[
                        styles.statusBadge,
                        { backgroundColor: isAlive ? "rgba(74, 222, 128, 0.12)" : "rgba(239, 68, 68, 0.12)" },
                      ]}
                    >
                      <Text style={[styles.statusText, { color: isAlive ? "#22C55E" : "#EF4444" }]}>
                        {item.trang_thai}
                      </Text>
                    </View>
                  </View>
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
                    style={[
                      styles.actionBtn,
                      { backgroundColor: isAlive ? "rgba(239, 68, 68, 0.08)" : "rgba(34, 197, 94, 0.08)" },
                    ]}
                    onPress={() => toggleTrangThai(item.id)}
                    activeOpacity={0.7}
                  >
                    <Ionicons
                      name={isAlive ? "heart-dislike-outline" : "heart-outline"}
                      size={16}
                      color={isAlive ? "#EF4444" : "#22C55E"}
                    />
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={[styles.actionBtn, { backgroundColor: "rgba(239, 68, 68, 0.08)" }]}
                    onPress={() => xoaThanhVien(item.id)}
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

      {/* MODAL FORM THÊM / SỬA THÀNH VIÊN */}
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
                {isEditing ? "CẬP NHẬT THÀNH VIÊN" : "THÊM THÀNH VIÊN MỚI"}
              </Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowFormModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.formScroll}>
              {/* HỌ TÊN */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Họ và tên *</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="Nhập họ tên đầy đủ..."
                  placeholderTextColor="#94A3B8"
                  value={hoTen}
                  onChangeText={setHoTen}
                />
              </View>

              {/* GIỚI TÍNH & TRẠNG THÁI */}
              <View style={styles.rowForm}>
                <View style={[styles.formGroup, { flex: 1, marginRight: 12 }]}>
                  <Text style={styles.formLabel}>Giới tính</Text>
                  <View style={styles.genderRow}>
                    <TouchableOpacity
                      style={[styles.genderBtn, gioiTinh === "Nam" && styles.genderBtnActive]}
                      onPress={() => setGioiTinh("Nam")}
                    >
                      <Text style={[styles.genderBtnText, gioiTinh === "Nam" && styles.genderBtnTextActive]}>
                        Nam ♂
                      </Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={[styles.genderBtn, gioiTinh === "Nữ" && styles.genderBtnActive]}
                      onPress={() => setGioiTinh("Nữ")}
                    >
                      <Text style={[styles.genderBtnText, gioiTinh === "Nữ" && styles.genderBtnTextActive]}>
                        Nữ ♀
                      </Text>
                    </TouchableOpacity>
                  </View>
                </View>

                <View style={[styles.formGroup, { flex: 1 }]}>
                  <Text style={styles.formLabel}>Trạng thái</Text>
                  <View style={styles.genderRow}>
                    <TouchableOpacity
                      style={[
                        styles.genderBtn,
                        trangThai === "Còn sống" && [styles.genderBtnActive, { borderColor: "#22C55E" }],
                      ]}
                      onPress={() => setTrangThai("Còn sống")}
                    >
                      <Text style={[styles.genderBtnText, trangThai === "Còn sống" && { color: "#22C55E", fontWeight: "900" }]}>
                        Còn sống
                      </Text>
                    </TouchableOpacity>
                    <TouchableOpacity
                      style={[
                        styles.genderBtn,
                        trangThai === "Đã mất" && [styles.genderBtnActive, { borderColor: "#EF4444" }],
                      ]}
                      onPress={() => setTrangThai("Đã mất")}
                    >
                      <Text style={[styles.genderBtnText, trangThai === "Đã mất" && { color: "#EF4444", fontWeight: "900" }]}>
                        Đã mất
                      </Text>
                    </TouchableOpacity>
                  </View>
                </View>
              </View>

              {/* NGHỀ NGHIỆP & EMAIL */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Nghề nghiệp</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="Kỹ sư, Giáo viên, Doanh nhân..."
                  placeholderTextColor="#94A3B8"
                  value={ngheNghiep}
                  onChangeText={setNgheNghiep}
                />
              </View>

              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Thư điện tử (Email)</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="name@domain.com"
                  placeholderTextColor="#94A3B8"
                  value={email}
                  onChangeText={setEmail}
                  autoCapitalize="none"
                  keyboardType="email-address"
                />
              </View>

              {/* NGÀY SINH & NGÀY MẤT */}
              <View style={styles.rowForm}>
                <View style={[styles.formGroup, { flex: 1, marginRight: 12 }]}>
                  <Text style={styles.formLabel}>Ngày sinh</Text>
                  <TextInput
                    style={styles.formInput}
                    placeholder="YYYY-MM-DD"
                    placeholderTextColor="#94A3B8"
                    value={ngaySinh}
                    onChangeText={setNgaySinh}
                  />
                </View>

                {trangThai === "Đã mất" && (
                  <View style={[styles.formGroup, { flex: 1 }]}>
                    <Text style={styles.formLabel}>Ngày mất</Text>
                    <TextInput
                      style={styles.formInput}
                      placeholder="YYYY-MM-DD"
                      placeholderTextColor="#94A3B8"
                      value={ngayMat}
                      onChangeText={setNgayMat}
                    />
                  </View>
                )}
              </View>

              {/* HÌNH ẢNH AVATAR */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Đường dẫn ảnh đại diện (Avatar URL)</Text>
                <TextInput
                  style={styles.formInput}
                  placeholder="https://link-to-avatar.png"
                  placeholderTextColor="#94A3B8"
                  value={avatar}
                  onChangeText={setAvatar}
                />
              </View>

              {/* BỘ LỌC CHỌN QUAN HỆ GIA PHẢ TRỰC QUAN */}
              <Text style={styles.relationSectionTitle}>Liên Kết Mối Quan Hệ</Text>

              {/* CHỌN CHA */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Người Cha</Text>
                <TouchableOpacity
                  style={styles.relationSelectorTrigger}
                  onPress={() => openFormSelector("cha")}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.relationSelectorText, chaId === null && styles.relationSelectorPlaceholder]}>
                    👨 {getMemberNameById(chaId)}
                  </Text>
                  <Ionicons name="chevron-forward-outline" size={16} color="#FF9F43" />
                </TouchableOpacity>
              </View>

              {/* CHỌN MẸ */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Người Mẹ</Text>
                <TouchableOpacity
                  style={styles.relationSelectorTrigger}
                  onPress={() => openFormSelector("me")}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.relationSelectorText, meId === null && styles.relationSelectorPlaceholder]}>
                    👩 {getMemberNameById(meId)}
                  </Text>
                  <Ionicons name="chevron-forward-outline" size={16} color="#FF9F43" />
                </TouchableOpacity>
              </View>

              {/* CHỌN VỢ / CHỒNG */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Vợ hoặc Chồng</Text>
                <TouchableOpacity
                  style={styles.relationSelectorTrigger}
                  onPress={() => openFormSelector("spouse")}
                  activeOpacity={0.7}
                >
                  <Text style={[styles.relationSelectorText, spouseOfId === null && styles.relationSelectorPlaceholder]}>
                    💍 {getMemberNameById(spouseOfId)}
                  </Text>
                  <Ionicons name="chevron-forward-outline" size={16} color="#FF9F43" />
                </TouchableOpacity>
              </View>

              {/* GHI CHÚ / THÔNG TIN THÊM */}
              <View style={styles.formGroup}>
                <Text style={styles.formLabel}>Thông tin thêm / Ghi chú gia phả</Text>
                <TextInput
                  style={[styles.formInput, { height: 80, textAlignVertical: "top", paddingTop: 10 }]}
                  placeholder="Thông tin chi tiết về cuộc đời, giai thoại..."
                  placeholderTextColor="#94A3B8"
                  value={thongTinThem}
                  onChangeText={setThongTinThem}
                  multiline={true}
                />
              </View>

              {/* NÚT LƯU */}
              <TouchableOpacity
                style={styles.saveBtn}
                onPress={luuThanhVien}
                activeOpacity={0.8}
              >
                <Text style={styles.saveBtnText}>{isEditing ? "Cập Nhật" : "Thêm Mới"}</Text>
                <Ionicons name="save-outline" size={18} color="#0c0e12" style={{ marginLeft: 8 }} />
              </TouchableOpacity>
            </ScrollView>
          </View>
        </View>
      </Modal>

      {/* MODAL SEARCHABLE SELECTOR QUAN HỆ TRONG FORM */}
      <Modal
        visible={showSelectorModal}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowSelectorModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.relationSelectorModal}>
            <View style={styles.modalIndicator} />
            
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {selectorTarget === "cha"
                  ? "CHỌN NGƯỜI CHA ♂"
                  : selectorTarget === "me"
                  ? "CHỌN NGƯỜI MẸ ♀"
                  : "CHỌN VỢ / CHỒNG 💍"}
              </Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowSelectorModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            {/* Ô TÌM KIẾM TRONG SELECTOR */}
            <View style={styles.searchBar}>
              <Ionicons name="search-outline" size={20} color="#FF9F43" style={{ marginRight: 8 }} />
              <TextInput
                style={styles.searchInput}
                placeholder="Tìm thành viên..."
                placeholderTextColor="#94A3B8"
                value={selectorSearch}
                onChangeText={setSelectorSearch}
              />
            </View>

            {/* DANH SÁCH CHỌN */}
            <FlatList
              data={selectorMembersFiltered}
              keyExtractor={(item) => item.id.toString()}
              contentContainerStyle={{ paddingBottom: 30, paddingTop: 10 }}
              showsVerticalScrollIndicator={false}
              ListHeaderComponent={
                <TouchableOpacity
                  style={styles.selectorItem}
                  onPress={() => handleFormSelect(null)}
                >
                  <View style={[styles.selectorAvatar, { backgroundColor: "rgba(255, 159, 67, 0.1)" }]}>
                    <Ionicons name="ban-outline" size={20} color="#FF9F43" />
                  </View>
                  <Text style={[styles.selectorName, { color: "#D97706" }]}>Không có liên kết này</Text>
                </TouchableOpacity>
              }
              ListEmptyComponent={
                <View style={styles.emptyContainer}>
                  <Text style={styles.emptyText}>Không tìm thấy thành viên phù hợp</Text>
                </View>
              }
              renderItem={({ item }) => (
                <TouchableOpacity
                  style={styles.selectorItem}
                  onPress={() => handleFormSelect(item.id)}
                >
                  <Image
                    source={{ uri: item.avatar || "https://avatar.iran.liara.run/public" }}
                    style={styles.selectorAvatar}
                  />
                  <View style={{ flex: 1 }}>
                    <Text style={styles.selectorName}>{item.ho_ten}</Text>
                    <Text style={styles.selectorSub}>
                      {item.gioi_tinh} • {item.nghe_nghiep || "Chưa cập nhật nghề"}
                    </Text>
                  </View>
                  <Ionicons name="chevron-forward-outline" size={16} color="#CBD5E1" />
                </TouchableOpacity>
              )}
            />
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

  // TÌM KIẾM VÀ BỘ LỌC
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
  divider: {
    width: 1.5,
    height: 20,
    backgroundColor: "rgba(255, 159, 67, 0.18)",
    marginHorizontal: 8,
  },

  // DANH SÁCH THÀNH VIÊN CARD
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
  memberCard: {
    flexDirection: "row",
    alignItems: "center",
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
  avatar: {
    width: 60,
    height: 60,
    borderRadius: 30,
    borderWidth: 1.5,
    marginRight: 14,
  },
  memberDetails: {
    flex: 1,
    justifyContent: "center",
  },
  memberName: {
    fontSize: 16,
    fontWeight: "800",
    color: "#1E293B",
  },
  memberJob: {
    fontSize: 12,
    color: "#D97706",
    fontWeight: "700",
    marginTop: 2,
  },
  memberEmail: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  badgeRow: {
    flexDirection: "row",
    marginTop: 8,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  statusText: {
    fontSize: 10,
    fontWeight: "800",
    textTransform: "uppercase",
  },
  actionColumn: {
    flexDirection: "column",
    justifyContent: "space-between",
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
    height: "88%",
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
    marginBottom: 14,
  },
  formLabel: {
    fontSize: 11,
    fontWeight: "800",
    color: "#D97706",
    marginBottom: 6,
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
  rowForm: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  genderRow: {
    flexDirection: "row",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    padding: 3,
    height: 48,
  },
  genderBtn: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    borderRadius: 10,
  },
  genderBtnActive: {
    backgroundColor: "#ffffff",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.3)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 4,
    elevation: 1,
  },
  genderBtnText: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "700",
  },
  genderBtnTextActive: {
    color: "#D97706",
    fontWeight: "900",
  },

  // CHỌN QUAN HỆ SỐ ĐỒ TRỰC QUAN TRONG FORM
  relationSectionTitle: {
    fontSize: 13,
    fontWeight: "900",
    color: "#0F172A",
    marginTop: 14,
    marginBottom: 12,
    borderBottomWidth: 1.5,
    borderBottomColor: "rgba(255, 159, 67, 0.15)",
    paddingBottom: 6,
    textTransform: "uppercase",
    letterSpacing: 0.8,
  },
  relationSelectorTrigger: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    paddingHorizontal: 14,
    height: 48,
  },
  relationSelectorText: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
  },
  relationSelectorPlaceholder: {
    color: "#94A3B8",
    fontWeight: "600",
  },
  saveBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 16,
    borderRadius: 16,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 26,
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

  // SELECTOR MODAL CHO MỐI QUAN HỆ TRONG FORM
  relationSelectorModal: {
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    height: "78%",
    paddingHorizontal: 22,
    paddingTop: 12,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
  },
  selectorItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 12,
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.06)",
  },
  selectorAvatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    marginRight: 12,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
  },
  selectorName: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
  },
  selectorSub: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
});
