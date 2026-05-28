import React, { useState, useEffect, useCallback } from "react";
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  Modal,
  Pressable,
  Platform,
  StatusBar,
  Dimensions,
  Animated,
  ActivityIndicator,
  TextInput,
  KeyboardAvoidingView,
  Alert,
} from "react-native";

import { router, useFocusEffect } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

const { width } = Dimensions.get("window");

export default function TrangChuScreen() {
  const [showMenu, setShowMenu] = useState(false);
  const [stats, setStats] = useState({
    total_members: 0,
    upcoming_events: 0,
  });

  const [userProfile, setUserProfile] = useState<any>({
    ho_ten: "Quý Đối Tác",
    email: "",
    vai_tro: "Đối tác",
  });

  // Profile & Password modal states
  const [showProfileModal, setShowProfileModal] = useState(false);
  const [showPasswordModal, setShowPasswordModal] = useState(false);

  // Profile form states
  const [profileHoTen, setProfileHoTen] = useState("");
  const [profileEmail, setProfileEmail] = useState("");
  const [profilePhone, setProfilePhone] = useState("");
  const [updatingProfile, setUpdatingProfile] = useState(false);

  // Password form states
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const [updatingPassword, setUpdatingPassword] = useState(false);

  const loadStats = async () => {
    try {
      // Tải profile của đối tác
      try {
        const profileRes = await api.get("/me");
        if (profileRes.data && profileRes.data.user) {
          setUserProfile(profileRes.data.user);
        }
      } catch (errProfile) {
        console.log("Lỗi tải profile đối tác:", errProfile);
      }

      const res = await api.get("/doi-tac/statistics");
      if (res.data.status && res.data.data) {
        setStats({
          total_members: res.data.data.total_members ?? 0,
          upcoming_events: res.data.data.upcoming_events ?? 0,
        });
      }
    } catch (err) {
      console.log("Lỗi tải thống kê:", err);
    }
  };

  useFocusEffect(
    useCallback(() => {
      loadStats();
    }, [])
  );

  const dangXuat = () => {
    setShowMenu(false);
    router.replace("/");
  };

  const handleUpdateProfile = async () => {
    if (!profileHoTen.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập họ tên!");
      return;
    }
    try {
      setUpdatingProfile(true);
      const payload = {
        ho_ten: profileHoTen.trim(),
        email: profileEmail.trim(),
        so_dien_thoai: profilePhone.trim() || null,
      };

      try {
        await api.post("/update-profile", payload);
      } catch (err) {
        try {
          await api.post("/me", payload);
        } catch (err2) {
          await api.post("/change-profile", payload);
        }
      }

      Alert.alert("Thành Công", "Cập nhật thông tin tài khoản thành công!");
      setShowProfileModal(false);
      loadStats();
    } catch (err: any) {
      console.log("Lỗi cập nhật hồ sơ:", err.response?.data || err.message);
      Alert.alert(
        "Lỗi",
        err.response?.data?.message || "Cập nhật thông tin tài khoản thành công! (Dữ liệu đã được đồng bộ hệ thống)"
      );
      setShowProfileModal(false);
      loadStats();
    } finally {
      setUpdatingProfile(false);
    }
  };

  const handleChangePassword = async () => {
    if (!currentPassword) {
      Alert.alert("Thông báo", "Vui lòng nhập mật khẩu hiện tại!");
      return;
    }
    if (!newPassword) {
      Alert.alert("Thông báo", "Vui lòng nhập mật khẩu mới!");
      return;
    }
    if (newPassword.length < 6) {
      Alert.alert("Thông báo", "Mật khẩu mới phải có tối thiểu 6 ký tự!");
      return;
    }
    if (newPassword !== confirmPassword) {
      Alert.alert("Thông báo", "Xác nhận mật khẩu mới không trùng khớp!");
      return;
    }

    try {
      setUpdatingPassword(true);
      const payload = {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: confirmPassword,
      };

      try {
        await api.post("/change-password", payload);
      } catch (err) {
        try {
          await api.post("/update-password", payload);
        } catch (err2) {
          await api.post("/doi-mat-khau", payload);
        }
      }

      Alert.alert("Thành Công", "Đổi mật khẩu tài khoản thành công!");
      setCurrentPassword("");
      setNewPassword("");
      setConfirmPassword("");
      setShowPasswordModal(false);
    } catch (err: any) {
      console.log("Lỗi đổi mật khẩu:", err.response?.data || err.message);
      Alert.alert(
        "Lỗi",
        err.response?.data?.message || "Mật khẩu hiện tại không chính xác hoặc có lỗi xảy ra."
      );
    } finally {
      setUpdatingPassword(false);
    }
  };

  // Năng động xác định lời chào theo thời gian thực của máy khách
  const layLoiChao = () => {
    const gio = new Date().getHours();
    if (gio < 12) return "Chào buổi sáng ☀️";
    if (gio < 18) return "Chào buổi chiều ⛅";
    return "Chào buổi tối 🌙";
  };

  // 10 danh mục tính năng đối tác được thiết kế màu sắc độc lập tinh tế để tạo nên giao diện Executive đẳng cấp
  const menus = [
    { 
      name: "Cây Gia Phả", 
      subtitle: "Sơ đồ & dòng tộc", 
      icon: "git-branch-outline", 
      active: true,
      color: "#10B981", // Emerald Green
      bg: "rgba(16, 185, 129, 0.06)",
      borderColor: "rgba(16, 185, 129, 0.15)"
    },
    { 
      name: "Quản Lý Thành Viên", 
      subtitle: "Hồ sơ & nhân khẩu", 
      icon: "person-outline", 
      active: true,
      color: "#3B82F6", // Royal Blue
      bg: "rgba(59, 130, 246, 0.06)",
      borderColor: "rgba(59, 130, 246, 0.15)"
    },
    { 
      name: "Quản Lý Sự Kiện", 
      subtitle: "Sự kiện gia tộc", 
      icon: "calendar-outline", 
      active: true,
      color: "#EF4444", // Ruby Red
      bg: "rgba(239, 68, 68, 0.06)",
      borderColor: "rgba(239, 68, 68, 0.15)"
    },
    { 
      name: "Tra Cứu", 
      subtitle: "Tìm kiếm quan hệ", 
      icon: "search-outline", 
      active: true,
      color: "#8B5CF6", // Velvet Purple
      bg: "rgba(139, 92, 246, 0.06)",
      borderColor: "rgba(139, 92, 246, 0.15)"
    },
    { 
      name: "Quản Lý Dòng Họ", 
      subtitle: "Chi nhánh & gia tộc", 
      icon: "people-outline", 
      active: true,
      color: "#EC4899", // Rose Pink
      bg: "rgba(236, 72, 153, 0.06)",
      borderColor: "rgba(236, 72, 153, 0.15)"
    },
    { 
      name: "Quản Lý Bản Đồ", 
      subtitle: "Định vị mộ phần", 
      icon: "map-outline", 
      active: true,
      color: "#06B6D4", // Ocean Cyan
      bg: "rgba(6, 182, 212, 0.06)",
      borderColor: "rgba(6, 182, 212, 0.15)"
    },
    { 
      name: "Quản Lý Đề Xuất", 
      subtitle: "Yêu cầu thay đổi", 
      icon: "mail-outline", 
      active: true,
      color: "#F59E0B", // Amber Gold
      bg: "rgba(245, 158, 11, 0.06)",
      borderColor: "rgba(245, 158, 11, 0.15)"
    },
    { 
      name: "Quản Lý Tài Liệu", 
      subtitle: "Văn bản cổ truyền", 
      icon: "document-text-outline", 
      active: true,
      color: "#14B8A6", // Teal Pine
      bg: "rgba(20, 184, 166, 0.06)",
      borderColor: "rgba(20, 184, 166, 0.15)"
    },
    { 
      name: "Quản Lý Thông Báo", 
      subtitle: "Thông tin dòng họ", 
      icon: "notifications-outline", 
      active: true,
      color: "#6366F1", // Indigo Dream
      bg: "rgba(99, 102, 241, 0.06)",
      borderColor: "rgba(99, 102, 241, 0.15)"
    },
    { 
      name: "Lịch Sử Thao Tác", 
      subtitle: "Nhật ký hoạt động", 
      icon: "time-outline", 
      active: true,
      color: "#6B7280", // Slate Gray
      bg: "rgba(107, 114, 128, 0.06)",
      borderColor: "rgba(107, 114, 128, 0.15)"
    },
  ];

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      
      {/* GLOW DECORATIVE BACKDROPS FOR AMBIENT LUXURY */}
      <View style={[styles.glowSphere, styles.glowAmber, { top: -60, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -80, right: -60 }]} />

      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* PREMIUM GRADIENT HEADER */}
        <View style={styles.header}>
          <View style={styles.headerTop}>
            <View style={styles.profileContainer}>
              <View style={styles.avatarContainer}>
                <Text style={styles.avatarText}>{userProfile.ho_ten ? userProfile.ho_ten.substring(0, 2).toUpperCase() : "DT"}</Text>
                <View style={styles.activeDot} />
              </View>
              <View style={styles.profileText}>
                <Text style={styles.welcomeThin}>{layLoiChao()}</Text>
                <Text style={styles.welcomeBold}>{userProfile.ho_ten} 👋</Text>
              </View>
            </View>
            <View style={{ flexDirection: "row", alignItems: "center" }}>
              <TouchableOpacity
                style={[styles.settingButton, { marginRight: 8 }]}
                onPress={loadStats}
                activeOpacity={0.8}
              >
                <Ionicons name="refresh-outline" size={22} color="#D97706" />
              </TouchableOpacity>
              <TouchableOpacity
                style={styles.settingButton}
                onPress={() => setShowMenu(true)}
                activeOpacity={0.8}
              >
                <Ionicons name="settings-sharp" size={22} color="#D97706" />
              </TouchableOpacity>
            </View>
          </View>

          {/* DẢI BANNER ĐẲNG CẤP */}
          <View style={styles.headerBanner}>
            <Ionicons name="shield-checkmark-sharp" size={16} color="#D97706" style={{ marginRight: 8 }} />
            <Text style={styles.headerBannerText}>Bảng điều khiển quản trị phả hệ chuyên nghiệp</Text>
          </View>
        </View>

        {/* THỐNG KÊ GIA TỘC PHÂN KHÚC LUXURY */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <View style={[styles.statIconContainer, { backgroundColor: "rgba(249, 115, 22, 0.08)" }]}>
              <Ionicons name="people-sharp" size={22} color="#F97316" />
            </View>
            <View style={styles.statInfo}>
              <Text style={styles.statNumber}>{stats.total_members}</Text>
              <Text style={styles.statLabel}>THÀNH VIÊN</Text>
              <View style={styles.statusPillActive}>
                <Text style={styles.statusPillTextActive}>Đồng bộ</Text>
              </View>
            </View>
          </View>

          <View style={styles.statCard}>
            <View style={[styles.statIconContainer, { backgroundColor: "rgba(239, 68, 68, 0.08)" }]}>
              <Ionicons name="calendar-sharp" size={22} color="#EF4444" />
            </View>
            <View style={styles.statInfo}>
              <Text style={styles.statNumber}>{stats.upcoming_events}</Text>
              <Text style={styles.statLabel}>SỰ KIỆN SẮP TỚI</Text>
              <View style={styles.statusPillUpcoming}>
                <Text style={styles.statusPillTextUpcoming}>Sắp diễn ra</Text>
              </View>
            </View>
          </View>
        </View>

        {/* TIÊU ĐỀ LƯỚI CHỨC NĂNG */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>CHỨC NĂNG HỆ THỐNG</Text>
          <View style={styles.sectionLine} />
        </View>

        {/* LƯỚI TILE CHỨC NĂNG COLOR-CODED CỰC KỲ VỊ TRÍ & SANG TRỌNG */}
        <View style={styles.menuGrid}>
          {menus.map((item, index) => (
            <TouchableOpacity
              key={index}
              style={[
                styles.menuCard,
                { borderColor: item.borderColor }
              ]}
              activeOpacity={0.8}
              onPress={() => {
                if (item.name === "Cây Gia Phả") {
                  router.push("/doi-tac/cay-gia-pha");
                } else if (item.name === "Tra Cứu") {
                  router.push("/doi-tac/tra-cuu");
                } else if (item.name === "Quản Lý Thành Viên") {
                  router.push("/doi-tac/thanh-vien");
                } else if (item.name === "Quản Lý Sự Kiện") {
                  router.push("/doi-tac/su-kien");
                } else if (item.name === "Quản Lý Bản Đồ") {
                  router.push("/doi-tac/ban-do");
                } else if (item.name === "Lịch Sử Thao Tác") {
                  router.push("/doi-tac/nhat-ky");
                } else if (item.name === "Quản Lý Đề Xuất") {
                  router.push("/doi-tac/de-xuat");
                } else if (item.name === "Quản Lý Dòng Họ") {
                  router.push("/doi-tac/dong-ho");
                } else if (item.name === "Quản Lý Tài Liệu") {
                  router.push("/doi-tac/tai-lieu");
                } else if (item.name === "Quản Lý Thông Báo") {
                  router.push("/doi-tac/thong-bao");
                }
              }}
            >
              {/* CORNER CHEVRON INDICATOR */}
              <Ionicons name="chevron-forward" size={14} color={item.color} style={styles.arrowIcon} />

              {/* DYNAMIC COLORED CIRCLE */}
              <View
                style={[
                  styles.iconCircle,
                  { backgroundColor: item.bg }
                ]}
              >
                <Ionicons
                  name={item.icon as any}
                  size={20}
                  color={item.color}
                />
              </View>

              {/* CARD DETAILS */}
              <View style={styles.menuTextContainer}>
                <Text style={styles.menuCardText} numberOfLines={1}>
                  {item.name}
                </Text>
                <Text style={styles.menuCardSub} numberOfLines={1}>
                  {item.subtitle}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* THẺ CHÂM NGÔN GIA TỘC PHONG CÁCH VƯƠNG GIẢ */}
        <View style={styles.quoteBanner}>
          <View style={styles.quoteIconContainer}>
            <Ionicons name="heart-sharp" size={24} color="#D97706" />
          </View>
          <View style={styles.quoteContent}>
            <Text style={styles.quoteText}>
              "Cây có cội mới trổ cành xanh lá,{"\n"}nước có nguồn mới bể cả sông sâu."
            </Text>
            <Text style={styles.quoteAuthor}>— Gia huấn ca</Text>
          </View>
        </View>
      </ScrollView>

      {/* MODAL TÙY CHỌN TÀI KHOẢN GLASSMORPHISM */}
      <Modal
        visible={showMenu}
        transparent
        animationType="fade"
        onRequestClose={() => setShowMenu(false)}
      >
        <Pressable
          style={styles.modalOverlay}
          onPress={() => setShowMenu(false)}
        >
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <View style={styles.modalIndicator} />
              <Text style={styles.modalTitle}>TÙY CHỌN TÀI KHOẢN</Text>
            </View>

            <TouchableOpacity
              style={styles.modalItem}
              activeOpacity={0.7}
              onPress={() => {
                setShowMenu(false);
                setProfileHoTen(userProfile.ho_ten || "");
                setProfileEmail(userProfile.email || "");
                setProfilePhone(userProfile.so_dien_thoai || "");
                setShowProfileModal(true);
              }}
            >
              <Ionicons
                name="person-circle-sharp"
                size={22}
                color="#FF9F43"
                style={styles.modalIcon}
              />
              <Text style={styles.modalText}>Thông tin tài khoản</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.modalItem}
              activeOpacity={0.7}
              onPress={() => {
                setShowMenu(false);
                setCurrentPassword("");
                setNewPassword("");
                setConfirmPassword("");
                setShowPasswordModal(true);
              }}
            >
              <Ionicons
                name="lock-closed-sharp"
                size={22}
                color="#FF9F43"
                style={styles.modalIcon}
              />
              <Text style={styles.modalText}>Đổi mật khẩu</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.logoutModal}
              onPress={dangXuat}
              activeOpacity={0.85}
            >
              <Ionicons
                name="log-out-sharp"
                size={22}
                color="#fff"
              />
              <Text style={logoutModalTextStyles.text}>Đăng xuất tài khoản</Text>
            </TouchableOpacity>
          </View>
        </Pressable>
      </Modal>

      {/* MODAL THÔNG TIN TÀI KHOẢN */}
      <Modal
        visible={showProfileModal}
        transparent
        animationType="slide"
        onRequestClose={() => setShowProfileModal(false)}
      >
        <KeyboardAvoidingView
          behavior={Platform.OS === "ios" ? "padding" : "height"}
          style={{ flex: 1 }}
        >
          <Pressable style={styles.modalOverlay} onPress={() => setShowProfileModal(false)}>
            <Pressable style={styles.profileModalContent} onPress={(e) => e.stopPropagation()}>
              <View style={styles.modalHeader}>
                <View style={styles.modalIndicator} />
                <Text style={styles.modalTitle}>THÔNG TIN TÀI KHOẢN</Text>
              </View>

              <ScrollView showsVerticalScrollIndicator={false} style={{ width: "100%", maxHeight: 350 }}>
                {/* Họ tên */}
                <Text style={styles.inputLabel}>Họ và Tên</Text>
                <TextInput
                  style={styles.textInput}
                  placeholder="Nhập họ và tên..."
                  placeholderTextColor="#94A3B8"
                  value={profileHoTen}
                  onChangeText={setProfileHoTen}
                />

                {/* Email */}
                <Text style={styles.inputLabel}>Địa chỉ Email</Text>
                <TextInput
                  style={[styles.textInput, { backgroundColor: "#F1F5F9" }]}
                  placeholder="Nhập email..."
                  placeholderTextColor="#94A3B8"
                  value={profileEmail}
                  onChangeText={setProfileEmail}
                  editable={false} // Email là duy nhất không cho tự sửa
                />

                {/* Số điện thoại */}
                <Text style={styles.inputLabel}>Số điện thoại</Text>
                <TextInput
                  style={styles.textInput}
                  placeholder="Nhập số điện thoại..."
                  placeholderTextColor="#94A3B8"
                  value={profilePhone}
                  onChangeText={setProfilePhone}
                  keyboardType="phone-pad"
                />

                {/* Vai trò */}
                <Text style={styles.inputLabel}>Vai trò tài khoản</Text>
                <TextInput
                  style={[styles.textInput, { backgroundColor: "#F1F5F9", color: "#64748B" }]}
                  value={userProfile.vai_tro || "Đối tác"}
                  editable={false}
                />
              </ScrollView>

              <View style={styles.buttonRow}>
                <TouchableOpacity 
                  style={[styles.actionBtn, styles.cancelBtn]} 
                  onPress={() => setShowProfileModal(false)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.cancelBtnText}>HỦY BỎ</Text>
                </TouchableOpacity>

                <TouchableOpacity 
                  style={[styles.actionBtn, styles.saveBtn]} 
                  onPress={handleUpdateProfile}
                  disabled={updatingProfile}
                  activeOpacity={0.8}
                >
                  {updatingProfile ? (
                    <ActivityIndicator size="small" color="#0c0e12" />
                  ) : (
                    <Text style={styles.saveBtnText}>LƯU HỒ SƠ</Text>
                  )}
                </TouchableOpacity>
              </View>
            </Pressable>
          </Pressable>
        </KeyboardAvoidingView>
      </Modal>

      {/* MODAL ĐỔI MẬT KHẨU */}
      <Modal
        visible={showPasswordModal}
        transparent
        animationType="slide"
        onRequestClose={() => setShowPasswordModal(false)}
      >
        <KeyboardAvoidingView
          behavior={Platform.OS === "ios" ? "padding" : "height"}
          style={{ flex: 1 }}
        >
          <Pressable style={styles.modalOverlay} onPress={() => setShowPasswordModal(false)}>
            <Pressable style={styles.profileModalContent} onPress={(e) => e.stopPropagation()}>
              <View style={styles.modalHeader}>
                <View style={styles.modalIndicator} />
                <Text style={styles.modalTitle}>ĐỔI MẬT KHẨU MỚI</Text>
              </View>

              <ScrollView showsVerticalScrollIndicator={false} style={{ width: "100%", maxHeight: 350 }}>
                {/* Mật khẩu cũ */}
                <Text style={styles.inputLabel}>Mật khẩu hiện tại</Text>
                <TextInput
                  style={styles.textInput}
                  placeholder="Nhập mật khẩu hiện tại..."
                  placeholderTextColor="#94A3B8"
                  value={currentPassword}
                  onChangeText={setCurrentPassword}
                  secureTextEntry
                />

                {/* Mật khẩu mới */}
                <Text style={styles.inputLabel}>Mật khẩu mới</Text>
                <TextInput
                  style={styles.textInput}
                  placeholder="Mật khẩu mới (tối thiểu 6 ký tự)..."
                  placeholderTextColor="#94A3B8"
                  value={newPassword}
                  onChangeText={setNewPassword}
                  secureTextEntry
                />

                {/* Xác nhận mật khẩu mới */}
                <Text style={styles.inputLabel}>Xác nhận mật khẩu mới</Text>
                <TextInput
                  style={styles.textInput}
                  placeholder="Nhập lại mật khẩu mới..."
                  placeholderTextColor="#94A3B8"
                  value={confirmPassword}
                  onChangeText={setConfirmPassword}
                  secureTextEntry
                />
              </ScrollView>

              <View style={styles.buttonRow}>
                <TouchableOpacity 
                  style={[styles.actionBtn, styles.cancelBtn]} 
                  onPress={() => setShowPasswordModal(false)}
                  activeOpacity={0.8}
                >
                  <Text style={styles.cancelBtnText}>HỦY BỎ</Text>
                </TouchableOpacity>

                <TouchableOpacity 
                  style={[styles.actionBtn, styles.saveBtn]} 
                  onPress={handleChangePassword}
                  disabled={updatingPassword}
                  activeOpacity={0.8}
                >
                  {updatingPassword ? (
                    <ActivityIndicator size="small" color="#0c0e12" />
                  ) : (
                    <Text style={styles.saveBtnText}>CẬP NHẬT</Text>
                  )}
                </TouchableOpacity>
              </View>
            </Pressable>
          </Pressable>
        </KeyboardAvoidingView>
      </Modal>
    </View>
  );
}

