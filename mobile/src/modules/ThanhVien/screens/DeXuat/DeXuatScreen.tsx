import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  Modal,
  TextInput,
  Image,
  StatusBar,
  Platform,
  FlatList,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function DeXuatScreen() {
  const [activeTab, setActiveTab] = useState<"form" | "history">("form");
  const [loading, setLoading] = useState(true);
  const [historyLoading, setHistoryLoading] = useState(false);
  const [members, setMembers] = useState<any[]>([]);
  const [myProposals, setMyProposals] = useState<any[]>([]);

  // Trạng thái Form
  const [proposalType, setProposalType] = useState<"edit" | "add_child" | "add_spouse">("edit");
  const [selectedMember, setSelectedMember] = useState<any | null>(null);
  
  // Các trường dữ liệu đề xuất
  const [hoTen, setHoTen] = useState("");
  const [gioiTinh, setGioiTinh] = useState<"Nam" | "Nữ">("Nam");
  const [ngaySinh, setNgaySinh] = useState("");
  const [ngayMat, setNgayMat] = useState("");
  const [trangThai, setTrangThai] = useState<"Còn sống" | "Đã mất">("Còn sống");
  const [sdt, setSdt] = useState("");
  const [email, setEmail] = useState("");
  const [ngheNghiep, setNgheNghiep] = useState("");
  const [diaChi, setDiaChi] = useState("");
  const [diaChiMo, setDiaChiMo] = useState("");
  const [tieuSu, setTieuSu] = useState("");

  // Trạng thái modal tìm kiếm thành viên
  const [showMemberSelector, setShowMemberSelector] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadInitialData();
  }, []);

  useEffect(() => {
    if (activeTab === "history") {
      loadHistory();
    }
  }, [activeTab]);

  const handleReload = async () => {
    await loadInitialData();
    if (activeTab === "history") {
      await loadHistory();
    }
  };

  const loadInitialData = async () => {
    try {
      setLoading(true);
      const res = await api.get("/thanh-vien/get-data");
      if (Array.isArray(res.data)) {
        setMembers(res.data);
      } else if (res.data && Array.isArray(res.data.data)) {
        setMembers(res.data.data);
      }
    } catch (err: any) {
      console.log("Lỗi tải thành viên:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể tải danh sách thành viên gia tộc.");
    } finally {
      setLoading(false);
    }
  };

  const loadHistory = async () => {
    try {
      setHistoryLoading(true);
      const res = await api.get("/de-xuat/my-proposals");
      if (res.data.status) {
        setMyProposals(res.data.data || []);
      }
    } catch (err: any) {
      console.log("Lỗi tải lịch sử đề xuất:", err.response?.data || err.message);
    } finally {
      setHistoryLoading(false);
    }
  };

  const handleSelectMember = (member: any) => {
    setSelectedMember(member);
    setShowMemberSelector(false);

    // Nếu chọn loại đính chính (edit), tự động điền các trường dữ liệu hiện tại
    if (proposalType === "edit") {
      setHoTen(member.ho_ten || "");
      setGioiTinh(member.gioi_tinh === "Nữ" ? "Nữ" : "Nam");
      setNgaySinh(member.ngay_sinh || "");
      setNgayMat(member.ngay_mat || "");
      setTrangThai(member.trang_thai === "Đã mất" ? "Đã mất" : "Còn sống");
      setSdt(member.sdt || "");
      setEmail(member.email || "");
      setNgheNghiep(member.nghe_nghiep || "");
      setDiaChi(member.dia_chi || "");
      setDiaChiMo(member.dia_chi_mo || "");
      setTieuSu(member.tieu_su || "");
    }
  };

  const resetForm = () => {
    setSelectedMember(null);
    setHoTen("");
    setGioiTinh("Nam");
    setNgaySinh("");
    setNgayMat("");
    setTrangThai("Còn sống");
    setSdt("");
    setEmail("");
    setNgheNghiep("");
    setDiaChi("");
    setDiaChiMo("");
    setTieuSu("");
  };

  const handleTypeChange = (type: "edit" | "add_child" | "add_spouse") => {
    setProposalType(type);
    resetForm();
  };

  const handleSubmit = async () => {
    if (!selectedMember) {
      Alert.alert("Thông báo", "Vui lòng chọn thành viên đích làm mốc!");
      return;
    }

    if (!hoTen.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập họ và tên!");
      return;
    }

    try {
      setSubmitting(true);
      
      // Xây dựng object data đề xuất chỉnh sửa/thêm mới
      const proposedData: any = {
        ho_ten: hoTen.trim(),
        gioi_tinh: gioiTinh,
        ngay_sinh: ngaySinh.trim() || null,
        ngay_mat: trangThai === "Đã mất" ? (ngayMat.trim() || null) : null,
        trang_thai: trangThai,
        nghe_nghiep: ngheNghiep.trim() || null,
        tieu_su: tieuSu.trim() || null,
      };

      if (proposalType === "edit") {
        proposedData.sdt = sdt.trim() || null;
        proposedData.email = email.trim() || null;
        proposedData.dia_chi = diaChi.trim() || null;
        proposedData.dia_chi_mo = trangThai === "Đã mất" ? (diaChiMo.trim() || null) : null;
      }

      const payload = {
        type: proposalType,
        thanh_vien_id: selectedMember.id,
        data: proposedData,
      };

      const res = await api.post("/de-xuat/create", payload);
      if (res.data.status) {
        Alert.alert("Thành Công", res.data.message || "Gửi đề xuất thành công!");
        resetForm();
        setActiveTab("history"); // Chuyển sang xem lịch sử
      } else {
        Alert.alert("Lỗi", res.data.message || "Không thể gửi đề xuất.");
      }
    } catch (err: any) {
      console.log("Lỗi gửi đề xuất:", err.response?.data || err.message);
      Alert.alert("Lỗi", err.response?.data?.message || "Lỗi máy chủ khi gửi đề xuất.");
    } finally {
      setSubmitting(false);
    }
  };

  const getStatusStyle = (status: string) => {
    switch (status) {
      case "approved":
        return { bg: "rgba(16, 185, 129, 0.08)", text: "#10B981", border: "rgba(16, 185, 129, 0.15)", label: "Đã duyệt" };
      case "rejected":
        return { bg: "rgba(239, 68, 68, 0.08)", text: "#EF4444", border: "rgba(239, 68, 68, 0.15)", label: "Bác bỏ" };
      default:
        return { bg: "rgba(245, 158, 11, 0.08)", text: "#F59E0B", border: "rgba(245, 158, 11, 0.15)", label: "Chờ duyệt" };
    }
  };

  const getProposalTypeLabel = (type: string) => {
    switch (type) {
      case "edit":
        return "Đính chính thông tin";
      case "add_child":
        return "Thêm con nối dõi";
      case "add_spouse":
        return "Thêm vợ/chồng";
      default:
        return type;
    }
  };

  const filteredMembers = members.filter((m) =>
    m.ho_ten?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  if (loading) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#FF9F43" />
        <Text style={styles.loadingText}>Đang tải trang đề xuất...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <View style={[styles.glowSphere, styles.glowAmber, { top: -60, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -80, right: -60 }]} />

      {/* HEADER BANNER */}
      <View style={styles.header}>
        <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 }}>
          <View style={{ flex: 1 }}>
            <Text style={styles.headerSubtitle}>GIA PHẢ CHÍNH THỐNG</Text>
            <Text style={styles.headerTitle}>Gửi Đề Xuất Chỉnh Sửa</Text>
          </View>
          <TouchableOpacity
            style={styles.reloadHeaderBtn}
            onPress={handleReload}
            activeOpacity={0.7}
          >
            <Ionicons name="refresh-outline" size={20} color="#FF9F43" />
          </TouchableOpacity>
        </View>
        <Text style={styles.headerDesc}>
          Nếu phát hiện thông tin gia tộc ghi chép sai lệch hoặc có thành viên mới chào đời, hãy gửi đề xuất đính chính để trưởng tộc phê duyệt.
        </Text>
      </View>

      {/* TAB SWITCHER */}
      <View style={styles.tabContainer}>
        <TouchableOpacity
          style={[styles.tabButton, activeTab === "form" && styles.tabButtonActive]}
          onPress={() => setActiveTab("form")}
          activeOpacity={0.8}
        >
          <Ionicons
            name="create-outline"
            size={18}
            color={activeTab === "form" ? "#FFF8F2" : "#64748B"}
            style={{ marginRight: 6 }}
          />
          <Text style={[styles.tabText, activeTab === "form" && styles.tabTextActive]}>
            Gửi Đề Xuất
          </Text>
        </TouchableOpacity>

        <TouchableOpacity
          style={[styles.tabButton, activeTab === "history" && styles.tabButtonActive]}
          onPress={() => setActiveTab("history")}
          activeOpacity={0.8}
        >
          <Ionicons
            name="time-outline"
            size={18}
            color={activeTab === "history" ? "#FFF8F2" : "#64748B"}
            style={{ marginRight: 6 }}
          />
          <Text style={[styles.tabText, activeTab === "history" && styles.tabTextActive]}>
            Lịch Sử Đề Xuất
          </Text>
        </TouchableOpacity>
      </View>

      {activeTab === "form" ? (
        <ScrollView
          style={styles.scrollView}
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
          keyboardShouldPersistTaps="handled"
        >
          {/* LOẠI ĐỀ XUẤT */}
          <Text style={styles.sectionLabel}>1. Chọn hình thức đề xuất</Text>
          <View style={styles.typeSelector}>
            <TouchableOpacity
              style={[styles.typeOption, proposalType === "edit" && styles.typeOptionActive]}
              onPress={() => handleTypeChange("edit")}
              activeOpacity={0.8}
            >
              <Ionicons name="pencil" size={18} color={proposalType === "edit" ? "#0c0e12" : "#D97706"} />
              <Text style={[styles.typeOptionText, proposalType === "edit" && styles.typeOptionTextActive]}>
                Đính chính
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.typeOption, proposalType === "add_child" && styles.typeOptionActive]}
              onPress={() => handleTypeChange("add_child")}
              activeOpacity={0.8}
            >
              <Ionicons name="person-add-outline" size={18} color={proposalType === "add_child" ? "#0c0e12" : "#D97706"} />
              <Text style={[styles.typeOptionText, proposalType === "add_child" && styles.typeOptionTextActive]}>
                Thêm con
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.typeOption, proposalType === "add_spouse" && styles.typeOptionActive]}
              onPress={() => handleTypeChange("add_spouse")}
              activeOpacity={0.8}
            >
              <Ionicons name="people" size={18} color={proposalType === "add_spouse" ? "#0c0e12" : "#D97706"} />
              <Text style={[styles.typeOptionText, proposalType === "add_spouse" && styles.typeOptionTextActive]}>
                Thêm vợ/chồng
              </Text>
            </TouchableOpacity>
          </View>

          {/* CHỌN MỐC THÀNH VIÊN */}
          <Text style={styles.sectionLabel}>
            {proposalType === "edit" ? "2. Chọn thành viên cần sửa đổi" : "2. Chọn thành viên làm mốc liên hệ"}
          </Text>
          <TouchableOpacity
            style={styles.selectorTrigger}
            onPress={() => setShowMemberSelector(true)}
            activeOpacity={0.8}
          >
            <View style={styles.selectorLeft}>
              <Image
                source={{
                  uri: selectedMember?.avatar || "https://avatar.iran.liara.run/public",
                }}
                style={styles.avatarMini}
              />
              <View>
                <Text
                  style={[
                    styles.selectorValueText,
                    !selectedMember && styles.selectorPlaceholder,
                  ]}
                >
                  {selectedMember ? selectedMember.ho_ten : "-- Nhấp chọn thành viên mốc --"}
                </Text>
                {selectedMember && (
                  <Text style={styles.selectorValueSub}>
                    Mã nhân khẩu: #{selectedMember.id} • {selectedMember.gioi_tinh}
                  </Text>
                )}
              </View>
            </View>
            <Ionicons name="search-outline" size={20} color="#FF9F43" />
          </TouchableOpacity>

          {selectedMember && (
            <View style={styles.formContainer}>
              <Text style={styles.sectionLabel}>3. Nhập thông tin đề xuất thay đổi</Text>
              
              {/* Họ tên */}
              <Text style={styles.inputLabel}>Họ và Tên <Text style={{ color: "red" }}>*</Text></Text>
              <TextInput
                style={styles.textInput}
                placeholder="Nhập họ và tên đầy đủ..."
                placeholderTextColor="#94A3B8"
                value={hoTen}
                onChangeText={setHoTen}
              />

              {/* Giới tính */}
              <Text style={styles.inputLabel}>Giới tính</Text>
              <View style={styles.genderRow}>
                <TouchableOpacity
                  style={[styles.genderBtn, gioiTinh === "Nam" && styles.genderBtnActive]}
                  onPress={() => setGioiTinh("Nam")}
                  activeOpacity={0.8}
                >
                  <Ionicons name="male" size={16} color={gioiTinh === "Nam" ? "#FFF8F2" : "#D97706"} />
                  <Text style={[styles.genderBtnText, gioiTinh === "Nam" && styles.genderBtnTextActive]}>Nam</Text>
                </TouchableOpacity>
                
                <TouchableOpacity
                  style={[styles.genderBtn, gioiTinh === "Nữ" && styles.genderBtnActive]}
                  onPress={() => setGioiTinh("Nữ")}
                  activeOpacity={0.8}
                >
                  <Ionicons name="female" size={16} color={gioiTinh === "Nữ" ? "#FFF8F2" : "#D97706"} />
                  <Text style={[styles.genderBtnText, gioiTinh === "Nữ" && styles.genderBtnTextActive]}>Nữ</Text>
                </TouchableOpacity>
              </View>

              {/* Ngày sinh */}
              <Text style={styles.inputLabel}>Ngày sinh (YYYY-MM-DD)</Text>
              <TextInput
                style={styles.textInput}
                placeholder="VD: 1995-10-24"
                placeholderTextColor="#94A3B8"
                value={ngaySinh}
                onChangeText={setNgaySinh}
              />

              {/* Trạng thái sinh tử */}
              <Text style={styles.inputLabel}>Trạng thái hiện tại</Text>
              <View style={styles.genderRow}>
                <TouchableOpacity
                  style={[styles.trangThaiBtn, trangThai === "Còn sống" && styles.trangThaiBtnActive]}
                  onPress={() => setTrangThai("Còn sống")}
                  activeOpacity={0.8}
                >
                  <Text style={[styles.trangThaiBtnText, trangThai === "Còn sống" && styles.trangThaiBtnTextActive]}>Còn sống</Text>
                </TouchableOpacity>

                <TouchableOpacity
                  style={[styles.trangThaiBtn, trangThai === "Đã mất" && styles.trangThaiBtnActive]}
                  onPress={() => setTrangThai("Đã mất")}
                  activeOpacity={0.8}
                >
                  <Text style={[styles.trangThaiBtnText, trangThai === "Đã mất" && styles.trangThaiBtnTextActive]}>Đã mất</Text>
                </TouchableOpacity>
              </View>

              {/* Nếu đã mất, hiển thị ngày mất và vị trí mộ phần */}
              {trangThai === "Đã mất" && (
                <>
                  <Text style={styles.inputLabel}>Ngày mất (YYYY-MM-DD)</Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="VD: 2024-05-15"
                    placeholderTextColor="#94A3B8"
                    value={ngayMat}
                    onChangeText={setNgayMat}
                  />

                  {proposalType === "edit" && (
                    <>
                      <Text style={styles.inputLabel}>Vị trí mộ phần</Text>
                      <TextInput
                        style={styles.textInput}
                        placeholder="Địa chỉ khu mộ, hàng mộ..."
                        placeholderTextColor="#94A3B8"
                        value={diaChiMo}
                        onChangeText={setDiaChiMo}
                      />
                    </>
                  )}
                </>
              )}

              {/* Nghề nghiệp */}
              <Text style={styles.inputLabel}>Nghề nghiệp</Text>
              <TextInput
                style={styles.textInput}
                placeholder="Nhập nghề nghiệp..."
                placeholderTextColor="#94A3B8"
                value={ngheNghiep}
                onChangeText={setNgheNghiep}
              />

              {/* Các trường nâng cao khi đính chính */}
              {proposalType === "edit" && (
                <>
                  <Text style={styles.inputLabel}>Số điện thoại</Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="Nhập số điện thoại liên lạc..."
                    placeholderTextColor="#94A3B8"
                    value={sdt}
                    onChangeText={setSdt}
                    keyboardType="phone-pad"
                  />

                  <Text style={styles.inputLabel}>Địa chỉ Email</Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="Nhập hòm thư điện tử..."
                    placeholderTextColor="#94A3B8"
                    value={email}
                    onChangeText={setEmail}
                    keyboardType="email-address"
                    autoCapitalize="none"
                  />

                  <Text style={styles.inputLabel}>Địa chỉ thường trú</Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="Địa chỉ cư trú..."
                    placeholderTextColor="#94A3B8"
                    value={diaChi}
                    onChangeText={setDiaChi}
                  />
                </>
              )}

              {/* Tiểu sử */}
              <Text style={styles.inputLabel}>Tóm tắt tiểu sử / Ghi chú</Text>
              <TextInput
                style={[styles.textInput, { height: 90, textAlignVertical: "top" }]}
                placeholder="Thông tin thêm về công trạng, tiểu sử cá nhân tổ tiên..."
                placeholderTextColor="#94A3B8"
                multiline
                numberOfLines={4}
                value={tieuSu}
                onChangeText={setTieuSu}
              />

              <TouchableOpacity
                style={styles.submitBtn}
                onPress={handleSubmit}
                disabled={submitting}
                activeOpacity={0.85}
              >
                {submitting ? (
                  <ActivityIndicator size="small" color="#0c0e12" />
                ) : (
                  <>
                    <Text style={styles.submitBtnText}>GỬI ĐỀ XUẤT CHO TRƯỞNG CHI NHÁNH</Text>
                    <Ionicons name="send" size={16} color="#0c0e12" style={{ marginLeft: 8 }} />
                  </>
                )}
              </TouchableOpacity>
            </View>
          )}
        </ScrollView>
      ) : (
        /* LỊCH SỬ ĐỀ XUẤT */
        <FlatList
          data={myProposals}
          keyExtractor={(item) => item.id.toString()}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={{ paddingHorizontal: 24, paddingTop: 16, paddingBottom: 40 }}
          refreshing={historyLoading}
          onRefresh={loadHistory}
          ListEmptyComponent={
            historyLoading ? (
              <ActivityIndicator size="large" color="#FF9F43" style={{ marginTop: 40 }} />
            ) : (
              <View style={styles.emptyHistory}>
                <Ionicons name="folder-open-outline" size={48} color="#94A3B8" />
                <Text style={styles.emptyHistoryText}>Bạn chưa gửi đề xuất chỉnh sửa nào.</Text>
              </View>
            )
          }
          renderItem={({ item }) => {
            const status = getStatusStyle(item.status);
            const proposedInfo = item.data || {};
            
            return (
              <View style={styles.historyCard}>
                <View style={styles.historyHeader}>
                  <View style={[styles.statusBadge, { backgroundColor: status.bg, borderColor: status.border }]}>
                    <Text style={[styles.statusBadgeText, { color: status.text }]}>{status.label}</Text>
                  </View>
                  <Text style={styles.historyDate}>
                    {new Date(item.created_at).toLocaleDateString("vi-VN")}
                  </Text>
                </View>

                <View style={styles.historyBody}>
                  <Text style={styles.historyType}>
                    📦 {getProposalTypeLabel(item.type)}
                  </Text>

                  <Text style={styles.historyTarget}>
                    Mốc liên quan: <Text style={{ fontWeight: "700" }}>{item.thanh_vien?.ho_ten || `ID: ${item.thanh_vien_id}`}</Text>
                  </Text>

                  <View style={styles.historyDataBox}>
                    <Text style={styles.historyDataTitle}>Thông tin đề nghị:</Text>
                    <Text style={styles.historyDataText}>
                      • Họ tên: {proposedInfo.ho_ten} {"\n"}
                      • Giới tính: {proposedInfo.gioi_tinh} {"\n"}
                      {proposedInfo.ngay_sinh && `• Ngày sinh: ${proposedInfo.ngay_sinh} \n`}
                      • Trạng thái: {proposedInfo.trang_thai}
                    </Text>
                  </View>

                  {item.note && (
                    <View style={styles.historyFeedback}>
                      <Text style={styles.feedbackTitle}>Phản hồi trưởng tộc:</Text>
                      <Text style={styles.feedbackText}>{item.note}</Text>
                    </View>
                  )}
                </View>
              </View>
            );
          }}
        />
      )}

      {/* DROPDOWN SELECTOR MODAL */}
      <Modal
        visible={showMemberSelector}
        animationType="slide"
        transparent
        onRequestClose={() => setShowMemberSelector(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalIndicator} />

            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>CHỌN THÀNH VIÊN GIA TỘC</Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowMemberSelector(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            <View style={styles.searchContainer}>
              <Ionicons name="search-outline" size={20} color="#FF9F43" style={styles.searchIcon} />
              <TextInput
                style={styles.searchInput}
                placeholder="Nhập tên thành viên cần tìm..."
                placeholderTextColor="#94A3B8"
                value={searchQuery}
                onChangeText={setSearchQuery}
                autoCorrect={false}
              />
              {searchQuery.length > 0 && (
                <TouchableOpacity onPress={() => setSearchQuery("")} style={{ padding: 4 }}>
                  <Ionicons name="close-circle" size={18} color="#94A3B8" />
                </TouchableOpacity>
              )}
            </View>

            <FlatList
              data={filteredMembers}
              keyExtractor={(item) => item.id.toString()}
              showsVerticalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 30 }}
              ListEmptyComponent={
                <View style={styles.emptySearchContainer}>
                  <Ionicons name="sad-outline" size={40} color="#94A3B8" />
                  <Text style={styles.emptySearchText}>Không tìm thấy thành viên phù hợp</Text>
                </View>
              }
              renderItem={({ item }) => (
                <TouchableOpacity
                  style={styles.memberItem}
                  onPress={() => handleSelectMember(item)}
                  activeOpacity={0.7}
                >
                  <Image
                    source={{
                      uri: item.avatar || "https://avatar.iran.liara.run/public",
                    }}
                    style={styles.memberAvatar}
                  />
                  <View style={styles.memberInfo}>
                    <Text style={styles.memberName}>{item.ho_ten}</Text>
                    <Text style={styles.memberSub}>
                      Mã: #{item.id} • {item.gioi_tinh} • {item.nghe_nghiep || "Không nghề"}
                    </Text>
                  </View>
                  <Ionicons name="chevron-forward" size={18} color="#FF9F43" />
                </TouchableOpacity>
              )}
            />
          </View>
        </View>
      </Modal>
    </View>
  );
}

