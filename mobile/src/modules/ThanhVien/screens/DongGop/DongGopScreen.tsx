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
import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

const { width } = Dimensions.get("window");

export default function DongGopScreen() {
  const [contributions, setContributions] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentUser, setCurrentUser] = useState<any | null>(null);

  // Trạng thái biểu mẫu Quyên Góp
  const [showDonateModal, setShowDonateModal] = useState(false);
  const [donorName, setDonorName] = useState("");
  const [amount, setAmount] = useState("200000"); // Mặc định 200,000 VND
  const [isCustomAmount, setIsCustomAmount] = useState(false);
  
  // Trạng thái hiển thị VietQR
  const [showQRCode, setShowQRCode] = useState(false);
  const [qrUrl, setQrUrl] = useState("");
  const [verifyingPayment, setVerifyingPayment] = useState(false);

  useEffect(() => {
    loadData();
  }, []);

  const handleReload = async () => {
    await loadData();
  };

  const loadData = async () => {
    try {
      setLoading(true);
      // 1. Tải thông tin người dùng hiện tại
      const profileRes = await api.get("/me");
      if (profileRes.data && profileRes.data.user) {
        setCurrentUser(profileRes.data.user);
        setDonorName(profileRes.data.user.ho_ten || "");
      }

      // 2. Tải danh sách đóng góp từ trước đến nay
      const res = await api.get("/dong-gop/get-data");
      if (res.data.status) {
        // Lọc các đóng góp đã được duyệt để hiển thị Bảng Vàng danh dự
        const list = res.data.data || [];
        const approvedList = list.filter((item: any) => item.trang_thai === "Đã duyệt");
        setContributions(approvedList);
      }
    } catch (err: any) {
      console.log("Lỗi tải trang đóng góp quỹ:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể tải danh sách sổ sách đóng góp quỹ.");
    } finally {
      setLoading(false);
    }
  };

  const handleOpenDonate = () => {
    setShowDonateModal(true);
    setShowQRCode(false);
    setAmount("200000");
    setIsCustomAmount(false);
    if (currentUser) {
      setDonorName(currentUser.ho_ten || "");
    }
  };

  const generateVietQR = () => {
    if (!donorName.trim()) {
      Alert.alert("Thông báo", "Vui lòng nhập tên người quyên góp!");
      return;
    }

    const numAmount = parseInt(amount, 10);
    if (isNaN(numAmount) || numAmount < 10000) {
      Alert.alert("Thông báo", "Số tiền đóng góp tối thiểu là 10,000đ!");
      return;
    }

    // Tạo mã nội dung chuyển khoản khớp với backend ThanhToanController
    // expectedContent: DONGGOP {ho_ten}
    // format nội dung: DONGGOP [Tên không dấu] (để an toàn tránh lỗi tiếng Việt có dấu trên ngân hàng)
    const unsignedName = donorName
      .normalize("NHD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/đ/g, "d")
      .replace(/Đ/g, "D")
      .replace(/[^a-zA-Z0-9\s]/g, "")
      .toUpperCase();

    const transferContent = `DONGGOP ${unsignedName.replace(/\s+/g, "")}`;
    
    // Tạo link ảnh VietQR (MBBank, Account: 999999999, Quy Gia Toc)
    const bankId = "MB";
    const accountNo = "999999999";
    const template = "print";
    const accountName = "QUY GIA TOC CAY GIA PHA";
    
    const url = `https://img.vietqr.io/image/${bankId}-${accountNo}-${template}.jpg?amount=${numAmount}&addInfo=${encodeURIComponent(
      transferContent
    )}&accountName=${encodeURIComponent(accountName)}`;

    setQrUrl(url);
    setShowQRCode(true);
  };

  // Xác minh chuyển khoản thông qua cổng SePay
  const verifyPayment = async () => {
    if (!currentUser) return;
    try {
      setVerifyingPayment(true);
      
      const unsignedName = donorName
        .normalize("NHD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/đ/g, "d")
        .replace(/Đ/g, "D")
        .replace(/[^a-zA-Z0-9\s]/g, "")
        .toUpperCase();

      const transferContent = `DONGGOP ${unsignedName.replace(/\s+/g, "")}`;

      const payload = {
        nguoi_dung_id: currentUser.id,
        noi_dung: `${transferContent} | Số tiền: ${amount}đ`,
      };

      const res = await api.post("/thanh-toan/xac-nhan-thanh-toan", payload);
      
      if (res.data.success) {
        Alert.alert("Tâm Đức Trọn Vẹn", "Hệ thống SePay đã xác thực giao dịch thành công! Tên của bạn đã được ghi vào Bảng Vàng công đức dòng tộc.");
        setShowDonateModal(false);
        setShowQRCode(false);
        loadData(); // Tải lại Bảng Vàng đóng góp
      } else {
        Alert.alert(
          "Chờ xác thực",
          res.data.message || "Hệ thống chưa tìm thấy giao dịch chuyển khoản. Vui lòng thử lại sau 1-2 phút nếu ngân hàng đã báo trừ tiền."
        );
      }
    } catch (err: any) {
      console.log("Lỗi xác minh thanh toán:", err.response?.data || err.message);
      Alert.alert("Lỗi", "Không thể liên kết cổng SePay để kiểm tra giao dịch.");
    } finally {
      setVerifyingPayment(false);
    }
  };

  const formatCurrency = (val: string) => {
    const num = parseInt(val, 10);
    if (isNaN(num)) return "0đ";
    return num.toLocaleString("vi-VN") + "đ";
  };

  // Trích xuất số tiền và tên từ nội dung đóng góp
  const parseContribution = (noiDung: string) => {
    // Định dạng: "DONGGOP HUYNHMINHTRI | Số tiền: 500000đ"
    if (!noiDung) return { name: "Thành viên gia tộc", amountText: "Tâm đức" };
    try {
      const parts = noiDung.split("|");
      let name = parts[0].replace("DONGGOP", "").trim();
      if (!name) name = "Ẩn danh";
      
      let amountText = "Tâm đức";
      if (parts[1]) {
        const amtMatch = parts[1].match(/\d+/);
        if (amtMatch) {
          amountText = parseInt(amtMatch[0], 10).toLocaleString("vi-VN") + "đ";
        }
      }
      return { name, amountText };
    } catch (e) {
      return { name: noiDung, amountText: "Tâm đức" };
    }
  };

  if (loading) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#FF9F43" />
        <Text style={styles.loadingText}>Đang tải trang đóng góp quỹ dòng tộc...</Text>
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
        {/* BANNER ĐÓNG GÓP QUỸ */}
        <View style={styles.header}>
          <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "center", marginBottom: 8 }}>
            <View style={{ flex: 1 }}>
              <Text style={styles.headerSubtitle}>TẤM LÒNG VÀNG DÒNG HỌ</Text>
              <Text style={styles.headerTitle}>Đóng Góp Quỹ Tâm Đức</Text>
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
            "Ăn quả nhớ kẻ trồng cây." Con cháu góp công, góp của trùng tu từ đường, tôn tạo mộ phần tổ tiên và bồi dưỡng nhân tài học tập.
          </Text>
        </View>

        {/* THÔNG TIN TÀI KHOẢN CLAN */}
        <View style={styles.clanAccountCard}>
          <Text style={styles.cardHeader}>🏛️ TÀI KHOẢN BAN TRỊ SỰ DÒNG HỌ</Text>
          <View style={styles.cardDivider} />
          
          <View style={styles.accountRow}>
            <Text style={styles.accountLabel}>Ngân hàng:</Text>
            <Text style={styles.accountValue}>MBBank (Ngân hàng Quân Đội)</Text>
          </View>
          <View style={styles.accountRow}>
            <Text style={styles.accountLabel}>Số tài khoản:</Text>
            <Text style={[styles.accountValue, { fontWeight: "900", color: "#D97706" }]}>999999999</Text>
          </View>
          <View style={styles.accountRow}>
            <Text style={styles.accountLabel}>Chủ tài khoản:</Text>
            <Text style={styles.accountValue}>QUY GIA TOC CAY GIA PHA</Text>
          </View>

          <TouchableOpacity
            style={styles.donateTriggerBtn}
            onPress={handleOpenDonate}
            activeOpacity={0.85}
          >
            <Ionicons name="heart-sharp" size={16} color="#FFF8F2" style={{ marginRight: 6 }} />
            <Text style={styles.donateTriggerBtnText}>QUYÊN GÓP TÂM ĐỨC</Text>
          </TouchableOpacity>
        </View>

        {/* 🏆 BẢNG VÀNG DANH DỰ (LEADERBOARD) */}
        <View style={styles.sectionHeader}>
          <Text style={styles.sectionTitle}>🏆 BẢNG VÀNG DANH DỰ CÔNG ĐỨC</Text>
          <View style={styles.sectionLine} />
        </View>

        {contributions.length === 0 ? (
          <View style={styles.emptyCard}>
            <Ionicons name="ribbon-outline" size={40} color="#D97706" style={{ marginBottom: 8 }} />
            <Text style={styles.emptyText}>Hiện chưa có dữ liệu đóng góp công khai.</Text>
          </View>
        ) : (
          <View style={styles.leaderboard}>
            {contributions.map((item, index) => {
              const parse = parseContribution(item.noi_dung);
              const rankColor = index === 0 ? "#FFD700" : index === 1 ? "#C0C0C0" : index === 2 ? "#CD7F32" : "#FFF8F2";

              return (
                <View key={item.id} style={styles.leaderboardItem}>
                  <View style={styles.leaderboardLeft}>
                    <View style={[styles.rankBadge, { backgroundColor: rankColor }]}>
                      {index < 3 ? (
                        <Ionicons name="trophy" size={14} color={index === 0 ? "#D97706" : index === 1 ? "#475569" : "#ffffff"} />
                      ) : (
                        <Text style={styles.rankText}>{index + 1}</Text>
                      )}
                    </View>
                    <View>
                      <Text style={styles.donorName}>{parse.name}</Text>
                      <Text style={styles.donorDate}>
                        Đã công đức ngày: {new Date(item.created_at).toLocaleDateString("vi-VN")}
                      </Text>
                    </View>
                  </View>
                  <View style={styles.donorAmountContainer}>
                    <Text style={styles.donorAmount}>{parse.amountText}</Text>
                  </View>
                </View>
              );
            })}
          </View>
        )}
      </ScrollView>

      {/* 🏛️ MODAL QUYÊN GÓP TÂM ĐỨC */}
      <Modal
        visible={showDonateModal}
        animationType="slide"
        transparent
        onRequestClose={() => setShowDonateModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <View style={styles.modalIndicator} />

            {/* Header Modal */}
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>✨ QUYÊN GÓP TÂM ĐỨC GIA TỘC</Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowDonateModal(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={{ paddingBottom: 40 }}>
              {!showQRCode ? (
                // BƯỚC 1: NHẬP THÔNG TIN
                <View style={styles.formContainer}>
                  {/* Tên người quyên góp */}
                  <Text style={styles.inputLabel}>Tên người quyên góp công đức</Text>
                  <TextInput
                    style={styles.textInput}
                    placeholder="Nhập họ và tên người dâng tâm đức..."
                    placeholderTextColor="#94A3B8"
                    value={donorName}
                    onChangeText={setDonorName}
                  />

                  {/* Chọn số tiền dâng quỹ */}
                  <Text style={styles.inputLabel}>Chọn số tiền dâng quỹ dòng họ</Text>
                  <View style={styles.amountPills}>
                    {["100000", "200000", "500000", "1000000"].map((val) => {
                      const active = amount === val && !isCustomAmount;
                      return (
                        <TouchableOpacity
                          key={val}
                          style={[styles.amountPill, active && styles.amountPillActive]}
                          onPress={() => {
                            setAmount(val);
                            setIsCustomAmount(false);
                          }}
                          activeOpacity={0.8}
                        >
                          <Text style={[styles.amountPillText, active && styles.amountPillTextActive]}>
                            {formatCurrency(val)}
                          </Text>
                        </TouchableOpacity>
                      );
                    })}
                  </View>

                  {/* Nút nhập số tiền tùy chọn */}
                  <TouchableOpacity
                    style={[styles.customAmountTrigger, isCustomAmount && styles.customAmountTriggerActive]}
                    onPress={() => setIsCustomAmount(true)}
                    activeOpacity={0.8}
                  >
                    <Text style={[styles.customAmountText, isCustomAmount && styles.customAmountTextActive]}>
                      Số tiền tùy chọn tự nhập
                    </Text>
                  </TouchableOpacity>

                  {isCustomAmount && (
                    <TextInput
                      style={[styles.textInput, { marginTop: 10 }]}
                      placeholder="Nhập số tiền VNĐ. Tối thiểu 10,000đ..."
                      placeholderTextColor="#94A3B8"
                      value={amount}
                      onChangeText={setAmount}
                      keyboardType="numeric"
                    />
                  )}

                  <TouchableOpacity
                    style={styles.nextBtn}
                    onPress={generateVietQR}
                    activeOpacity={0.85}
                  >
                    <Text style={styles.nextBtnText}>TẠO MÃ CHUYỂN KHOẢN VIETQR</Text>
                    <Ionicons name="qr-code-outline" size={16} color="#0c0e12" style={{ marginLeft: 6 }} />
                  </TouchableOpacity>
                </View>
              ) : (
                // BƯỚC 2: QUÉT QR CODE VIETQR
                <View style={styles.qrContainer}>
                  <Text style={styles.qrDesc}>
                    Hãy quét mã VietQR dưới đây bằng ứng dụng Ngân hàng (Mobile Banking) của bạn để chuyển khoản tự động và tức thì:
                  </Text>

                  {/* QR Image */}
                  <View style={styles.qrFrame}>
                    <Image
                      source={{ uri: qrUrl }}
                      style={styles.qrCodeImage}
                      resizeMode="contain"
                    />
                  </View>

                  <View style={styles.qrBillInfo}>
                    <Text style={styles.billRow}>
                      • Người dâng: <Text style={{ fontWeight: "800" }}>{donorName}</Text>
                    </Text>
                    <Text style={styles.billRow}>
                      • Số tiền: <Text style={{ fontWeight: "800", color: "#FF5E36" }}>{formatCurrency(amount)}</Text>
                    </Text>
                    <Text style={styles.billRow}>
                      • Nội dung: <Text style={{ fontWeight: "800", color: "#D97706" }}>DONGGOP {donorName.normalize("NHD").replace(/[\u0300-\u036f]/g, "").replace(/đ/g, "d").replace(/Đ/g, "D").replace(/[^a-zA-Z0-9\s]/g, "").replace(/\s+/g, "").toUpperCase()}</Text>
                    </Text>
                  </View>

                  <Text style={styles.warningNote}>
                    ⚠️ Lưu ý: Vui lòng chuyển khoản đúng số tiền và giữ nguyên nội dung chuyển khoản để hệ thống SePay tự động ghi nhận tức thì sau 5 giây.
                  </Text>

                  {/* Nút bấm để check SePay */}
                  <TouchableOpacity
                    style={styles.verifyBtn}
                    onPress={verifyPayment}
                    disabled={verifyingPayment}
                    activeOpacity={0.85}
                  >
                    {verifyingPayment ? (
                      <ActivityIndicator size="small" color="#0c0e12" />
                    ) : (
                      <>
                        <Text style={styles.verifyBtnText}>TÔI ĐÃ CHUYỂN KHOẢN Xong</Text>
                        <Ionicons name="shield-checkmark" size={16} color="#0c0e12" style={{ marginLeft: 6 }} />
                      </>
                    )}
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={styles.backBtn}
                    onPress={() => setShowQRCode(false)}
                    activeOpacity={0.8}
                  >
                    <Text style={styles.backBtnText}>Chọn lại số tiền & tên</Text>
                  </TouchableOpacity>
                </View>
              )}
            </ScrollView>
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

  // CLAN ACCOUNT CARD
  clanAccountCard: {
    backgroundColor: "#ffffff",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    borderRadius: 24,
    padding: 20,
    marginHorizontal: 24,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.04,
    shadowRadius: 12,
    elevation: 3,
  },
  cardHeader: {
    fontSize: 12,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1,
    textTransform: "uppercase",
  },
  cardDivider: {
    height: 1.5,
    backgroundColor: "rgba(255, 159, 67, 0.12)",
    marginVertical: 12,
  },
  accountRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: 8,
  },
  accountLabel: {
    fontSize: 13,
    color: "#64748B",
    fontWeight: "600",
  },
  accountValue: {
    fontSize: 13.5,
    color: "#1E293B",
    fontWeight: "700",
  },
  donateTriggerBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 2,
  },
  donateTriggerBtnText: {
    color: "#FFF8F2",
    fontWeight: "900",
    fontSize: 13,
    letterSpacing: 0.5,
  },

  // TIÊU ĐỀ SECTION
  sectionHeader: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 24,
    marginTop: 28,
    marginBottom: 16,
  },
  sectionTitle: {
    fontSize: 11.5,
    fontWeight: "900",
    color: "#475569",
    letterSpacing: 1.2,
    marginRight: 10,
  },
  sectionLine: {
    flex: 1,
    height: 1,
    backgroundColor: "rgba(255, 159, 67, 0.15)",
  },

  // LEADERBOARD PUBLIC LOG
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
  leaderboard: {
    marginHorizontal: 24,
  },
  leaderboardItem: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
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
  leaderboardLeft: {
    flexDirection: "row",
    alignItems: "center",
    flex: 1,
  },
  rankBadge: {
    width: 32,
    height: 32,
    borderRadius: 10,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 12,
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
  },
  rankText: {
    fontSize: 12,
    fontWeight: "900",
    color: "#D97706",
  },
  donorName: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
  },
  donorDate: {
    fontSize: 11,
    color: "#94A3B8",
    fontWeight: "600",
    marginTop: 2,
  },
  donorAmountContainer: {
    backgroundColor: "rgba(16, 185, 129, 0.08)",
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderWidth: 1,
    borderColor: "rgba(16, 185, 129, 0.15)",
  },
  donorAmount: {
    fontSize: 13,
    fontWeight: "900",
    color: "#10B981",
  },

  // MODAL OVERLAY STYLING
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
    marginBottom: 16,
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

  // FORM CONTAINER
  formContainer: {
    marginTop: 10,
  },
  inputLabel: {
    fontSize: 12.5,
    fontWeight: "800",
    color: "#475569",
    marginBottom: 8,
    textTransform: "uppercase",
    letterSpacing: 0.5,
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
    marginBottom: 16,
  },
  amountPills: {
    flexDirection: "row",
    flexWrap: "wrap",
    justifyContent: "space-between",
    marginBottom: 12,
  },
  amountPill: {
    width: "48%",
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.25)",
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: "center",
    marginBottom: 10,
  },
  amountPillActive: {
    backgroundColor: "#FF9F43",
    borderColor: "#FF9F43",
  },
  amountPillText: {
    fontSize: 13,
    fontWeight: "800",
    color: "#D97706",
  },
  amountPillTextActive: {
    color: "#0c0e12",
  },
  customAmountTrigger: {
    backgroundColor: "#ffffff",
    borderWidth: 1.2,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 12,
    paddingVertical: 12,
    alignItems: "center",
    marginBottom: 12,
  },
  customAmountTriggerActive: {
    borderColor: "#FF9F43",
    backgroundColor: "rgba(255, 159, 67, 0.04)",
  },
  customAmountText: {
    fontSize: 13,
    fontWeight: "800",
    color: "#64748B",
  },
  customAmountTextActive: {
    color: "#D97706",
  },
  nextBtn: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 3,
  },
  nextBtnText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 13,
    letterSpacing: 0.5,
  },

  // QR CONTAINER STYLING
  qrContainer: {
    alignItems: "center",
    marginTop: 8,
  },
  qrDesc: {
    fontSize: 13,
    color: "#475569",
    lineHeight: 18,
    textAlign: "center",
    fontWeight: "500",
    marginBottom: 16,
  },
  qrFrame: {
    width: 200,
    height: 200,
    backgroundColor: "#ffffff",
    borderRadius: 20,
    borderWidth: 2,
    borderColor: "#FF9F43",
    padding: 10,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
    elevation: 4,
    marginBottom: 16,
  },
  qrCodeImage: {
    width: "100%",
    height: "100%",
  },
  qrBillInfo: {
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 16,
    padding: 14,
    width: "100%",
    marginBottom: 16,
  },
  billRow: {
    fontSize: 13,
    color: "#1E293B",
    fontWeight: "600",
    marginBottom: 4,
  },
  warningNote: {
    fontSize: 11,
    color: "#EF4444",
    lineHeight: 16,
    fontWeight: "600",
    textAlign: "center",
    paddingHorizontal: 12,
    marginBottom: 20,
  },
  verifyBtn: {
    backgroundColor: "#FF5E36",
    flexDirection: "row",
    paddingVertical: 14,
    borderRadius: 14,
    alignItems: "center",
    justifyContent: "center",
    width: "100%",
    shadowColor: "#FF5E36",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 3,
  },
  verifyBtnText: {
    color: "#ffffff",
    fontWeight: "900",
    fontSize: 13.5,
    letterSpacing: 0.5,
  },
  backBtn: {
    paddingVertical: 12,
    marginTop: 10,
  },
  backBtnText: {
    fontSize: 12.5,
    color: "#64748B",
    fontWeight: "700",
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
