import {
  View,
  Text,
  StyleSheet,
  Image,
} from "react-native";

export default function FamilyNode({
  husband,
  wife,
}: any) {

  const renderPerson = (
    person: any
  ) => {
    if (!person) return null;

    return (
      <View style={styles.personBox}>
        <Image
          source={{
            uri: person.avatar || "https://avatar.iran.liara.run/public",
          }}
          style={styles.avatar}
        />

        <Text style={styles.name} numberOfLines={2}>
          {person.ho_ten}
        </Text>

        <Text style={styles.job} numberOfLines={1}>
          {person.nghe_nghiep || "Chưa cập nhật"}
        </Text>

        <View
          style={[
            styles.status,
            {
              backgroundColor:
                person.trang_thai === "Còn sống"
                  ? "rgba(76, 175, 80, 0.12)"
                  : "rgba(244, 67, 54, 0.12)",
            },
          ]}
        >
          <Text
            style={{
              color:
                person.trang_thai === "Còn sống"
                  ? "#4CAF50"
                  : "#F44336",
              fontSize: 10,
              fontWeight: "700",
              textTransform: "uppercase",
              letterSpacing: 0.5,
            }}
          >
            {person.trang_thai}
          </Text>
        </View>
      </View>
    );
  };

  return (
    <View style={styles.card}>
      {renderPerson(husband)}
      {wife && (
        <View style={styles.line} />
      )}
      {renderPerson(wife)}
    </View>
  );
}

const styles = StyleSheet.create({
  card: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff", // Nền thẻ trắng tinh khôi vương giả
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.15)", // Viền cam nhẹ tinh tế đồng bộ
    borderRadius: 24,
    padding: 16,
    shadowColor: "#FF9F43",
    shadowOffset: {
      width: 0,
      height: 6,
    },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 4,
  },

  personBox: {
    alignItems: "center",
    width: 140,
  },

  avatar: {
    width: 74,
    height: 74,
    borderRadius: 37,
    borderWidth: 1.5,
    borderColor: "rgba(255, 159, 67, 0.35)", // Viền cam ôm quanh ảnh cực đẹp
    marginBottom: 10,
    backgroundColor: "rgba(255, 255, 255, 0.05)",
  },

  name: {
    fontWeight: "800",
    textAlign: "center",
    fontSize: 13,
    color: "#1E293B", // Chữ tối màu sắc nét dễ đọc ở nền sáng
    paddingHorizontal: 6,
    lineHeight: 18,
  },

  job: {
    marginTop: 4,
    fontSize: 11,
    fontWeight: "700",
    textAlign: "center",
    color: "#D97706", // Màu cam đậm sắc sảo nổi bật
    paddingHorizontal: 4,
  },

  status: {
    marginTop: 8,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },

  line: {
    width: 32,
    height: 4,
    backgroundColor: "#FF5E36", // Giữ nguyên đường nối đỏ cam rực rỡ tuyệt đẹp
    borderRadius: 10,
    marginHorizontal: 8,
    shadowColor: "#FF5E36",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.25,
    shadowRadius: 4,
  },
});