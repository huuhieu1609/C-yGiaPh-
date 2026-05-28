import {
  View,
  StyleSheet,
  ScrollView,
  ActivityIndicator,
  Text,
  TouchableOpacity,
  StatusBar,
  Platform,
} from "react-native";
import { useEffect, useState } from "react";
import { Ionicons } from "@expo/vector-icons";

import FamilyTree from "../../../../shared/components/FamilyTree";
import { layThanhVien } from "../../../DoiTac/services/cayGiaPhaService";

export default function CayGiaPhaScreen() {
  const [loading, setLoading] = useState(true);
  const [members, setMembers] = useState<any[]>([]);
  const [zoom, setZoom] = useState(1);

  const getData = async () => {
    try {
      setLoading(true);
      const data = await layThanhVien();
      setMembers(data?.data || []);
    } catch (error) {
      console.log(error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    getData();
  }, []);

  const tangZoom = () => {
    if (zoom < 1.8) {
      setZoom(zoom + 0.1);
    }
  };

  const giamZoom = () => {
    if (zoom > 0.4) {
      setZoom(zoom - 0.1);
    }
  };

  if (loading) {
    return (
      <View style={styles.loading}>
        <ActivityIndicator size="large" color="#FF9F43" />
      </View>
    );
  }

  return (
    <View style={styles.container}>
      <StatusBar barStyle="dark-content" />
      
      {/* HEADER */}
      <View style={styles.header}>
        <Text style={styles.title}>Cây Gia Phả</Text>

        <View style={styles.zoomGroup}>
          <TouchableOpacity
            style={styles.zoomBtn}
            onPress={getData}
            activeOpacity={0.8}
          >
            <Ionicons name="refresh-outline" size={20} color="#FF9F43" />
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.zoomBtn}
            onPress={giamZoom}
            activeOpacity={0.8}
          >
            <Text style={styles.zoomText}>-</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.zoomBtn}
            onPress={tangZoom}
            activeOpacity={0.8}
          >
            <Text style={styles.zoomText}>+</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* TREE WRAPPER */}
      <ScrollView horizontal contentContainerStyle={{ flexGrow: 1 }}>
        <ScrollView contentContainerStyle={{ flexGrow: 1 }}>
          <View
            style={[
              styles.treeContainer,
              {
                transform: [{ scale: zoom }],
              },
            ]}
          >
            <FamilyTree members={members} />
          </View>
        </ScrollView>
      </ScrollView>
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
  header: {
    height: Platform.OS === "ios" ? 110 : 90,
    backgroundColor: "#ffffff",
    borderBottomWidth: 1.5,
    borderBottomColor: "rgba(255, 159, 67, 0.25)",
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 24,
    paddingTop: Platform.OS === "ios" ? 40 : 25,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 4,
  },
  title: {
    color: "#0F172A",
    fontSize: 26,
    fontWeight: "900",
    letterSpacing: 0.8,
  },
  zoomGroup: {
    flexDirection: "row",
  },
  zoomBtn: {
    width: 44,
    height: 44,
    backgroundColor: "rgba(255, 159, 67, 0.12)",
    borderWidth: 1,
    borderColor: "rgba(255, 159, 67, 0.35)",
    borderRadius: 14,
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 10,
    shadowColor: "#FF9F43",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  zoomText: {
    fontSize: 24,
    fontWeight: "900",
    color: "#FF9F43",
  },
  treeContainer: {
    justifyContent: "center",
    alignItems: "center",
  },
});
