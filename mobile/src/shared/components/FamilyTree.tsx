import { StyleSheet, View } from "react-native";

import FamilyNode from "./FamilyNode";

export default function FamilyTree({ members }: any) {
  
  // ROOT
  const roots = members.filter(
    (x: any) =>
      x.cha_id === null &&
      x.loai_quan_he !== "Vợ/Chồng"
  );

  const renderChildren = (parentId: number) => {

    const children = members.filter(
      (x: any) =>
        x.cha_id === parentId &&
        x.loai_quan_he !== "Vợ/Chồng"
    );

    if (children.length === 0) {
      return null;
    }

    return (
      <View style={styles.childrenWrapper}>

        {/* ĐƯỜNG NỐI TỪ CHA MẸ XUỐNG */}
        <View style={styles.parentDropLine} />

        {/* DANH SÁCH CON */}
        <View style={styles.childrenRow}>

          {children.map(
            (child: any, index: number) => {

              const vo = members.find(
                (v: any) =>
                  v.spouse_of_id === child.id
              );

              return (
                <View
                  key={child.id}
                  style={styles.childBlock}
                >

                  {/* NHÁNH NGANG & DỌC CỦA TỪNG CON */}
                  <View style={styles.branchLineContainer}>
                    <View
                      style={[
                        styles.branchLineLeft,
                        { borderTopWidth: index === 0 ? 0 : 4 },
                      ]}
                    />
                    <View style={styles.branchLineCenter} />
                    <View
                      style={[
                        styles.branchLineRight,
                        { borderTopWidth: index === children.length - 1 ? 0 : 4 },
                      ]}
                    />
                  </View>

                  {/* NODE */}
                  <View style={styles.nodeContainer}>
                    <FamilyNode
                      husband={child}
                      wife={vo}
                    />
                  </View>

                  {/* CON CHÁU */}
                  {renderChildren(child.id)}

                </View>
              );
            }
          )}

        </View>

      </View>
    );
  };

  return (
    <View style={styles.container}>

      {roots.map((root: any) => {

        const vo = members.find(
          (v: any) =>
            v.spouse_of_id === root.id
        );

        return (
          <View
            key={root.id}
            style={styles.rootBlock}
          >

            {/* ROOT */}
            <FamilyNode
              husband={root}
              wife={vo}
            />

            {/* CON */}
            {renderChildren(root.id)}

          </View>
        );
      })}

    </View>
  );
}

const styles = StyleSheet.create({

  container: {
    paddingVertical: 60,
    paddingHorizontal: 120,
    alignItems: "center",
  },

  rootBlock: {
    alignItems: "center",
    marginBottom: 40,
  },

  childrenWrapper: {
    alignItems: "center",
  },

  childrenRow: {
    flexDirection: "row",
    justifyContent: "center",
    alignItems: "flex-start",
  },

  childBlock: {
    alignItems: "center",
  },

  nodeContainer: {
    paddingHorizontal: 20,
  },

  parentDropLine: {
    width: 4,
    height: 30,
    backgroundColor: "#FF9F43", // Đổi màu nhánh từ vàng đồng cũ sang vàng cam rực rỡ
  },

  branchLineContainer: {
    flexDirection: "row",
    width: "100%",
  },

  branchLineLeft: {
    flex: 1,
    borderColor: "#FF9F43", // Đổi màu đường biên nhánh ngang
  },

  branchLineCenter: {
    width: 4,
    height: 30,
    backgroundColor: "#FF9F43", // Đổi màu nhánh dọc trung tâm
  },

  branchLineRight: {
    flex: 1,
    borderColor: "#FF9F43", // Đổi màu đường biên nhánh ngang
  },

});