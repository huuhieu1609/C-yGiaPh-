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

export default function TraCuuScreen() {
  const [members, setMembers] = useState<any[]>([]);
  const [nguoi1, setNguoi1] = useState<number | null>(null);
  const [nguoi2, setNguoi2] = useState<number | null>(null);
  
  const [selectedMember1, setSelectedMember1] = useState<any | null>(null);
  const [selectedMember2, setSelectedMember2] = useState<any | null>(null);
  
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState("");
  
  // Sơ đồ đường đi BFS
  const [pathData, setPathData] = useState<any[]>([]);
  const [pathSteps, setPathSteps] = useState<string[]>([]);
  
  // Trạng thái modal tìm kiếm
  const [showSelector, setShowSelector] = useState(false);
  const [selectorTarget, setSelectorTarget] = useState<1 | 2>(1);
  const [searchQuery, setSearchQuery] = useState("");

  // LOAD MEMBERS
  useEffect(() => {
    loadMembers();
  }, []);

  const loadMembers = async () => {
    try {
      const res = await api.get("/thanh-vien/get-data");
      console.log("FULL RESPONSE:", JSON.stringify(res.data, null, 2));

      if (Array.isArray(res.data)) {
        setMembers(res.data);
      } else if (res.data && Array.isArray(res.data.data)) {
        setMembers(res.data.data);
      } else {
        setMembers([]);
      }
    } catch (error: any) {
      console.log(error.response?.data || error.message);
      Alert.alert("Lỗi", "Không tải được thành viên");
    }
  };

  // Mở trình chọn thành viên
  const openSelector = (target: 1 | 2) => {
    setSelectorTarget(target);
    setSearchQuery("");
    setShowSelector(true);
  };

  // Chọn thành viên
  const selectMember = (member: any) => {
    if (selectorTarget === 1) {
      setNguoi1(member.id);
      setSelectedMember1(member);
      // Reset kết quả cũ khi đổi lựa chọn
      setResult("");
      setPathData([]);
      setPathSteps([]);
    } else {
      setNguoi2(member.id);
      setSelectedMember2(member);
      // Reset kết quả cũ khi đổi lựa chọn
      setResult("");
      setPathData([]);
      setPathSteps([]);
    }
    setShowSelector(false);
  };

  // Lọc danh sách thành viên theo từ khóa tìm kiếm
  const filteredMembers = members.filter((m) => {
    const q = searchQuery.toLowerCase();
    const nameMatch = m.ho_ten?.toLowerCase().includes(q);
    const jobMatch = m.nghe_nghiep?.toLowerCase().includes(q);
    const codeMatch = String(m.id).includes(q);
    return nameMatch || jobMatch || codeMatch;
  });

  // TRA CỨU
  const traCuuQuanHe = async () => {
    if (!nguoi1 || !nguoi2) {
      Alert.alert("Thông báo", "Vui lòng chọn đủ 2 người");
      return;
    }

    if (nguoi1 === nguoi2) {
      Alert.alert("Thông báo", "Vui lòng chọn 2 người khác nhau");
      return;
    }

    try {
      setLoading(true);
      setResult("");
      setPathData([]);
      setPathSteps([]);

      const payload = {
        nguoi_1: nguoi1,
        nguoi_2: nguoi2,
      };

      console.log("REQUEST PAYLOAD:", JSON.stringify(payload, null, 2));
      const res = await api.post("/thanh-vien/tra-cuu-quan-he", payload);
      console.log("RESPONSE DATA:", JSON.stringify(res.data, null, 2));

      setResult(res.data.relationship);
      setPathData(res.data.members || []);
      setPathSteps(res.data.path || []);
    } catch (error: any) {
      console.log("ERROR RESPONSE:", JSON.stringify(error.response?.data, null, 2));
      console.log("ERROR MESSAGE:", error.message);

      const errorMsg =
        error.response?.data?.message ||
        error.response?.data?.error ||
        "Tra cứu thất bại từ hệ thống";

      Alert.alert("Lỗi", errorMsg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      {/* ĐỐM SÁNG GOLD/AMBER TRANG TRÍ SANG TRỌNG */}
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      <ScrollView
        contentContainerStyle={styles.scrollContainer}
        showsVerticalScrollIndicator={false}
      >
        <Text style={styles.title}>Tra cứu quan hệ</Text>

        {/* NGƯỜI THỨ 1 */}
        <View style={styles.card}>
          <Text style={styles.label}>Chọn người thứ 1</Text>
          <TouchableOpacity
            style={styles.selectorTrigger}
            onPress={() => openSelector(1)}
            activeOpacity={0.8}
          >
            <View style={styles.selectorLeft}>
              <Image
                source={{
                  uri:
                    selectedMember1?.avatar ||
                    "https://avatar.iran.liara.run/public",
                }}
                style={styles.avatarMini}
              />
              <View>
                <Text
                  style={[
                    styles.selectorValueText,
                    !selectedMember1 && styles.selectorPlaceholder,
                  ]}
                >
                  {selectedMember1 ? selectedMember1.ho_ten : "-- Chọn thành viên 1 --"}
                </Text>
                {selectedMember1 && (
                  <Text style={styles.selectorValueSub}>
                    {selectedMember1.gioi_tinh} • {selectedMember1.nghe_nghiep || "Chưa rõ nghề"}
                  </Text>
                )}
              </View>
            </View>
            <Ionicons name="chevron-down-outline" size={20} color="#FF9F43" />
          </TouchableOpacity>
        </View>

        {/* MŨI TÊN CHUYỂN TIẾP THẨM MỸ GIỮA 2 Ô */}
        <View style={styles.connectorArrow}>
          <View style={styles.connectorLine} />
          <View style={styles.connectorIconContainer}>
            <Ionicons name="swap-vertical-outline" size={18} color="#FF9F43" />
          </View>
          <View style={styles.connectorLine} />
        </View>

        {/* NGƯỜI THỨ 2 */}
        <View style={styles.card}>
          <Text style={styles.label}>Chọn người thứ 2</Text>
          <TouchableOpacity
            style={styles.selectorTrigger}
            onPress={() => openSelector(2)}
            activeOpacity={0.8}
          >
            <View style={styles.selectorLeft}>
              <Image
                source={{
                  uri:
                    selectedMember2?.avatar ||
                    "https://avatar.iran.liara.run/public",
                }}
                style={styles.avatarMini}
              />
              <View>
                <Text
                  style={[
                    styles.selectorValueText,
                    !selectedMember2 && styles.selectorPlaceholder,
                  ]}
                >
                  {selectedMember2 ? selectedMember2.ho_ten : "-- Chọn thành viên 2 --"}
                </Text>
                {selectedMember2 && (
                  <Text style={styles.selectorValueSub}>
                    {selectedMember2.gioi_tinh} • {selectedMember2.nghe_nghiep || "Chưa rõ nghề"}
                  </Text>
                )}
              </View>
            </View>
            <Ionicons name="chevron-down-outline" size={20} color="#FF9F43" />
          </TouchableOpacity>
        </View>

        {/* NÚT THỰC THI TRA CỨU */}
        <TouchableOpacity
          style={[styles.button, (!nguoi1 || !nguoi2) && styles.buttonDisabled]}
          onPress={traCuuQuanHe}
          activeOpacity={0.85}
          disabled={loading}
        >
          <Text style={styles.buttonText}>Tra cứu quan hệ</Text>
          <Ionicons name="git-compare-outline" size={18} color="#0c0e12" style={{ marginLeft: 8 }} />
        </TouchableOpacity>

        {/* VÒNG XOAY CHỜ */}
        {loading && (
          <ActivityIndicator
            size="large"
            color="#FF9F43"
            style={{
              marginTop: 24,
            }}
          />
        )}

        {/* KHỐI KẾT QUẢ SANG TRỌNG */}
        {!!result && (
          <View style={styles.resultBox}>
            <View style={styles.resultHeader}>
              <Text style={styles.emblemText}>🌳</Text>
              <Text style={styles.resultTitle}>Phả Hệ Kết Quả</Text>
            </View>
            <Text style={styles.resultText}>{result}</Text>

            {/* SƠ ĐỒ ĐƯỜNG ĐI CHI TIẾT */}
            {pathData && pathData.length > 0 && (
              <View style={styles.timelineContainer}>
                <Text style={styles.timelineTitle}>Sơ đồ liên kết dòng tộc:</Text>
                
                {pathData.map((item, index) => {
                  const isMale = item.gioi_tinh === "Nam";
                  
                  return (
                    <View key={item.id || index} style={styles.timelineNodeContainer}>
                      <View style={styles.timelineNode}>
                        <Image
                          source={{ uri: item.avatar || `https://avatar.iran.liara.run/username?username=${item.ho_ten}` }}
                          style={[
                            styles.timelineAvatar,
                            { borderColor: isMale ? "rgba(2, 132, 199, 0.4)" : "rgba(236, 72, 153, 0.4)" }
                          ]}
                        />
                        <View style={styles.timelineInfo}>
                          <Text style={styles.timelineName}>{item.ho_ten}</Text>
                          <Text style={styles.timelineGender}>
                            {isMale ? "Vế Nam" : "Vế Nữ"}{item.nghe_nghiep ? ` • ${item.nghe_nghiep}` : ""}
                          </Text>
                        </View>
                      </View>
                      
                      {index < pathData.length - 1 && (
                        <View style={styles.timelineConnectorContainer}>
                          <View style={styles.timelineLine} />
                          <View style={styles.timelineStepBadge}>
                            <Ionicons name="arrow-down-outline" size={11} color="#D97706" />
                            <Text style={styles.timelineStepText}>
                              {pathSteps[index] === "parent" ? "Con của" :
                               pathSteps[index] === "child" ? "Cha/mẹ của" :
                               pathSteps[index] === "spouse" ? "Vợ/Chồng của" :
                               pathSteps[index] === "sibling" ? "Anh/Chị/Em của" : "Có liên hệ"}
                            </Text>
                          </View>
                        </View>
                      )}
                    </View>
                  );
                })}
              </View>
            )}
          </View>
        )}
      </ScrollView>

      {/* MODAL SEARCHABLE BOTTOM SHEET CHỌN THÀNH VIÊN */}
      <Modal
        visible={showSelector}
        animationType="slide"
        transparent={true}
        onRequestClose={() => setShowSelector(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            
            {/* THANH NGANG THẨM MỸ Ở ĐỈNH */}
            <View style={styles.modalIndicator} />
            
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {selectorTarget === 1 ? "CHỌN NGƯỜI THỨ NHẤT" : "CHỌN NGƯỜI THỨ HAI"}
              </Text>
              <TouchableOpacity
                style={styles.closeBtn}
                onPress={() => setShowSelector(false)}
              >
                <Ionicons name="close-outline" size={24} color="#64748B" />
              </TouchableOpacity>
            </View>

            {/* THANH TÌM KIẾM SẮC NÉT CHUYÊN NGHIỆP */}
            <View style={styles.searchContainer}>
              <Ionicons name="search-outline" size={20} color="#FF9F43" style={styles.searchIcon} />
              <TextInput
                style={styles.searchInput}
                placeholder="Tìm họ tên hoặc nghề nghiệp..."
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

            {/* DANH SÁCH THÀNH VIÊN */}
            <FlatList
              data={filteredMembers}
              keyExtractor={(item) => item.id.toString()}
              showsVerticalScrollIndicator={false}
              contentContainerStyle={{ paddingBottom: 30, paddingTop: 10 }}
              ListEmptyComponent={
                <View style={styles.emptyContainer}>
                  <Ionicons name="sad-outline" size={40} color="#94A3B8" />
                  <Text style={styles.emptyText}>Không tìm thấy thành viên nào phù hợp</Text>
                </View>
              }
              renderItem={({ item }) => {
                const isSelected =
                  selectorTarget === 1 ? nguoi1 === item.id : nguoi2 === item.id;
                
                return (
                  <TouchableOpacity
                    style={[styles.memberItem, isSelected && styles.memberItemActive]}
                    onPress={() => selectMember(item)}
                    activeOpacity={0.7}
                  >
                    <Image
                      source={{
                        uri:
                          item.avatar ||
                          "https://avatar.iran.liara.run/public",
                      }}
                      style={styles.memberAvatar}
                    />
                    <View style={styles.memberInfo}>
                      <Text style={styles.memberName}>{item.ho_ten}</Text>
                      <Text style={styles.memberSub}>
                        {item.gioi_tinh} • {item.nghe_nghiep || "Chưa cập nhật nghề"}
                      </Text>
                    </View>
                    {isSelected ? (
                      <Ionicons name="checkmark-circle" size={24} color="#FF9F43" />
                    ) : (
                      <View style={styles.radioOutline} />
                    )}
                  </TouchableOpacity>
                );
              }}
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
  scrollContainer: {
    flexGrow: 1,
    padding: 22,
    paddingTop: Platform.OS === "ios" ? 60 : 40,
    paddingBottom: 50,
  },
  glowSphere: {
    position: "absolute",
    width: 300,
    height: 300,
    borderRadius: 150,
    opacity: 0.12,
  },
  glowGold: {
    backgroundColor: "#FFE3C2",
  },
  glowAmber: {
    backgroundColor: "#FFD5C2",
  },
  title: {
    fontSize: 28,
    fontWeight: "900",
    marginBottom: 24,
    color: "#0F172A",
    letterSpacing: 0.5,
    textAlign: "left",
  },
  card: {
    backgroundColor: "#ffffff",
    borderRadius: 22,
    padding: 18,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 3,
  },
  label: {
    fontSize: 12,
    fontWeight: "800",
    color: "#D97706",
    marginBottom: 12,
    textTransform: "uppercase",
    letterSpacing: 1.2,
  },
  selectorTrigger: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.2)",
    borderRadius: 16,
    paddingHorizontal: 16,
    paddingVertical: 12,
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
    fontSize: 12,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  connectorArrow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    marginVertical: 10,
    paddingHorizontal: 30,
  },
  connectorLine: {
    flex: 1,
    height: 1.5,
    backgroundColor: "rgba(255, 159, 67, 0.18)",
  },
  connectorIconContainer: {
    width: 32,
    height: 32,
    borderRadius: 16,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.25)",
    backgroundColor: "#ffffff",
    justifyContent: "center",
    alignItems: "center",
    marginHorizontal: 12,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  button: {
    backgroundColor: "#FF9F43",
    flexDirection: "row",
    paddingVertical: 18,
    borderRadius: 18,
    alignItems: "center",
    justifyContent: "center",
    marginTop: 24,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.25,
    shadowRadius: 14,
    elevation: 6,
  },
  buttonDisabled: {
    backgroundColor: "#CBD5E1",
    shadowOpacity: 0,
    elevation: 0,
  },
  buttonText: {
    color: "#0c0e12",
    fontWeight: "900",
    fontSize: 16,
    letterSpacing: 0.8,
    textTransform: "uppercase",
  },
  resultBox: {
    marginTop: 28,
    backgroundColor: "#ffffff",
    borderRadius: 24,
    borderWidth: 1.5,
    borderColor: "#FF9F43",
    padding: 22,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.1,
    shadowRadius: 12,
    elevation: 4,
  },
  resultHeader: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 12,
  },
  emblemText: {
    fontSize: 22,
    marginRight: 8,
  },
  resultTitle: {
    fontSize: 14,
    fontWeight: "900",
    color: "#D97706",
    letterSpacing: 1.2,
    textTransform: "uppercase",
  },
  resultText: {
    fontSize: 17,
    lineHeight: 28,
    color: "#1E293B",
    fontWeight: "800",
    marginBottom: 20,
  },
  
  // SƠ ĐỒ TIMELINE ĐƯỜNG ĐI QUAN HỆ CỰC ĐẸP
  timelineContainer: {
    borderTopWidth: 1.5,
    borderTopColor: "rgba(255, 159, 67, 0.15)",
    paddingTop: 18,
  },
  timelineTitle: {
    fontSize: 13,
    fontWeight: "800",
    color: "#D97706",
    marginBottom: 18,
    textTransform: "uppercase",
    letterSpacing: 1,
  },
  timelineNodeContainer: {
    alignItems: "stretch",
  },
  timelineNode: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "rgba(255, 159, 67, 0.03)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.15)",
    borderRadius: 16,
    padding: 12,
  },
  timelineAvatar: {
    width: 42,
    height: 42,
    borderRadius: 21,
    borderWidth: 2,
    marginRight: 12,
  },
  timelineInfo: {
    flex: 1,
  },
  timelineName: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1E293B",
  },
  timelineGender: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "700",
    marginTop: 2,
  },
  timelineConnectorContainer: {
    height: 44,
    alignItems: "center",
    justifyContent: "center",
    position: "relative",
  },
  timelineLine: {
    position: "absolute",
    width: 2,
    top: 0,
    bottom: 0,
    backgroundColor: "rgba(255, 159, 67, 0.25)",
  },
  timelineStepBadge: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#FFF8F2",
    borderWidth: 1,
    borderColor: "#FF9F43",
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 4,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    elevation: 2,
    zIndex: 2,
  },
  timelineStepText: {
    fontSize: 11,
    fontWeight: "800",
    color: "#D97706",
    marginLeft: 4,
  },

  // MODAL SEARCHABLE BOTTOM SHEET
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.4)",
    justifyContent: "flex-end",
  },
  modalContent: {
    backgroundColor: "#ffffff",
    borderTopLeftRadius: 32,
    borderTopRightRadius: 32,
    height: "78%",
    paddingHorizontal: 24,
    paddingTop: 12,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: -10 },
    shadowOpacity: 0.15,
    shadowRadius: 20,
    elevation: 24,
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
    fontSize: 15,
    fontWeight: "700",
    padding: 0,
  },
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 50,
  },
  emptyText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 10,
    textAlign: "center",
  },
  memberItem: {
    flexDirection: "row",
    alignItems: "center",
    paddingVertical: 14,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: "rgba(255, 159, 67, 0.06)",
    borderRadius: 14,
  },
  memberItemActive: {
    backgroundColor: "rgba(255, 159, 67, 0.06)",
    borderColor: "rgba(255, 159, 67, 0.15)",
  },
  memberAvatar: {
    width: 48,
    height: 48,
    borderRadius: 24,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.25)",
    marginRight: 14,
  },
  memberInfo: {
    flex: 1,
  },
  memberName: {
    fontSize: 15,
    fontWeight: "800",
    color: "#1E293B",
  },
  memberSub: {
    fontSize: 12,
    color: "#64748B",
    fontWeight: "600",
    marginTop: 2,
  },
  radioOutline: {
    width: 22,
    height: 22,
    borderRadius: 11,
    borderWidth: 2,
    borderColor: "rgba(255, 159, 67, 0.3)",
    backgroundColor: "#ffffff",
  },
});