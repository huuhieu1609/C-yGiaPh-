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
  Dimensions,
  StatusBar,
  Platform,
  FlatList,
} from "react-native";
import { Ionicons, MaterialCommunityIcons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

const { width } = Dimensions.get("window");

export default function TuongNiemScreen() {
  const [upcomingAnniversaries, setUpcomingAnniversaries] = useState<any[]>([]);
  const [deceasedMembers, setDeceasedMembers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  
  // Trạng thái cho Modal Chi Tiết Tưởng Niệm
  const [selectedDeceased, setSelectedDeceased] = useState<any | null>(null);
  const [showDetailModal, setShowDetailModal] = useState(false);
  const [detailLoading, setDetailLoading] = useState(false);
  const [tributeStats, setTributeStats] = useState({ Nhang: 0, Hoa: 0, Nen: 0, TraiCay: 0 });
  const [tributeList, setTributeList] = useState<any[]>([]);

  // Trạng thái cho Form Dâng Lễ
  const [loaiLeVat, setLoaiLeVat] = useState<"Nhang" | "Hoa" | "Nen" | "TraiCay">("Nhang");
  const [loiNhan, setLoiNhan] = useState("");
  const [submitting, setSubmitting] = useState(false);

  // Tìm kiếm người đã khuất
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    loadData();
  }, []);

  const loadData = async () => {
    try {
      setLoading(true);
      // 1. Tải ngày giỗ sắp tới
      const upcomingRes = await api.get("/tuong-niem/sap-toi");
      if (upcomingRes.data.status && upcomingRes.data.data) {
        setUpcomingAnniversaries(upcomingRes.data.data);
      }

      // 2. Tải toàn bộ thành viên để lọc người đã khuất
      const membersRes = await api.get("/thanh-vien/get-data");
      let allMembers = [];
      if (Array.isArray(membersRes.data)) {
        allMembers = membersRes.data;
      } else if (membersRes.data && Array.isArray(membersRes.data.data)) {
        allMembers = membersRes.data.data;
      }
      
      const deceased = allMembers.filter((m: any) => m.trang_thai === "Đã mất");
      setDeceasedMembers(deceased);
    } catch (err: any) {
      console.log("Lỗi tải dữ liệu tưởng niệm:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể tải danh sách ngày giỗ & tưởng niệm.");
    } finally {
      setLoading(false);
    }
  };

  const loadTributes = async (memberId: number) => {
    try {
      setDetailLoading(true);
      const tributeRes = await api.get(`/tuong-niem/thanh-vien/${memberId}`);
      if (tributeRes.data.status) {
        setTributeStats(tributeRes.data.stats || { Nhang: 0, Hoa: 0, Nen: 0, TraiCay: 0 });
        setTributeList(tributeRes.data.tributes || []);
      }
    } catch (err: any) {
      console.log("Lỗi tải lịch sử tri ân:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể tải lịch sử dâng lễ của thành viên này.");
    } finally {
      setDetailLoading(false);
    }
  };

  const handleSelectDeceased = (member: any) => {
    setSelectedDeceased(member);
    setShowDetailModal(true);
    setLoiNhan("");
    setLoaiLeVat("Nhang");
    loadTributes(member.id);
  };

  const submitTribute = async () => {
    if (!selectedDeceased) return;
    try {
      setSubmitting(true);
      const payload = {
        thanh_vien_id: selectedDeceased.id,
        loai_le_vat: loaiLeVat,
        loi_nhan: loiNhan.trim() || null,
      };

      const res = await api.post("/tuong-niem/create", payload);
      if (res.data.status) {
        Alert.alert("Tấm Lòng Thành", "Dâng lễ kính lên tổ tiên thành công!");
        setLoiNhan("");
        // Reload tributes list
        loadTributes(selectedDeceased.id);
      } else {
        Alert.alert("Lỗi", res.data.message || "Gửi tri ân thất bại.");
      }
    } catch (err: any) {
      console.log("Lỗi gửi tri ân:", err.response?.data || err.message);
      Alert.alert("Lỗi", err.response?.data?.message || "Không thể dâng lễ.");
    } finally {
      setSubmitting(false);
    }
  };

  const filteredDeceased = deceasedMembers.filter((m) =>
    m.ho_ten?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  const getLeVatIcon = (type: string) => {
    switch (type) {
      case "Nhang":
        return "fire";
      case "Hoa":
        return "flower-outline";
      case "Nen":
        return "candle";
      case "TraiCay":
        return "food-apple-outline";
      default:
        return "heart";
    }
  };

  const getLeVatName = (type: string) => {
    switch (type) {
      case "Nhang":
        return "Nén Hương";
      case "Hoa":
        return "Đóa Hoa";
      case "Nen":
        return "Cây Nến";
      case "TraiCay":
        return "Mâm Quả";
      default:
        return type;
    }
  };

  const formatNgayMat = (dateStr: string) => {
    if (!dateStr) return "Chưa rõ";
    try {
      const d = new Date(dateStr);
      return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
    } catch (e) {
      return dateStr;
    }
  };

  if (loading) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#FF9F43" />
        <Text style={styles.loadingText}>Đang tải trang kính nhớ tổ tiên...</Text>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <View style={[styles.glowSphere, styles.glowAmber, { top: -60, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -80, right: -60 }]} />

      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
        showsVerticalScrollIndicator={false}
      >
        {/* BANNER TƯỞNG NIỆM */}
        <View style={styles.header}>
          <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "flex-start" }}>
            <View style={{ flex: 1, marginRight: 10 }}>
              <Text style={styles.headerSubtitle}>TẤM LÒNG THÀNH KÍNH</Text>
              <Text style={styles.headerTitle}>Tưởng Niệm & Giỗ Chạp</Text>
            </View>
            <TouchableOpacity
              style={styles.reloadHeaderBtn}
              onPress={loadData}
              activeOpacity={0.7}
            >
              <Ionicons name="refresh-outline" size={20} color="#D97706" />
            </TouchableOpacity>
          </View>
          <Text style={styles.headerDesc}>
            "Cây có cội mới trổ cành xanh lá, nước có nguồn mới bể cả sông sâu." Con cháu nhớ ơn, kính dâng hương hoa tổ tiên.
          </Text>
        </View>

        {/* 📅 DANH SÁCH GIỖ SẮP TỚI */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>📅 NGÀY KỴ NHẬT SẮP DIỄN RA (45 ngày)</Text>
          <View style={styles.sectionLine} />
        </View>

        {upcomingAnniversaries.length === 0 ? (
          <View style={styles.emptyCard}>
            <Ionicons name="calendar-clear-outline" size={32} color="#D97706" style={{ marginBottom: 8 }} />
            <Text style={styles.emptyText}>Hiện chưa có ngày giỗ tổ tiên nào sắp tới.</Text>
          </View>
        ) : (
          <ScrollView
            horizontal
            showsHorizontalScrollIndicator={false}
            contentContainerStyle={styles.horizontalScroll}
          >
            {upcomingAnniversaries.map((ann) => {
              const daysLeft = ann.days_remaining;
              const isToday = daysLeft === 0;

              return (
                <TouchableOpacity
                  key={ann.id}
                  style={[
                    styles.annCard,
                    isToday && styles.annCardToday
                  ]}
                  activeOpacity={0.8}
                  onPress={() => handleSelectDeceased(ann)}
                >
                  <View style={styles.annBadgeContainer}>
                    <View style={[styles.annBadge, isToday ? styles.annBadgeToday : styles.annBadgeNormal]}>
                      <Text style={styles.annBadgeText}>
                        {isToday ? "Hôm nay Giỗ" : `Còn ${daysLeft} ngày`}
                      </Text>
                    </View>
                  </View>

                  <Image
                    source={{
                      uri: ann.avatar || "https://avatar.iran.liara.run/public",
                    }}
                    style={[styles.annAvatar, isToday && styles.annAvatarToday]}
                  />

                  <Text style={styles.annName} numberOfLines={1}>
                    {ann.ho_ten}
                  </Text>
                  
                  <Text style={styles.annTime}>
                    Giỗ lần thứ {ann.years_mat}
                  </Text>

                  <Text style={styles.annDate}>
                    Ngày kỵ: {formatNgayMat(ann.ngay_mat)}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </ScrollView>
        )}

        {/* 🔍 TÌM KIẾM & THẮP HƯƠNG TẬN TÂM */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>🕯️ THẮP HƯƠNG TẬN TÂM</Text>
          <View style={styles.sectionLine} />
        </View>

        {/* Khung tìm kiếm */}
        <View style={styles.searchContainer}>
          <Ionicons name="search-outline" size={20} color="#FF9F43" style={styles.searchIcon} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm kiếm vong linh tổ tiên đã mất..."
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

        {/* Danh sách thành viên đã khuất */}
        {filteredDeceased.length === 0 ? (
          <View style={styles.emptyCard}>
            <Ionicons name="people-outline" size={32} color="#D97706" style={{ marginBottom: 8 }} />
            <Text style={styles.emptyText}>Không tìm thấy thành viên đã mất nào.</Text>
          </View>
        ) : (
          <View style={styles.deceasedList}>
            {filteredDeceased.map((item) => (
              <TouchableOpacity
                key={item.id}
                style={styles.deceasedItem}
                activeOpacity={0.8}
                onPress={() => handleSelectDeceased(item)}
              >
                <Image
                  source={{
                    uri: item.avatar || "https://avatar.iran.liara.run/public",
                  }}
                  style={styles.deceasedAvatar}
                />
                <View style={styles.deceasedInfo}>
                  <Text style={styles.deceasedName}>{item.ho_ten}</Text>
                  <Text style={styles.deceasedDates}>
                    Sinh ngày: {formatNgayMat(item.ngay_sinh)} • Mất ngày: {formatNgayMat(item.ngay_mat)}
                  </Text>
                  {item.dia_chi_mo && (
                    <Text style={styles.deceasedMo}>
                      📍 Mộ phần: {item.dia_chi_mo}
                    </Text>
                  )}
                </View>
                <View style={styles.danhLeButton}>
                  <Ionicons name="heart" size={16} color="#FF5E36" style={{ marginRight: 4 }} />
                  <Text style={styles.danhLeButtonText}>Dâng lễ</Text>
                </View>
              </TouchableOpacity>
            ))}
          </View>
        )}
      </ScrollView>

      {/* 🏛️ MODAL CHI TIẾT TƯỞNG NIỆM & GỬI LỄ VẬT */}
      <Modal
        visible={showDetailModal}
        animationType="slide"
        transparent
        onRequestClose={() => setShowDetailModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalIndicator} />

            {/* Header Modal */}
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>KÍNH NHỚ GIA TIÊN</Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowDetailModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            {selectedDeceased && (
              <FlatList
                data={tributeList}
                keyExtractor={(item) => item.id.toString()}
                showsVerticalScrollIndicator={false}
                ListHeaderComponent={
                  <View style={styles.modalTopSection}>
                    {/* Ảnh thờ Vong Linh */}
                    <View style={styles.vongLinhFrame}>
                      <Image
                        source={{
                          uri: selectedDeceased.avatar || "https://avatar.iran.liara.run/public",
                        }}
                        style={styles.vongLinhAvatar}
                      />
                    </View>
                    <Text style={styles.vongLinhName}>{selectedDeceased.ho_ten}</Text>
                    <Text style={styles.vongLinhDates}>
                      Kỵ nhật: {formatNgayMat(selectedDeceased.ngay_mat)}
                    </Text>

                    {/* Thống kê Lễ Vật */}
                    <Text style={styles.blockTitle}>🕯️ LỄ VẬT CON CHÁU KÍNH DÂNG</Text>
                    {detailLoading ? (
                      <ActivityIndicator size="small" color="#FF9F43" style={{ marginVertical: 15 }} />
                    ) : (
                      <View style={styles.statsContainer}>
                        <View style={styles.statPill}>
                          <MaterialCommunityIcons name="fire" size={22} color="#FF9F43" />
                          <Text style={styles.statCount}>{tributeStats.Nhang} Hương</Text>
                        </View>
                        <View style={styles.statPill}>
                          <Ionicons name="flower-outline" size={22} color="#EF4444" />
                          <Text style={styles.statCount}>{tributeStats.Hoa} Hoa</Text>
                        </View>
                        <View style={styles.statPill}>
                          <MaterialCommunityIcons name="candle" size={22} color="#F59E0B" />
                          <Text style={styles.statCount}>{tributeStats.Nen} Nến</Text>
                        </View>
                        <View style={styles.statPill}>
                          <MaterialCommunityIcons name="food-apple-outline" size={22} color="#10B981" />
                          <Text style={styles.statCount}>{tributeStats.TraiCay} Quả</Text>
                        </View>
                      </View>
                    )}

                    {/* Form dâng lễ mới */}
                    <View style={styles.tributeForm}>
                      <Text style={styles.formTitle}>✨ THÀNH KÍNH DÂNG LỄ VÀ GỬI LỜI TRI ÂN</Text>
                      
                      {/* Chọn loại lễ vật */}
                      <View style={styles.leVatSelector}>
                        {(["Nhang", "Hoa", "Nen", "TraiCay"] as const).map((type) => {
                          const active = loaiLeVat === type;
                          return (
                            <TouchableOpacity
                              key={type}
                              style={[styles.leVatOption, active && styles.leVatOptionActive]}
                              onPress={() => setLoaiLeVat(type)}
                              activeOpacity={0.8}
                            >
                              <MaterialCommunityIcons
                                name={getLeVatIcon(type) as any}
                                size={22}
                                color={active ? "#FFF8F2" : "#D97706"}
                              />
                              <Text style={[styles.leVatOptionText, active && styles.leVatOptionTextActive]}>
                                {getLeVatName(type)}
                              </Text>
                            </TouchableOpacity>
                          );
                        })}
                      </View>

                      {/* Viết lời nhắn */}
                      <TextInput
                        style={styles.tributeInput}
                        placeholder="Hãy gửi gắm lời kính cẩn cầu nguyện hoặc câu chuyện kỷ niệm tưởng nhớ tổ tiên..."
                        placeholderTextColor="#94A3B8"
                        multiline
                        numberOfLines={3}
                        value={loiNhan}
                        onChangeText={setLoiNhan}
                      />

                      <TouchableOpacity
                        style={styles.submitBtn}
                        onPress={submitTribute}
                        disabled={submitting}
                        activeOpacity={0.85}
                      >
                        {submitting ? (
                          <ActivityIndicator size="small" color="#0c0e12" />
                        ) : (
                          <>
                            <Text style={styles.submitBtnText}>DÂNG LỄ KHẤN NGUYỆN</Text>
                            <Ionicons name="heart-sharp" size={16} color="#0c0e12" style={{ marginLeft: 6 }} />
                          </>
                        )}
                      </TouchableOpacity>
                    </View>

                    {/* Danh sách 50 lời nhắn */}
                    <Text style={styles.blockTitle}>📝 TÂM NGUYỆN CỦA CON CHÁU</Text>
                  </View>
                }
                ListEmptyComponent={
                  <View style={styles.emptyTributes}>
                    <Text style={styles.emptyTributesText}>Chưa có lời nhắn nào được gửi gắm.</Text>
                  </View>
                }
                renderItem={({ item }) => (
                  <View style={styles.tributeCard}>
                    <View style={styles.tributeHeader}>
                      <Text style={styles.tributeSender}>{item.ho_ten_nguoi_gui || "Con cháu gia tộc"}</Text>
                      <View style={styles.tributeLeVatTag}>
                        <MaterialCommunityIcons
                          name={getLeVatIcon(item.loai_le_vat) as any}
                          size={12}
                          color="#D97706"
                          style={{ marginRight: 4 }}
                        />
                        <Text style={styles.tributeLeVatText}>{getLeVatName(item.loai_le_vat)}</Text>
                      </View>
                    </View>
                    {item.loi_nhan && <Text style={styles.tributeContent}>{item.loi_nhan}</Text>}
                    <Text style={styles.tributeTime}>
                      {new Date(item.created_at).toLocaleString("vi-VN")}
                    </Text>
                  </View>
                )}
                contentContainerStyle={{ paddingBottom: 50 }}
              />
            )}
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
    padding: 20,
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
    paddingBottom: 24,
    paddingHorizontal: 24,
    backgroundColor: "rgba(255, 159, 67, 0.05)",
    borderBottomWidth: 1.5,
    borderBottomColor: "rgba(255, 159, 67, 0.12)",
  },
  headerSubtitle: {
    color: "#FF9F43",
    fontSize: 11,
    fontWeight: "900",
    letterSpacing: 2,
    marginBottom: 6,
  },
  headerTitle: {
    fontSize: 26,
    fontWeight: "900",
    color: "#0F172A",
    marginBottom: 8,
  },
  headerDesc: {
    fontSize: 13,
    color: "#475569",
    lineHeight: 20,
    fontWeight: "500",
  },

  // TIÊU ĐỀ SECTION
  sectionHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 24,
    marginTop: 26,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 11.5,
    fontWeight: "900",
    color: "#475569",
    letterSpacing: 1.5,
    marginRight: 10,
  },
  sectionLine: {
    flex: 1,
    height: 1,
    backgroundColor: "rgba(255, 159, 67, 0.15)",
  },

  // HORIZONTAL GIỖ SẮP TỚI
  horizontalScroll: {
    paddingLeft: 24,
    paddingRight: 12,
  },
  annCard: {
    backgroundColor: "#ffffff",
    borderRadius: 22,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    padding: 16,
    width: 170,
    marginRight: 16,
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  annCardToday: {
    borderColor: "#FF5E36",
    backgroundColor: "#FFF5F2",
  },
  annBadgeContainer: {
    width: "100%",
    alignItems: "flex-end",
    marginBottom: 8,
  },
  annBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  annBadgeNormal: {
    backgroundColor: "rgba(255, 159, 67, 0.1)",
  },
  annBadgeToday: {
    backgroundColor: "#FF5E36",
  },
  annBadgeText: {
    fontSize: 9.5,
    fontWeight: "900",
    color: "#D97706",
  },
  annAvatar: {
    width: 60,
    height: 60,
    borderRadius: 30,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.25)",
    marginBottom: 10,
    backgroundColor: "#ffffff",
  },
  annAvatarToday: {
    borderColor: "#FF5E36",
  },
  annName: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
    marginBottom: 4,
    width: "100%",
    textAlign: "center",
  },
  annTime: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "600",
    marginBottom: 2,
  },
  annDate: {
    fontSize: 10,
    color: "#D97706",
    fontWeight: "700",
  },

  emptyCard: {
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 20,
    padding: 24,
    marginHorizontal: 24,
    alignItems: "center",
  },
  emptyText: {
    color: "#64748B",
    fontSize: 13,
    fontWeight: "500",
    textAlign: "center",
  },

  // TÌM KIẾM
  searchContainer: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 18,
    paddingHorizontal: 14,
    height: 52,
    marginHorizontal: 24,
    marginBottom: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.02,
    shadowRadius: 6,
    elevation: 2,
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

  // DANH SÁCH NGƯỜI ĐÃ KHUẤT
  deceasedList: {
    marginHorizontal: 24,
  },
  deceasedItem: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.08)",
    borderRadius: 20,
    padding: 14,
    marginBottom: 12,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.02,
    shadowRadius: 8,
    elevation: 2,
  },
  deceasedAvatar: {
    width: 52,
    height: 52,
    borderRadius: 26,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    marginRight: 12,
    backgroundColor: "#F3F4F6",
  },
  deceasedInfo: {
    flex: 1,
  },
  deceasedName: {
    fontSize: 14.5,
    fontWeight: "800",
    color: "#1E293B",
  },
  deceasedDates: {
    fontSize: 11.5,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  deceasedMo: {
    fontSize: 10.5,
    color: "#D97706",
    fontWeight: "700",
    marginTop: 3,
  },
  danhLeButton: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 94, 54, 0.08)",
    borderWidth: 1,
    borderColor: "rgba(255, 94, 54, 0.2)",
    borderRadius: 12,
    paddingHorizontal: 10,
    paddingVertical: 6,
  },
  danhLeButtonText: {
    fontSize: 11,
    color: "#FF5E36",
    fontWeight: "900",
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
    height: "90%",
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
    marginBottom: 14,
  },
  modalTitle: {
    fontSize: 13,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.5,
  },
  closeBtn: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    justifyContent: "center",
    alignItems: "center",
  },

  modalTopSection: {
    alignItems: "center",
    paddingTop: 10,
    marginBottom: 16,
  },
  vongLinhFrame: {
    width: 94,
    height: 94,
    borderRadius: 47,
    borderWidth: 3,
    borderColor: "#D97706",
    padding: 2,
    backgroundColor: "#ffffff",
    shadowColor: "#D97706",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
    elevation: 4,
  },
  vongLinhAvatar: {
    width: "100%",
    height: "100%",
    borderRadius: 45,
  },
  vongLinhName: {
    fontSize: 18,
    fontWeight: "900",
    color: "#1E293B",
    marginTop: 12,
  },
  vongLinhDates: {
    fontSize: 12.5,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  blockTitle: {
    fontSize: 11,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.2,
    marginTop: 24,
    marginBottom: 12,
    alignSelf: "flex-start",
    width: "100%",
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.15)",
    paddingBottom: 6,
  },

  // THỐNG KÊ LỄ VẬT
  statsContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
    width: "100%",
    marginBottom: 6,
  },
  statPill: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 159, 67, 0.05)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 14,
    paddingHorizontal: 12,
    paddingVertical: 10,
    width: "48%",
    marginBottom: 10,
  },
  statCount: {
    fontSize: 13,
    fontWeight: "800",
    color: "#1E293B",
    marginLeft: 8,
  },

  // FORM DÂNG LỄ
  tributeForm: {
    width: "100%",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 20,
    padding: 16,
    marginTop: 10,
  },
  formTitle: {
    fontSize: 10.5,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 0.8,
    marginBottom: 12,
  },
  leVatSelector: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 14,
  },
  leVatOption: {
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 12,
    paddingVertical: 8,
    width: "23%",
    justifyContent: "center",
  },
  leVatOptionActive: {
    backgroundColor: "#D97706",
    borderColor: "#D97706",
  },
  leVatOptionText: {
    fontSize: 9,
    fontWeight: "800",
    color: "#D97706",
    marginTop: 4,
  },
  leVatOptionTextActive: {
    color: "#FFF8F2",
  },
  tributeInput: {
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.2)",
    borderRadius: 14,
    paddingHorizontal: 14,
    paddingVertical: 10,
    height: 72,
    textAlignVertical: "top",
    color: "#1E293B",
    fontSize: 13.5,
    fontWeight: "600",
    marginBottom: 14,
  },
  submitBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 3,
  },
  submitBtnText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 13,
    letterSpacing: 0.8,
  },

  // DANH SÁCH LỜI NHẮN
  emptyTributes: {
    paddingVertical: 24,
    alignItems: "center",
  },
  emptyTributesText: {
    color: "#94A3B8",
    fontSize: 13,
    fontWeight: "600",
  },
  tributeCard: {
    backgroundColor: "rgba(255, 159, 67, 0.02)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.08)",
    borderRadius: 16,
    padding: 12,
    marginBottom: 10,
  },
  tributeHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 6,
  },
  tributeSender: {
    fontSize: 13,
    fontWeight: "800",
    color: "#1E293B",
  },
  tributeLeVatTag: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 159, 67, 0.08)",
    borderRadius: 6,
    paddingHorizontal: 6,
    paddingVertical: 2,
  },
  tributeLeVatText: {
    fontSize: 8.5,
    fontWeight: "800",
    color: "#D97706",
  },
  tributeContent: {
    fontSize: 12.5,
    color: "#475569",
    fontWeight: "600",
    lineHeight: 18,
    marginBottom: 6,
  },
  tributeTime: {
    fontSize: 9.5,
    color: "#94A3B8",
    fontWeight: "600",
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
    marginTop: 8,
  },
});
