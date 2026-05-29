import { useState } from "react";
import { router } from "expo-router";
import AsyncStorage from "@react-native-async-storage/async-storage";
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ActivityIndicator,
  Dimensions,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  ImageBackground,
  Modal,
  Pressable,
} from "react-native";

import { dangNhap } from "../services/authService";

const { width } = Dimensions.get("window");

export default function LoginScreen() {
  const [email, setEmail] = useState("");
  const [matKhau, setMatKhau] = useState("");
  const [loading, setLoading] = useState(false);
  const [focusInput, setFocusInput] = useState<string | null>(null);

  // CUSTOM BEAUTIFUL GLASSMORPHISM ALERT MODAL STATE
  const [alertVisible, setAlertVisible] = useState(false);
  const [alertTitle, setAlertTitle] = useState("");
  const [alertMessage, setAlertMessage] = useState("");
  const [alertType, setAlertType] = useState<"success" | "error" | "info">("info");
  const [alertCallback, setAlertCallback] = useState<(() => void) | null>(null);

  const hienThiThongBao = (title: string, message: string, type: "success" | "error" | "info" = "info", callback?: () => void) => {
    setAlertTitle(title);
    setAlertMessage(message);
    setAlertType(type);
    setAlertCallback(() => callback || null);
    setAlertVisible(true);
  };

  const xuLyDongThongBao = () => {
    setAlertVisible(false);
    if (alertCallback) {
      alertCallback();
    }
  };

  const xuLyDangNhap = async () => {
    if (!email.trim() || !matKhau.trim()) {
      hienThiThongBao("Thông báo", "Vui lòng điền đầy đủ Email và Mật khẩu", "info");
      return;
    }

    try {
      setLoading(true);
      const data = await dangNhap(email, matKhau);

      console.log("LOGIN:", data);
      await AsyncStorage.setItem("token", data.access_token);
      
      hienThiThongBao("Thành công", "Chào mừng bạn quay trở lại!", "success", () => {
        if (data.redirect_url?.includes("doi-tac")) {
          router.replace("/doi-tac");
        } else {
          router.replace("/doi-tac"); // Default fallback
        }
      });
    } catch (error: any) {
      console.log(error?.response?.data);
      hienThiThongBao(
        "Đăng nhập thất bại",
        error?.response?.data?.message || "Tài khoản hoặc mật khẩu không chính xác",
        "error"
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <ImageBackground
      source={require("../../../../assets/images/background.jpg")}
      style={styles.container}
      resizeMode="cover"
    >
      {/* LỚP PHỦ TỐI MỜ (OVERLAY) ĐỂ NỔI BẬT GIAO DIỆN VÀ TEXT */}
      <View style={styles.overlay}>
        {/* ĐỐM SÁNG GOLD MỜ TRANG TRÍ ĐỒNG BỘ MÀU HOÀNG HÔN CỦA NỀN */}
        <View style={[styles.glowSphere, styles.glowAmber, { top: -40, left: -60 }]} />
        <View style={[styles.glowSphere, styles.glowGold, { bottom: -80, right: -60 }]} />

        <KeyboardAvoidingView
          behavior={Platform.OS === "ios" ? "padding" : "height"}
          style={{ flex: 1 }}
        >
          <ScrollView
            contentContainerStyle={styles.scrollContainer}
            showsVerticalScrollIndicator={false}
          >
            {/* LOGO & TIÊU ĐỀ HỌ GIA TỘC */}
            <View style={styles.headerContainer}>
              <View style={styles.emblemContainer}>
                <Text style={styles.emblemText}>🌳</Text>
              </View>
              <Text style={styles.title}>CÂY GIA PHẢ</Text>
              <Text style={styles.tagline}>Gìn giữ cội nguồn • Kết nối thế hệ</Text>
              
              {/* THÔNG TIN GIỚI THIỆU SƠ LƯỢC XỊN SÒ */}
              <View style={styles.introCard}>
                <Text style={styles.introText}>
                  Hệ thống phả hệ số thông minh giúp bạn lưu trữ gia phả dòng tộc, 
                  vẽ sơ đồ cây tự động và tra cứu danh xưng họ hàng chính xác bằng 
                  thuật toán BFS Engine tiên tiến.
                </Text>
              </View>
            </View>

            {/* KHUNG ĐĂNG NHẬP GLASSMORPHISM */}
            <View style={styles.glassCard}>
              <Text style={styles.cardTitle}>ĐĂNG NHẬP HỆ THỐNG</Text>

              <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Tài khoản Email</Text>
                <TextInput
                  placeholder="Nhập email của bạn..."
                  value={email}
                  onChangeText={setEmail}
                  style={[
                    styles.input,
                    focusInput === "email" && styles.inputActive
                  ]}
                  placeholderTextColor="rgba(255, 255, 255, 0.3)"
                  keyboardType="email-address"
                  autoCapitalize="none"
                  onFocus={() => setFocusInput("email")}
                  onBlur={() => setFocusInput(null)}
                />
              </View>

              <View style={styles.inputGroup}>
                <Text style={styles.inputLabel}>Mật khẩu</Text>
                <TextInput
                  placeholder="Nhập mật khẩu..."
                  value={matKhau}
                  onChangeText={setMatKhau}
                  secureTextEntry
                  style={[
                    styles.input,
                    focusInput === "password" && styles.inputActive
                  ]}
                  placeholderTextColor="rgba(255, 255, 255, 0.3)"
                  onFocus={() => setFocusInput("password")}
                  onBlur={() => setFocusInput(null)}
                />
              </View>

              <TouchableOpacity
                style={styles.button}
                onPress={xuLyDangNhap}
                disabled={loading}
                activeOpacity={0.85}
              >
                {loading ? (
                  <ActivityIndicator color="#0c0e12" size="small" />
                ) : (
                  <Text style={styles.buttonText}>Truy cập hệ thống</Text>
                )}
              </TouchableOpacity>

              {/* LIÊN KẾT PHỤ TRANG TRÍ & PHỤC VỤ DỊCH VỤ */}
              <View style={styles.extraLinks}>
                <TouchableOpacity activeOpacity={0.7}>
                  <Text style={styles.linkText}>Quên mật khẩu?</Text>
                </TouchableOpacity>
                
                <Text style={styles.divider}>|</Text>
                
                <TouchableOpacity activeOpacity={0.7}>
                  <Text style={[styles.linkText, styles.highlightText]}>Đăng ký mới</Text>
                </TouchableOpacity>
              </View>
            </View>

            {/* BẢN QUYỀN / FOOTER GIA TỘC */}
            <Text style={styles.footerCopyright}>
              © 2026 Hệ Thống Quản Lý Phả Hệ Số. All rights reserved.
            </Text>
          </ScrollView>
        </KeyboardAvoidingView>
      </View>

      {/* THÔNG BÁO CUSTOM MODAL */}
      <Modal
        visible={alertVisible}
        transparent
        animationType="fade"
        onRequestClose={xuLyDongThongBao}
      >
        <View style={styles.alertModalOverlay}>
          <View style={styles.alertCard}>
            <View style={[
              styles.alertIconContainer,
              alertType === "success" && styles.alertIconSuccess,
              alertType === "error" && styles.alertIconError,
              alertType === "info" && styles.alertIconInfo,
            ]}>
              <Text style={styles.alertIconText}>
                {alertType === "success" ? "✓" : alertType === "error" ? "✕" : "i"}
              </Text>
            </View>
            
            <Text style={styles.alertTitleText}>{alertTitle}</Text>
            <Text style={styles.alertMessageText}>{alertMessage}</Text>
            
            <TouchableOpacity
              style={styles.alertButton}
              onPress={xuLyDongThongBao}
              activeOpacity={0.8}
            >
              <Text style={styles.alertButtonText}>OK</Text>
            </TouchableOpacity>
          </View>
        </View>
      </Modal>
    </ImageBackground>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },

  overlay: {
    flex: 1,
    backgroundColor: "rgba(10, 13, 18, 0.78)", // Phủ tối huyền bí nhưng giữ được chiều sâu của tranh nền hoàng hôn
  },

  scrollContainer: {
    flexGrow: 1,
    justifyContent: "center",
    padding: 24,
    paddingTop: 70,
    paddingBottom: 40,
  },

  // GLOW DECORATIVE BACKGROUND ELEMENTS
  glowSphere: {
    position: "absolute",
    width: 350,
    height: 350,
    borderRadius: 175,
    opacity: 0.12,
  },
  glowGold: {
    backgroundColor: "#FF9F43", // Vàng cam ấm áp theo tông của cây
  },
  glowAmber: {
    backgroundColor: "#FF5E36", // Đỏ hoàng hôn
  },

  // HEADER STYLES
  headerContainer: {
    alignItems: "center",
    marginBottom: 28,
  },
  emblemContainer: {
    width: 84,
    height: 84,
    borderRadius: 42,
    backgroundColor: "rgba(255, 159, 67, 0.12)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.4)",
    justifyContent: "center",
    alignItems: "center",
    marginBottom: 16,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.3,
    shadowRadius: 14,
    elevation: 6,
  },
  emblemText: {
    fontSize: 42,
  },
  title: {
    fontSize: 34,
    fontWeight: "900",
    textAlign: "center",
    color: "#ffffff",
    letterSpacing: 3,
    marginBottom: 8,
    textShadowColor: "rgba(255, 159, 67, 0.45)",
    textShadowOffset: { width: 0, height: 3 },
    textShadowRadius: 8,
  },
  tagline: {
    fontSize: 13,
    color: "#FF9F43", // Màu vàng cam ấm áp đồng bộ
    fontWeight: "700",
    letterSpacing: 2,
    textTransform: "uppercase",
    marginBottom: 18,
  },
  introCard: {
    width: "100%",
    backgroundColor: "rgba(255, 255, 255, 0.02)",
    borderWidth: 1,
    borderColor: "rgba(255, 255, 255, 0.05)",
    borderRadius: 20,
    padding: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  introText: {
    fontSize: 13,
    color: "rgba(255, 255, 255, 0.6)",
    textAlign: "center",
    lineHeight: 20,
    fontWeight: "400",
  },

  // CARD STYLES (GLASSMORPHISM)
  glassCard: {
    backgroundColor: "rgba(18, 21, 28, 0.86)", // Cực tối mịn như kính sang trọng
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.22)", // Viền vàng cam tinh tế
    borderRadius: 28,
    padding: 24,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.5,
    shadowRadius: 24,
    elevation: 10,
  },
  cardTitle: {
    fontSize: 15,
    fontWeight: "800",
    color: "#e2e8f0",
    letterSpacing: 1.5,
    marginBottom: 24,
    textAlign: "center",
  },

  // INPUTS
  inputGroup: {
    marginBottom: 20,
  },
  inputLabel: {
    fontSize: 11,
    fontWeight: "700",
    color: "#FF9F43",
    marginBottom: 8,
    textTransform: "uppercase",
    letterSpacing: 1,
  },
  input: {
    backgroundColor: "rgba(255, 255, 255, 0.03)",
    borderWidth: 1,
    borderColor: "rgba(255, 255, 255, 0.07)",
    borderRadius: 16,
    paddingHorizontal: 16,
    paddingVertical: 14,
    fontSize: 15,
    color: "#ffffff",
  },
  inputActive: {
    borderColor: "#FF9F43",
    backgroundColor: "rgba(255, 159, 67, 0.04)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.25,
    shadowRadius: 10,
  },

  // BUTTON
  button: {
    backgroundColor: "#FF9F43", // Nổi bật rực rỡ đúng chất hoàng hôn
    paddingVertical: 18,
    borderRadius: 16,
    marginTop: 10,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.35,
    shadowRadius: 12,
    elevation: 7,
  },
  buttonText: {
    color: "#0c0e12", // Đảm bảo tương phản hoàn hảo trên nút màu sáng
    textAlign: "center",
    fontWeight: "900",
    fontSize: 16,
    letterSpacing: 1.2,
    textTransform: "uppercase",
  },

  // EXTRA LINKS
  extraLinks: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "center",
    marginTop: 22,
  },
  linkText: {
    fontSize: 13,
    color: "rgba(255, 255, 255, 0.5)",
    fontWeight: "500",
  },
  divider: {
    color: "rgba(255, 255, 255, 0.15)",
    marginHorizontal: 16,
    fontSize: 13,
  },
  highlightText: {
    color: "#FF9F43",
    fontWeight: "700",
  },

  // FOOTER COPYRIGHT
  footerCopyright: {
    fontSize: 11,
    color: "rgba(255, 255, 255, 0.3)",
    textAlign: "center",
    marginTop: 32,
    letterSpacing: 0.5,
  },

  // CUSTOM ALERT MODAL STYLES
  alertModalOverlay: {
    flex: 1,
    backgroundColor: "rgba(10, 13, 18, 0.85)", // Tối huyền bí đồng bộ nền
    justifyContent: "center",
    alignItems: "center",
    padding: 24,
  },
  alertCard: {
    width: "100%",
    maxWidth: 340,
    backgroundColor: "rgba(20, 24, 33, 0.95)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.3)",
    borderRadius: 24,
    padding: 24,
    alignItems: "center",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.5,
    shadowRadius: 20,
    elevation: 10,
  },
  alertIconContainer: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: "center",
    alignItems: "center",
    marginBottom: 16,
    borderWidth: 2,
  },
  alertIconSuccess: {
    backgroundColor: "rgba(76, 175, 80, 0.15)",
    borderColor: "#4CAF50",
  },
  alertIconError: {
    backgroundColor: "rgba(244, 67, 54, 0.15)",
    borderColor: "#F44336",
  },
  alertIconInfo: {
    backgroundColor: "rgba(255, 159, 67, 0.15)",
    borderColor: "#FF9F43",
  },
  alertIconText: {
    fontSize: 28,
    fontWeight: "bold",
    color: "#fff",
  },
  alertTitleText: {
    fontSize: 18,
    fontWeight: "900",
    color: "#fff",
    marginBottom: 8,
    textAlign: "center",
  },
  alertMessageText: {
    fontSize: 14,
    color: "rgba(255, 255, 255, 0.7)",
    lineHeight: 20,
    textAlign: "center",
    marginBottom: 24,
  },
  alertButton: {
    backgroundColor: "#FF9F43",
    paddingVertical: 12,
    paddingHorizontal: 36,
    borderRadius: 12,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  alertButtonText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 15,
    textTransform: "uppercase",
    letterSpacing: 0.5,
  },
});