const styles: Record<string, any> = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2",
  },
  loading: {
    flex: 1,
    backgroundColor: "#FFF8F2",
    justifyContent: "center",
    alignItems: "center",
  },
  loadingText: {
    fontSize: 14,
    color: "#D97706",
    fontWeight: "700",
    marginTop: 12,
  },
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    paddingHorizontal: 24,
    paddingBottom: 40,
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

  // HEADER BANNER
  header: {
    paddingTop: Platform.OS === "ios" ? 65 : 45,
    paddingBottom: 18,
    paddingHorizontal: 24,
  },
  headerSubtitle: {
    color: "#FF9F43",
    fontSize: 11,
    fontWeight: "900",
    letterSpacing: 2,
    marginBottom: 6,
  },
  headerTitle: {
    fontSize: 25,
    fontWeight: "900",
    color: "#0F172A",
    marginBottom: 8,
  },
  headerDesc: {
    fontSize: 12.5,
    color: "#475569",
    lineHeight: 19,
    fontWeight: "500",
  },

  // TAB SWITCHER
  tabContainer: {
    flexDirection: "row",
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    marginHorizontal: 24,
    padding: 6,
    borderRadius: 16,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.12)",
    marginBottom: 16,
  },
  tabButton: {
    flex: 1,
    flexDirection: "row",
    paddingVertical: 12,
    justifyContent: "center",
    alignItems: "center",
    borderRadius: 12,
  },
  tabButtonActive: {
    backgroundColor: "#FF9F43",
  },
  tabText: {
    fontSize: 13.5,
    fontWeight: "800",
    color: "#64748B",
  },
  tabTextActive: {
    color: "#FFF8F2",
  },

  // FORM STYLING
  sectionLabel: {
    fontSize: 12.5,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1,
    textTransform: "uppercase",
    marginTop: 18,
    marginBottom: 10,
  },
  typeSelector: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 14,
  },
  typeOption: {
    width: "31%",
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 12,
    paddingVertical: 12,
  },
  typeOptionActive: {
    backgroundColor: "#FF9F43",
    borderColor: "#FF9F43",
  },
  typeOptionText: {
    fontSize: 11,
    fontWeight: "800",
    color: "#D97706",
    marginLeft: 4,
  },
  typeOptionTextActive: {
    color: "#0c0e12",
  },

  selectorTrigger: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    borderRadius: 16,
    paddingHorizontal: 16,
    paddingVertical: 12,
    marginBottom: 10,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.02,
    shadowRadius: 6,
    elevation: 2,
  },
  selectorLeft: {
    flexDirection: "row",
    alignItems: "center",
    flex: 1,
  },
  avatarMini: {
    width: 44,
    height: 44,
    borderRadius: 22,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.3)",
    marginRight: 12,
    backgroundColor: "rgba(255, 159, 67, 0.05)",
  },
  selectorValueText: {
    fontSize: 15,
    fontWeight: "800",
    color: "#1E293B",
  },
  selectorPlaceholder: {
    color: "#94A3B8",
    fontWeight: "600",
  },
  selectorValueSub: {
    fontSize: 11.5,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },

  formContainer: {
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 24,
    padding: 20,
    marginTop: 12,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.03,
    shadowRadius: 10,
    elevation: 2,
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: "800",
    color: "#475569",
    marginTop: 14,
    marginBottom: 6,
    textTransform: "uppercase",
    letterSpacing: 0.8,
  },
  textInput: {
    backgroundColor: "rgba(255, 159, 67, 0.02)",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    paddingHorizontal: 14,
    height: 48,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "600",
  },
  genderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  genderBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 12,
    paddingVertical: 12,
    marginHorizontal: 4,
  },
  genderBtnActive: {
    backgroundColor: "#D97706",
    borderColor: "#D97706",
  },
  genderBtnText: {
    fontSize: 13,
    fontWeight: "800",
    color: "#D97706",
    marginLeft: 6,
  },
  genderBtnTextActive: {
    color: "#FFF8F2",
  },

  trangThaiBtn: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 12,
    paddingVertical: 12,
    marginHorizontal: 4,
  },
  trangThaiBtnActive: {
    backgroundColor: "#FF5E36",
    borderColor: "#FF5E36",
  },
  trangThaiBtnText: {
    fontSize: 13,
    fontWeight: "800",
    color: "#FF5E36",
  },
  trangThaiBtnTextActive: {
    color: "#ffffff",
  },

  submitBtn: {
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
  submitBtnText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 13,
    letterSpacing: 0.8,
  },

  // HISTORY TAB STYLING
  emptyHistory: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 80,
  },
  emptyHistoryText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 12,
    textAlign: "center",
  },
  historyCard: {
    backgroundColor: "#ffffff",
    borderRadius: 22,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.1)",
    padding: 18,
    marginBottom: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.02,
    shadowRadius: 8,
    elevation: 2,
  },
  historyHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 14,
  },
  statusBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
    borderWidth: 1,
  },
  statusBadgeText: {
    fontSize: 10,
    fontWeight: "900",
    textTransform: "uppercase",
  },
  historyDate: {
    fontSize: 11,
    color: "#94A3B8",
    fontWeight: "600",
  },
  historyBody: {
    marginTop: 2,
  },
  historyType: {
    fontSize: 14.5,
    fontWeight: "900",
    color: "#1E293B",
  },
  historyTarget: {
    fontSize: 12.5,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 6,
  },
  historyDataBox: {
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderRadius: 14,
    padding: 12,
    marginTop: 10,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.08)",
  },
  historyDataTitle: {
    fontSize: 11.5,
    fontWeight: "800",
    color: "#D97706",
    marginBottom: 4,
  },
  historyDataText: {
    fontSize: 12,
    lineHeight: 18,
    color: "#475569",
    fontWeight: "600",
  },
  historyFeedback: {
    borderTopWidth: 1,
    borderTopColor: "rgba(255, 159, 67, 0.15)",
    paddingTop: 10,
    marginTop: 12,
  },
  feedbackTitle: {
    fontSize: 11,
    fontWeight: "800",
    color: "#EF4444",
  },
  feedbackText: {
    fontSize: 12,
    color: "#475569",
    fontWeight: "600",
    marginTop: 2,
    lineHeight: 17,
  },

  // MODAL OVERLAYS
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.4)",
    justifyContent: "flex-end",
  },
  modalContent: {
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    height: "80%",
    paddingHorizontal: 24,
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
    marginBottom: 18,
  },
  modalTitle: {
    fontSize: 13,
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

  searchContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    borderRadius: 16,
    paddingHorizontal: 14,
    height: 52,
    marginBottom: 16,
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    color: "#1E293B",
    fontSize: 14.5,
    fontWeight: "700",
    padding: 0,
  },

  emptySearchContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 50,
  },
  emptySearchText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 10,
  },

  memberItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 14,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.06)",
  },
  memberAvatar: {
    width: 46,
    height: 46,
    borderRadius: 23,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.25)",
    marginRight: 14,
  },
  memberInfo: {
    flex: 1,
  },
  memberName: {
    fontSize: 14.5,
    fontWeight: "800",
    color: "#1E293B",
  },
  memberSub: {
    fontSize: 11.5,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  reloadHeaderBtn: {
    backgroundColor: "#ffffff",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    width: 38,
    height: 38,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
  },
});
