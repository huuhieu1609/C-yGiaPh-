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
} from "react-native";

import { router, useFocusEffect } from "expo-router";
import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function TrangChuScreen() {
  const [showMenu, setShowMenu] = useState(false);
  const [stats, setStats] = useState({
    total_members: 0,
    upcoming_events: 0,
  });

  const loadStats = async () => {
    try {
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

  // Các chức năng có trạng thái hoạt động (đã được làm) kèm mô tả phụ cực kỳ chuyên nghiệp
  const menus = [
    { name: "Cây Gia Phả", subtitle: "Sơ đồ & dòng tộc", icon: "git-branch-outline", active: true },
    { name: "Lịch Sử Thao Tác", subtitle: "Nhật ký hoạt động", icon: "time-outline", active: true },
    { name: "Quản Lý Bản Đồ", subtitle: "Định vị mộ phần", icon: "map-outline", active: true },
    { name: "Quản Lý Đề Xuất", subtitle: "Yêu cầu thay đổi", icon: "mail-outline", active: true },
    { name: "Quản Lý Dòng Họ", subtitle: "Chi nhánh & gia tộc", icon: "people-outline", active: true },
    { name: "Quản Lý Sự Kiện", subtitle: "Sự kiện gia tộc", icon: "calendar-outline", active: true },
    { name: "Quản Lý Tài Liệu", subtitle: "Văn bản cổ truyền", icon: "document-text-outline", active: true },
    { name: "Quản Lý Thành Viên", subtitle: "Hồ sơ & nhân khẩu", icon: "person-outline", active: true },
    { name: "Quản Lý Thông Báo", subtitle: "Thông tin dòng họ", icon: "notifications-outline", active: true },
    { name: "Tra Cứu", subtitle: "Tìm kiếm quan hệ", icon: "search-outline", active: true },
  ];

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      {/* ĐỐM SÁNG GOLD MỜ TRANG TRÍ HSL SANG TRỌNG */}
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* HEADER CHÀO MỪNG PHONG CÁCH PREMIUM */}
        <View style={styles.header}>
          <View style={styles.headerTop}>
            <View style={styles.profileContainer}>
              <View style={styles.avatarContainer}>
                <Text style={styles.avatarText}>DT</Text>
                <View style={styles.activeDot} />
              </View>
              <View style={styles.profileText}>
                <Text style={styles.welcomeThin}>Xin chào,</Text>
                <Text style={styles.welcomeBold}>Quý Đối Tác 👋</Text>
              </View>
            </View>
            <TouchableOpacity
              style={styles.settingButton}
              onPress={() => setShowMenu(true)}
              activeOpacity={0.8}
            >
              <Ionicons name="settings-outline" size={22} color="#D97706" />
            </TouchableOpacity>
          </View>

          {/* DẢI BANNER PHỤ ĐẰNG CẤP */}
          <View style={styles.headerBanner}>
            <Ionicons name="shield-checkmark" size={15} color="#D97706" style={{ marginRight: 6 }} />
            <Text style={styles.headerBannerText}>Hệ thống quản lý phả hệ dòng tộc chuyên nghiệp</Text>
          </View>
        </View>

        {/* THỐNG KÊ GIA TỘC PHONG CÁCH EXECUTIVE WIDGETS */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <View style={[styles.statIconContainer, { backgroundColor: "rgba(255, 159, 67, 0.08)" }]}>
              <Ionicons name="people" size={22} color="#FF9F43" />
            </View>
            <View style={styles.statInfo}>
              <Text style={styles.statNumber}>{stats.total_members}</Text>
              <Text style={styles.statLabel}>Thành viên</Text>
              <Text style={styles.statTrend}>Đang hoạt động</Text>
            </View>
          </View>

          <View style={styles.statCard}>
            <View style={[styles.statIconContainer, { backgroundColor: "rgba(239, 68, 68, 0.08)" }]}>
              <Ionicons name="calendar" size={22} color="#EF4444" />
            </View>
            <View style={styles.statInfo}>
              <Text style={styles.statNumber}>{stats.upcoming_events}</Text>
              <Text style={styles.statLabel}>Sự kiện</Text>
              <Text style={styles.statTrend}>Sắp diễn ra</Text>
            </View>
          </View>
        </View>

        {/* TIÊU ĐỀ KHU VỰC CHỨC NĂNG */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>CHỨC NĂNG QUẢN TRỊ</Text>
          <View style={styles.sectionLine} />
        </View>

        {/* LƯỚI TILE CHỨC NĂNG ĐƠN LỚP SIÊU SẠCH SẼ & CAO CẤP */}
        <View style={styles.menuGrid}>
          {menus.map((item, index) => (
            <TouchableOpacity
              key={index}
              style={[
                styles.menuCard,
                item.active ? styles.menuCardActive : styles.menuCardMuted,
              ]}
              activeOpacity={item.active ? 0.85 : 1}
              disabled={!item.active}
              onPress={() => {
                if (!item.active) return;
                
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
              {/* CHỈ HƯỚNG BẰNG CHEVRON HOẶC BADGE "SẮP CÓ" GỌN GÀNG */}
              {item.active ? (
                <Ionicons name="chevron-forward-outline" size={16} color="rgba(217, 119, 6, 0.4)" style={styles.arrowIcon} />
              ) : (
                <View style={styles.comingSoonBadge}>
                  <Text style={styles.comingSoonText}>Sắp có</Text>
                </View>
              )}

              {/* BIỂU TƯỢNG PASTEL */}
              <View
                style={[
                  styles.iconCircle,
                  item.active ? styles.iconCircleActive : styles.iconCircleMuted,
                ]}
              >
                <Ionicons
                  name={item.icon as any}
                  size={20}
                  color={item.active ? "#D97706" : "#94A3B8"}
                />
              </View>

              {/* NỘI DUNG CHỮ CĂN TRÁI PHÂN CẤP RÕ RÀNG */}
              <View style={styles.menuTextContainer}>
                <Text
                  style={[
                    styles.menuCardText,
                    item.active ? styles.menuCardTextActive : styles.menuCardTextMuted,
                  ]}
                  numberOfLines={1}
                >
                  {item.name}
                </Text>
                <Text
                  style={[
                    styles.menuCardSub,
                    item.active ? styles.menuCardSubActive : styles.menuCardSubMuted,
                  ]}
                  numberOfLines={1}
                >
                  {item.active ? item.subtitle : "Sắp ra mắt"}
                </Text>
              </View>
            </TouchableOpacity>
          ))}
        </View>

        {/* BANNER CHÂM NGÔN GIA TỘC Ý NGHĨA VĂN HÓA */}
        <View style={styles.quoteBanner}>
          <View style={styles.quoteIconContainer}>
            <Ionicons name="heart" size={24} color="#D97706" />
          </View>
          <View style={styles.quoteContent}>
            <Text style={styles.quoteText}>
              "Cây có cội mới trổ cành xanh lá,{"\n"}nước có nguồn mới bể cả sông sâu."
            </Text>
            <Text style={styles.quoteAuthor}>— Gia huấn ca</Text>
          </View>
        </View>
      </ScrollView>

      {/* MODAL CÀI ĐẶT GLASSMORPHISM CHUYÊN NGHIỆP */}
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
            >
              <Ionicons
                name="person-circle-outline"
                size={22}
                color="#FF9F43"
                style={styles.modalIcon}
              />
              <Text style={styles.modalText}>Thông tin tài khoản</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={styles.modalItem}
              activeOpacity={0.7}
            >
              <Ionicons
                name="lock-closed-outline"
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
                name="log-out-outline"
                size={22}
                color="#fff"
              />
              <Text style={styles.logoutModalText}>Đăng xuất tài khoản</Text>
            </TouchableOpacity>
          </View>
        </Pressable>
      </Modal>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2", // Nền cam đào sáng nhẹ cực kỳ sang trọng
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
  statTrend: {
    fontSize: 9,
    color: "#64748B",
    fontWeight: "500",
    marginTop: 2,
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

  // LƯỚI TILE CHỨC NĂNG ĐƠN LỚP CAO CẤP
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
  },
  menuCardActive: {
    backgroundColor: "#ffffff",
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 12,
    elevation: 3,
  },
  menuCardMuted: {
    backgroundColor: "rgba(241, 245, 249, 0.5)",
    borderColor: "rgba(226, 232, 240, 0.6)",
    shadowOpacity: 0,
    elevation: 0,
  },
  arrowIcon: {
    position: "absolute",
    top: 16,
    right: 16,
  },
  comingSoonBadge: {
    position: "absolute",
    top: 14,
    right: 14,
    backgroundColor: "rgba(148, 163, 184, 0.1)",
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 6,
  },
  comingSoonText: {
    fontSize: 8,
    fontWeight: "700",
    color: "#64748B",
    textTransform: "uppercase",
  },
  iconCircle: {
    width: 38,
    height: 38,
    borderRadius: 12,
    justifyContent: "center",
    alignItems: "center",
  },
  iconCircleActive: {
    backgroundColor: "rgba(255, 159, 67, 0.08)",
  },
  iconCircleMuted: {
    backgroundColor: "rgba(148, 163, 184, 0.06)",
  },
  menuTextContainer: {
    marginTop: 8,
    width: "100%",
  },
  menuCardText: {
    fontSize: 14,
    letterSpacing: 0.1,
  },
  menuCardTextActive: {
    color: "#0F172A",
    fontWeight: "700",
  },
  menuCardTextMuted: {
    color: "#94A3B8",
    fontWeight: "600",
  },
  menuCardSub: {
    fontSize: 10,
    marginTop: 2,
  },
  menuCardSubActive: {
    color: "#64748B",
    fontWeight: "500",
  },
  menuCardSubMuted: {
    color: "#CBD5E1",
    fontWeight: "400",
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
  logoutModalText: {
    color: "#fff",
    fontWeight: "900",
    fontSize: 15,
    marginLeft: 8,
    letterSpacing: 0.5,
  },
});