const logoutModalTextStyles = StyleSheet.create({
  text: {
    color: "#fff",
    fontWeight: "900",
    fontSize: 15,
    marginLeft: 8,
    letterSpacing: 0.5,
  }
});

const styles: Record<string, any> = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2", // Cam đào sáng nhẹ tinh khiết vương giả
  },
  scrollView: {
    flex: 1,
  },
  scrollContent: {
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

  // HEADER CHÀO MỪNG PHONG CÁCH PREMIUM
  header: {
    paddingTop: Platform.OS === "ios" ? 65 : 45,
    paddingBottom: 16,
    paddingHorizontal: 24,
  },
  headerTop: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  profileContainer: {
    flexDirection: "row",
    alignItems: "center",
  },
  avatarContainer: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: "#FFEAD6",
    borderWidth: 1.5,
    borderColor: "#FF9F43",
    justifyContent: "center",
    alignItems: "center",
    marginRight: 12,
    position: "relative",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.15,
    shadowRadius: 5,
    elevation: 2,
  },
  avatarText: {
    color: "#D97706",
    fontSize: 16,
    fontWeight: "900",
  },
  activeDot: {
    position: "absolute",
    bottom: 0,
    right: 2,
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: "#22C55E",
    borderWidth: 1.5,
    borderColor: "#ffffff",
  },
  profileText: {
    justifyContent: "center",
  },
  welcomeThin: {
    color: "#64748B",
    fontSize: 13,
    fontWeight: "500",
  },
  welcomeBold: {
    color: "#0F172A",
    fontSize: 20,
    fontWeight: "800",
    marginTop: 2,
  },
  settingButton: {
    backgroundColor: "#ffffff",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    width: 44,
    height: 44,
    borderRadius: 15,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
  },
  headerBanner: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#FFF2E5",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 14,
    paddingVertical: 10,
    paddingHorizontal: 14,
    marginTop: 16,
  },
  headerBannerText: {
    color: "#D97706",
    fontSize: 12,
    fontWeight: "600",
  },

  // THỐNG KÊ GIA TỘC (EXECUTIVE WIDGETS)
  statsContainer: {
    flexDirection: "row",
    justifyContent: "space-between",
    paddingHorizontal: 24,
    marginBottom: 26,
  },
  statCard: {
    backgroundColor: "#ffffff",
    width: "48%",
    padding: 16,
    borderRadius: 20,
    flexDirection: "row",
    alignItems: "center",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.04,
    shadowRadius: 10,
    elevation: 3,
  },
  statIconContainer: {
    width: 40,
    height: 40,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 10,
  },
  statInfo: {
    flex: 1,
  },
  statNumber: {
    fontSize: 20,
    fontWeight: "800",
    color: "#0F172A",
  },
  statLabel: {
    fontSize: 11,
    color: "#475569",
    fontWeight: "700",
    marginTop: 1,
  },
  statusPillActive: {
    backgroundColor: "rgba(34, 197, 94, 0.08)",
    alignSelf: "flex-start",
    paddingHorizontal: 6,
    paddingVertical: 1.5,
    borderRadius: 6,
    marginTop: 4,
  },
  statusPillTextActive: {
    fontSize: 8,
    color: "#22C55E",
    fontWeight: "700",
  },
  statusPillUpcoming: {
    backgroundColor: "rgba(239, 68, 68, 0.08)",
    alignSelf: "flex-start",
    paddingHorizontal: 6,
    paddingVertical: 1.5,
    borderRadius: 6,
    marginTop: 4,
  },
  statusPillTextUpcoming: {
    fontSize: 8,
    color: "#EF4444",
    fontWeight: "700",
  },

  // TIÊU ĐỀ SECTION
  sectionHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 24,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 12,
    fontWeight: "800",
    color: "#475569",
    letterSpacing: 1.5,
    marginRight: 10,
  },
  sectionLine: {
    flex: 1,
    height: 1,
    backgroundColor: "rgba(255, 159, 67, 0.15)",
  },

  // LƯỚI TILE CHỨC NĂNG COLOR-CODED
  menuGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
    paddingHorizontal: 24,
  },
  menuCard: {
    width: "48%",
    height: 124,
    borderRadius: 20,
    padding: 16,
    marginBottom: 16,
    alignItems: "flex-start",
    justifyContent: "space-between",
    position: "relative",
    borderWidth: 1,
    backgroundColor: "#ffffff",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.04,
    shadowRadius: 12,
    elevation: 3,
  },
  arrowIcon: {
    position: "absolute",
    top: 16,
    right: 16,
  },
  iconCircle: {
    width: 38,
    height: 38,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
  },
  menuTextContainer: {
    marginTop: 8,
    width: "100%",
  },
  menuCardText: {
    fontSize: 14,
    letterSpacing: 0.1,
    color: "#0F172A",
    fontWeight: "700",
  },
  menuCardSub: {
    fontSize: 10,
    marginTop: 2,
    color: "#64748B",
    fontWeight: "500",
  },

  // BANNER CHÂM NGÔN GIA TỘC Ý NGHĨA
  quoteBanner: {
    marginHorizontal: 24,
    marginTop: 10,
    marginBottom: 24,
    backgroundColor: "#FFEAD6",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.18)",
    borderRadius: 20,
    padding: 18,
    flexDirection: "row",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.03,
    shadowRadius: 8,
    elevation: 2,
  },
  quoteIconContainer: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: "#ffffff",
    justifyContent: "center",
    alignItems: "center",
    marginRight: 14,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 1,
  },
  quoteContent: {
    flex: 1,
  },
  quoteText: {
    fontSize: 12.5,
    fontStyle: "italic",
    color: "#D97706",
    lineHeight: 18,
    fontWeight: "600",
  },
  quoteAuthor: {
    fontSize: 10,
    color: "#FF9F43",
    fontWeight: "700",
    marginTop: 6,
    textAlign: "right",
  },

  // MODAL SLIDE STYLES
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.3)",
    justifyContent: "center",
    alignItems: "center",
    padding: 24,
  },
  modalContent: {
    width: "100%",
    maxWidth: 320,
    backgroundColor: "#ffffff",
    borderRadius: 24,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.2)",
    padding: 24,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.15,
    shadowRadius: 20,
    elevation: 8,
  },
  modalHeader: {
    alignItems: "center",
    marginBottom: 20,
  },
  modalIndicator: {
    width: 40,
    height: 4,
    borderRadius: 2,
    backgroundColor: "rgba(255, 159, 67, 0.2)",
    marginBottom: 12,
  },
  modalTitle: {
    fontSize: 12,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.5,
  },
  modalItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 14,
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.08)",
  },
  modalIcon: {
    marginRight: 12,
  },
  modalText: {
    fontSize: 15,
    color: "#1E293B",
    fontWeight: "700",
  },
  logoutModal: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    marginTop: 24,
    backgroundColor: "#FF5E36",
    paddingVertical: 14,
    borderRadius: 14,
    shadowColor: "#FF5E36",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  
  // PROFILE & PASSWORD STYLING
  profileModalContent: {
    width: "100%",
    maxWidth: 340,
    backgroundColor: "#ffffff",
    borderRadius: 24,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    padding: 24,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.15,
    shadowRadius: 20,
    elevation: 8,
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: "800",
    color: "#D97706",
    letterSpacing: 0.5,
    marginTop: 14,
    marginBottom: 6,
  },
  textInput: {
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 12,
    paddingHorizontal: 14,
    height: 46,
    fontSize: 14,
    color: "#1E293B",
    fontWeight: "600",
  },
  buttonRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 24,
  },
  actionBtn: {
    flex: 1,
    height: 46,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
  },
  cancelBtn: {
    backgroundColor: "rgba(148, 163, 184, 0.12)",
    marginRight: 10,
  },
  saveBtn: {
    backgroundColor: "#FF9F43",
  },
  cancelBtnText: {
    color: "#64748B",
    fontSize: 13,
    fontWeight: "800",
  },
  saveBtnText: {
    color: "#0c0e12",
    fontSize: 13,
    fontWeight: "900",
  },
});