import React, {
  useEffect,
  useState,
} from "react";

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
} from "react-native";

import { Ionicons } from "@expo/vector-icons";
import api from "../../../../shared/services/api";

export default function NhatKyScreen() {
  const [logs, setLogs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    loadLogs();
  }, []);

  const loadLogs = async () => {
    try {
      setLoading(true);
      const res = await api.get("/nhat-ky-hoat-dong/get-data");
      if (res.data.status) {
        const sorted = (res.data.data || []).sort((a: any, b: any) => b.id - a.id);
        setLogs(sorted);
      }
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };

  const filteredLogs = logs.filter((log) => {
    const q = searchQuery.toLowerCase();
    const actionMatch = log.hanh_dong?.toLowerCase().includes(q);
    const userMatch = log.nguoi_dung?.ho_ten?.toLowerCase().includes(q) || log.nguoi_dung?.email?.toLowerCase().includes(q);
    return actionMatch || userMatch;
  });

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      <View style={[styles.glowSphere, styles.glowAmber, { top: -80, left: -60 }]} />
      <View style={[styles.glowSphere, styles.glowGold, { bottom: -100, right: -60 }]} />

      {/* HEADER */}
      <View style={styles.header}>
        <View>
          <Text style={styles.title}>Lịch Sử Thao Tác</Text>
          <Text style={styles.subTitle}>Nhật ký hoạt động hệ thống</Text>
        </View>
        <TouchableOpacity style={styles.refreshBtn} onPress={loadLogs}>
          <Ionicons name="refresh-outline" size={22} color="#0c0e12" />
        </TouchableOpacity>
      </View>

      {/* TÌM KIẾM */}
      <View style={styles.filterSection}>
        <View style={styles.searchBar}>
          <Ionicons name="search-outline" size={20} color="#FF9F43" style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Tìm theo hành động hoặc người dùng..."
            placeholderTextColor="#94A3B8"
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
        </View>
      </View>

      {/* TIMELINE LIST */}
      {loading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#FF9F43" />
        </View>
      ) : (
        <FlatList
          data={filteredLogs}
          keyExtractor={(item) => item.id.toString()}
          contentContainerStyle={styles.listContent}
          showsVerticalScrollIndicator={false}
          ListEmptyComponent={
            <View style={styles.emptyContainer}>
              <Ionicons name="time-outline" size={60} color="#CBD5E1" />
              <Text style={styles.emptyText}>Chưa có nhật ký hoạt động nào</Text>
            </View>
          }
          renderItem={({ item, index }) => {
            const timeStr = item.created_at
              ? new Date(item.created_at).toLocaleString("vi-VN")
              : "Vừa xong";
            
            // Xác định màu sắc icon/badge dựa theo từ khóa hành động
            let iconName: keyof typeof Ionicons.glyphMap = "construct-outline";
            let iconColor = "#FF9F43";
            let bgColor = "rgba(255, 159, 67, 0.08)";

            if (item.hanh_dong?.toLowerCase().includes("thêm")) {
              iconName = "add-circle-outline";
              iconColor = "#22C55E";
              bgColor = "rgba(34, 197, 94, 0.08)";
            } else if (item.hanh_dong?.toLowerCase().includes("xóa")) {
              iconName = "trash-outline";
              iconColor = "#EF4444";
              bgColor = "rgba(239, 68, 68, 0.08)";
            } else if (item.hanh_dong?.toLowerCase().includes("đổi trạng thái") || item.hanh_dong?.toLowerCase().includes("cập nhật")) {
              iconName = "create-outline";
              iconColor = "#D97706";
              bgColor = "rgba(217, 119, 6, 0.08)";
            }

            return (
              <View style={styles.timelineRow}>
                {/* DÒNG DỌC TIMELINE */}
                <View style={styles.timelineLineContainer}>
                  <View style={[styles.timelineIconContainer, { backgroundColor: bgColor, borderColor: iconColor }]}>
                    <Ionicons name={iconName} size={16} color={iconColor} />
                  </View>
                  {index < filteredLogs.length - 1 && <View style={styles.timelineVerticalLine} />}
                </View>

                {/* THẺ NỘI DUNG NHẬT KÝ */}
                <View style={styles.logCard}>
                  <Text style={styles.logAction}>{item.hanh_dong}</Text>
                  
                  <View style={styles.logMetaRow}>
                    <Text style={styles.logUser}>
                      👤 {item.nguoi_dung ? (item.nguoi_dung.ho_ten || item.nguoi_dung.email) : "Hệ thống"}
                    </Text>
                    <Text style={styles.logTime}>🕒 {timeStr}</Text>
                  </View>
                </View>
              </View>
            );
          }}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#FFF8F2",
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
  },
  subTitle: {
    fontSize: 12,
    fontWeight: "800",
    color: "#D97706",
    textTransform: "uppercase",
    letterSpacing: 1.2,
    marginTop: 4,
  },
  refreshBtn: {
    backgroundColor: "#FF9F43",
    width: 48,
    height: 48,
    borderRadius: 16,
    justifyContent: "center",
    alignItems: "center",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
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
  },
  searchInput: {
    flex: 1,
    color: "#1E293B",
    fontSize: 14,
    fontWeight: "700",
  },
  loadingContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  listContent: {
    paddingHorizontal: 22,
    paddingBottom: 40,
    paddingTop: 10,
  },
  emptyContainer: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 80,
  },
  emptyText: {
    fontSize: 14,
    color: "#94A3B8",
    fontWeight: "700",
    marginTop: 10,
  },
  timelineRow: {
    flexDirection: "row",
    alignItems: "stretch",
    marginBottom: 0,
  },
  timelineLineContainer: {
    alignItems: "center",
    marginRight: 14,
    width: 32,
  },
  timelineIconContainer: {
    width: 32,
    height: 32,
    borderRadius: 16,
    borderWidth: 1.5,
    justifyContent: "center",
    alignItems: "center",
    zIndex: 2,
    backgroundColor: "#ffffff",
  },
  timelineVerticalLine: {
    width: 2,
    flex: 1,
    backgroundColor: "rgba(255, 159, 67, 0.2)",
    marginVertical: 4,
  },
  logCard: {
    flex: 1,
    backgroundColor: "#ffffff",
    borderRadius: 20,
    padding: 16,
    marginBottom: 18,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.12)",
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  logAction: {
    fontSize: 14.5,
    fontWeight: "800",
    color: "#1E293B",
    lineHeight: 20,
  },
  logMetaRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginTop: 10,
    borderTopWidth: 1,
    borderTopColor: "rgba(255, 159, 67, 0.06)",
    paddingTop: 8,
  },
  logUser: {
    fontSize: 12,
    color: "#D97706",
    fontWeight: "700",
  },
  logTime: {
    fontSize: 11,
    color: "#64748B",
    fontWeight: "600",
  },
});
