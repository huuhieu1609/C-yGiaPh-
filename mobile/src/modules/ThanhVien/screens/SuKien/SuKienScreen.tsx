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
  Image,
  Dimensions,
  StatusBar,
  Platform,
  FlatList,
} from "react-native";
import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function SuKienScreen() {
  const [events, setEvents] = useState<any[]>([]);
  const [members, setMembers] = useState<any[]>([]);
  const [myMember, setMyMember] = useState<any | null>(null);
  const [loading, setLoading] = useState(true);
  
  // Trạng thái cho việc Đăng Ký họp họ
  const [selectedEvent, setSelectedEvent] = useState<any | null>(null);
  const [showRegisterModal, setShowRegisterModal] = useState(false);
  const [eventParticipants, setEventParticipants] = useState<any[]>([]);
  const [participantsLoading, setParticipantsLoading] = useState(false);
  
  // Danh sách ID thành viên gia đình được chọn dự sự kiện
  const [selectedAttendeeIds, setSelectedAttendeeIds] = useState<number[]>([]);
  const [registering, setRegistering] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const handleReload = async () => {
    await loadData();
  };

  const loadData = async () => {
    try {
      setLoading(true);
      // 1. Lấy thông tin user hiện tại để tìm member tương ứng
      const meRes = await api.get("/me");
      const user = meRes.data?.user;

      // 2. Lấy toàn bộ thành viên dòng tộc
      const membersRes = await api.get("/thanh-vien/get-data");
      let allMembers: any[] = [];
      if (Array.isArray(membersRes.data)) {
        allMembers = membersRes.data;
      } else if (membersRes.data && Array.isArray(membersRes.data.data)) {
        allMembers = membersRes.data.data;
      }
      setMembers(allMembers);

      // Tìm thành viên chính của account
      if (user && user.email) {
        const found = allMembers.find((m) => m.email === user.email);
        if (found) setMyMember(found);
      }

      // 3. Lấy danh sách sự kiện
      const eventsRes = await api.get("/su-kien/get-data");
      if (eventsRes.data.status) {
        setEvents(eventsRes.data.data || []);
      }
    } catch (err: any) {
      console.log("Lỗi tải trang sự kiện:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể tải danh sách sự kiện dòng họ.");
    } finally {
      setLoading(false);
    }
  };

  const loadParticipants = async (eventId: number) => {
    try {
      setParticipantsLoading(true);
      const res = await api.get(`/su-kien/get-participants/${eventId}`);
      if (res.data.status) {
        const list = res.data.data || [];
        setEventParticipants(list);
        
        // Tự động tích chọn những thành viên gia đình đã đăng ký trước đó
        const registeredIds = list.map((p: any) => p.id);
        setSelectedAttendeeIds(registeredIds);
      }
    } catch (err: any) {
      console.log("Lỗi tải người tham gia:", err.response?.data || err.message);
    } finally {
      setParticipantsLoading(false);
    }
  };

  const handleOpenRegister = (event: any) => {
    setSelectedEvent(event);
    setShowRegisterModal(true);
    setSelectedAttendeeIds([]);
    setEventParticipants([]);
    loadParticipants(event.id);
  };

  const toggleAttendee = (memberId: number) => {
    setSelectedAttendeeIds((prev) =>
      prev.includes(memberId)
        ? prev.filter((id) => id !== memberId)
        : [...prev, memberId]
    );
  };

  const handleSaveRegistration = async () => {
    if (!selectedEvent) return;
    try {
      setRegistering(true);

      // 1. Lọc ra những thành viên đã hủy đăng ký
      const previouslyRegisteredIds = eventParticipants.map((p) => p.id);
      const uncheckedIds = previouslyRegisteredIds.filter(
        (id) => !selectedAttendeeIds.includes(id)
      );

      // Hủy đăng ký những người bỏ tích
      for (const cancelId of uncheckedIds) {
        await api.post("/su-kien/unregister", {
          su_kien_id: selectedEvent.id,
          thanh_vien_id: cancelId,
        });
      }

      // 2. Đăng ký những thành viên được chọn
      if (selectedAttendeeIds.length > 0) {
        await api.post("/su-kien/register", {
          su_kien_id: selectedEvent.id,
          thanh_vien_ids: selectedAttendeeIds,
        });
      }

      Alert.alert("Thành Công", "Cập nhật danh sách tham gia sự kiện thành công!");
      setShowRegisterModal(false);
      loadData(); // Tải lại để cập nhật view ngoài
    } catch (err: any) {
      console.log("Lỗi đăng ký tham gia sự kiện:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể cập nhật danh sách đăng ký.");
    } finally {
      setRegistering(false);
    }
  };

  const getLoaiSuKienStyle = (loai: string) => {
    switch (loai) {
      case "Giỗ tổ":
        return { bg: "#FEE2E2", text: "#EF4444", border: "rgba(239, 68, 68, 0.15)", icon: "ribbon-outline" };
      case "Họp họ":
        return { bg: "#FEF3C7", text: "#D97706", border: "rgba(217, 119, 6, 0.15)", icon: "people-outline" };
      case "Cưới hỏi":
        return { bg: "#FCE7F3", text: "#EC4899", border: "rgba(236, 72, 153, 0.15)", icon: "heart-outline" };
      case "Tang lễ":
        return { bg: "#E2E8F0", text: "#475569", border: "rgba(71, 85, 105, 0.15)", icon: "sad-outline" };
      default:
        return { bg: "#EFF6FF", text: "#3B82F6", border: "rgba(59, 130, 246, 0.15)", icon: "calendar-outline" };
    }
  };

  const formatEventDate = (dateStr: string) => {
    if (!dateStr) return "";
    try {
      const d = new Date(dateStr);
      return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
    } catch (e) {
      return dateStr;
    }
  };

  const getDaysCountdown = (dateStr: string) => {
    try {
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      const evDate = new Date(dateStr);
      evDate.setHours(0, 0, 0, 0);
      
      const diffTime = evDate.getTime() - today.getTime();
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
      
      if (diffDays === 0) return "Hôm nay diễn ra";
      if (diffDays < 0) return `Đã diễn ra`;
      return `Còn ${diffDays} ngày`;
    } catch (e) {
      return "";
    }
  };

  // Chỉ hiển thị các thành viên có cùng chi nhánh với user (để đăng ký hộ gia đình)
  const householdMembers = members.filter((m) =>
    myMember ? m.chi_nhanh_id === myMember.chi_nhanh_id : true
  );

  if (loading) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#FF9F43" />
        <Text style={styles.loadingText}>Đang tải danh sách sự kiện dòng họ...</Text>
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
            <Text style={styles.headerSubtitle}>LỊCH TRÌNH DÒNG TỘC</Text>
            <Text style={styles.headerTitle}>Sự Kiện Dòng Họ</Text>
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
          Xem lịch tế tổ, giỗ họ, họp chi phái và ngày hội dòng họ. Con cháu đăng ký tham gia đầy đủ để tăng gắn kết gia đình.
        </Text>
      </View>

      {/* DANH SÁCH SỰ KIỆN */}
      <FlatList
        data={events}
        keyExtractor={(item) => item.id.toString()}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={{ paddingHorizontal: 24, paddingTop: 16, paddingBottom: 40 }}
        refreshing={loading}
        onRefresh={loadData}
        ListEmptyComponent={
          <View style={styles.emptyContainer}>
            <Ionicons name="calendar-clear-outline" size={48} color="#94A3B8" />
            <Text style={styles.emptyText}>Hiện chưa có sự kiện nào được lên lịch.</Text>
          </View>
        }
        renderItem={({ item }) => {
          const style = getLoaiSuKienStyle(item.loai);
          const countdown = getDaysCountdown(item.ngay_to_chuc);
          const isOver = countdown.includes("Đã diễn ra");

          return (
            <View style={[styles.eventCard, isOver && styles.eventCardOver]}>
              {/* Header Sự Kiện */}
              <View style={styles.eventHeader}>
                <View style={[styles.typeBadge, { backgroundColor: style.bg, borderColor: style.border }]}>
                  <Ionicons name={style.icon as any} size={12} color={style.text} style={{ marginRight: 4 }} />
                  <Text style={[styles.typeBadgeText, { color: style.text }]}>{item.loai}</Text>
                </View>
                
                <View style={[styles.countdownBadge, isOver ? styles.countdownBadgeOver : styles.countdownBadgeActive]}>
                  <Text style={[styles.countdownText, isOver && { color: "#64748B" }]}>
                    {countdown}
                  </Text>
                </View>
              </View>

              {/* Chi tiết sự kiện */}
              <Text style={styles.eventTitle}>{item.tieu_de}</Text>
              
              <View style={styles.detailRow}>
                <Ionicons name="time-outline" size={15} color="#FF9F43" style={{ marginRight: 6 }} />
                <Text style={styles.detailText}>
                  Thời gian: <Text style={{ fontWeight: "700" }}>{formatEventDate(item.ngay_to_chuc)}</Text>
                </Text>
              </View>

              {item.dia_diem && (
                <View style={styles.detailRow}>
                  <Ionicons name="location-outline" size={15} color="#FF9F43" style={{ marginRight: 6 }} />
                  <Text style={styles.detailText} numberOfLines={1}>
                    Địa điểm: <Text style={{ fontWeight: "700" }}>{item.dia_diem}</Text>
                  </Text>
                </View>
              )}

              {item.noi_dung && (
                <Text style={styles.eventContent} numberOfLines={3}>
                  {item.noi_dung}
                </Text>
              )}

              {/* Nút Đăng Ký Tham Gia */}
              <TouchableOpacity
                style={[
                  styles.registerTriggerBtn,
                  isOver && styles.registerTriggerBtnOver
                ]}
                onPress={() => handleOpenRegister(item)}
                activeOpacity={0.8}
                disabled={isOver}
              >
                <Ionicons name="checkmark-done-circle" size={16} color="#FFF8F2" style={{ marginRight: 6 }} />
                <Text style={styles.registerTriggerBtnText}>
                  {isOver ? "ĐÃ DIỄN RA" : "ĐĂNG KÝ HỘ GIA ĐÌNH"}
                </Text>
              </TouchableOpacity>
            </View>
          );
        }}
      />

      {/* 🏡 MODAL ĐĂNG KÝ THÀNH VIÊN GIA ĐÌNH HỘ GIA ĐÌNH */}
      <Modal
        visible={showRegisterModal}
        animationType="slide"
        transparent
        onRequestClose={() => setShowRegisterModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalIndicator} />

            {/* Header Modal */}
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>ĐĂNG KÝ THAM GIA HỌP HỌ</Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowRegisterModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            {selectedEvent && (
              <View style={{ flex: 1 }}>
                <View style={styles.eventShortBox}>
                  <Text style={styles.shortTitle}>{selectedEvent.tieu_de}</Text>
                  <Text style={styles.shortTime}>
                    📅 Ngày diễn ra: {formatEventDate(selectedEvent.ngay_to_chuc)}
                  </Text>
                  <Text style={styles.shortLocation}>
                    📍 Địa điểm: {selectedEvent.dia_diem || "Tại nhà thờ tổ chi nhánh"}
                  </Text>
                </View>

                <Text style={styles.modalSubtitle}>
                  Tích chọn các thành viên trong gia đình bạn sẽ tham dự:
                </Text>

                {participantsLoading ? (
                  <ActivityIndicator size="large" color="#FF9F43" style={{ marginTop: 30, flex: 1 }} />
                ) : (
                  <FlatList
                    data={householdMembers}
                    keyExtractor={(item) => item.id.toString()}
                    showsVerticalScrollIndicator={false}
                    contentContainerStyle={{ paddingBottom: 24 }}
                    ListEmptyComponent={
                      <View style={styles.emptySearchContainer}>
                        <Text style={styles.emptySearchText}>Chưa có thành viên chi nhánh nào để chọn.</Text>
                      </View>
                    }
                    renderItem={({ item }) => {
                      const isSelected = selectedAttendeeIds.includes(item.id);
                      return (
                        <TouchableOpacity
                          style={[styles.memberItem, isSelected && styles.memberItemActive]}
                          onPress={() => toggleAttendee(item.id)}
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
                              {item.gioi_tinh} • {item.nghe_nghiep || "Không nghề"}
                            </Text>
                          </View>
                          <View style={[styles.checkbox, isSelected && styles.checkboxActive]}>
                            {isSelected && <Ionicons name="checkmark" size={14} color="#FFF8F2" />}
                          </View>
                        </TouchableOpacity>
                      );
                    }}
                  />
                )}

                <View style={styles.formActions}>
                  <TouchableOpacity
                    style={styles.saveBtn}
                    onPress={handleSaveRegistration}
                    disabled={registering || participantsLoading}
                    activeOpacity={0.85}
                  >
                    {registering ? (
                      <ActivityIndicator size="small" color="#0c0e12" />
                    ) : (
                      <>
                        <Text style={styles.saveBtnText}>CẬP NHẬT ĐĂNG KÝ HỌP HỌ</Text>
                        <Ionicons name="cloud-upload-outline" size={16} color="#0c0e12" style={{ marginLeft: 6 }} />
                      </>
                    )}
                  </TouchableOpacity>
                </View>
              </View>
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
  },
  loadingText: {
    fontSize: 14,
    color: "#D97706",
    fontWeight: "700",
    marginTop: 12,
  },

  // HEADER
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

  // EVENT CARDS
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 100,
  },
  emptyText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 12,
    textAlign: "center",
  },
  eventCard: {
    backgroundColor: "#ffffff",
    borderRadius: 22,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    padding: 18,
    marginBottom: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.03,
    shadowRadius: 10,
    elevation: 3,
  },
  eventCardOver: {
    borderColor: "rgba(255, 159, 67, 0.05)",
    backgroundColor: "#F8FAFC",
  },
  eventHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  typeBadge: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
    borderWidth: 1,
  },
  typeBadgeText: {
    fontSize: 10,
    fontWeight: "900",
  },
  countdownBadge: {
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 8,
  },
  countdownBadgeActive: {
    backgroundColor: "rgba(255, 159, 67, 0.1)",
  },
  countdownBadgeOver: {
    backgroundColor: "#E2E8F0",
  },
  countdownText: {
    fontSize: 10,
    fontWeight: "900",
    color: "#D97706",
  },
  eventTitle: {
    fontSize: 16,
    fontWeight: "900",
    color: "#1E293B",
    marginBottom: 10,
  },
  detailRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 6,
  },
  detailText: {
    fontSize: 12.5,
    color: "#475569",
    fontWeight: "600",
  },
  eventContent: {
    fontSize: 12.5,
    color: "#64748B",
    lineHeight: 18,
    fontWeight: "500",
    marginTop: 10,
    backgroundColor: "rgba(255, 159, 67, 0.02)",
    padding: 10,
    borderRadius: 12,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.05)",
  },
  registerTriggerBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 12,
    borderRadius: 12,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 6,
    elevation: 2,
  },
  registerTriggerBtnOver: {
    backgroundColor: "#94A3B8",
    shadowOpacity: 0,
    elevation: 0,
  },
  registerTriggerBtnText: {
    color: "#FFF8F2",
    fontWeight: "900",
    fontSize: 12,
    letterSpacing: 0.5,
  },

  // MODAL STYLING
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.4)",
    justifyContent: "flex-end",
  },
  modalContent: {
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    height: "85%",
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

  eventShortBox: {
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    borderRadius: 18,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)",
    padding: 14,
    marginBottom: 16,
  },
  shortTitle: {
    fontSize: 14.5,
    fontWeight: "900",
    color: "#1E293B",
    marginBottom: 6,
  },
  shortTime: {
    fontSize: 12,
    color: "#D97706",
    fontWeight: "700",
  },
  shortLocation: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  modalSubtitle: {
    fontSize: 12.5,
    fontWeight: "800",
    color: "#D97706",
    letterSpacing: 0.5,
    marginBottom: 12,
    textTransform: "uppercase",
  },

  emptySearchContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 50,
  },
  emptySearchText: {
    fontSize: 13.5,
    color: "#94A3B8",
    fontWeight: "600",
  },

  memberItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 12,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.06)",
    borderRadius: 14,
    marginBottom: 8,
  },
  memberItemActive: {
    backgroundColor: "rgba(255, 159, 67, 0.04)",
  },
  memberAvatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.25)",
    marginRight: 12,
  },
  memberInfo: {
    flex: 1,
  },
  memberName: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
  },
  memberSub: {
    fontSize: 11.5,
    color: "#64748B",
    fontWeight: "600",
  },
  checkbox: {
    width: 22,
    height: 22,
    borderRadius: 6,
    borderWidth: 2,
    borderColor: "rgba(255, 159, 67, 0.3)",
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#ffffff",
  },
  checkboxActive: {
    backgroundColor: "#FF9F43",
    borderColor: "#FF9F43",
  },

  formActions: {
    backgroundColor: "#ffffff",
    borderTopWidth: 1.5,
    borderTopColor: "rgba(255, 159, 67, 0.15)",
    paddingTop: 12,
    paddingBottom: 24,
  },
  saveBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 3,
  },
  saveBtnText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 13,
    letterSpacing: 0.5,
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
