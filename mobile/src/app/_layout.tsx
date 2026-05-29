import { LogBox } from "react-native";
import { Stack } from "expo-router";
import { ReducedMotionConfig, ReduceMotion } from 'react-native-reanimated';

// Bỏ qua các cảnh báo về Reduced Motion của Reanimated
LogBox.ignoreLogs([
  "[Reanimated] Reduced motion setting is enabled on this device.",
  "[Reanimated] Reduced motion setting is overwritten with mode 'never'.",
]);

export default function Layout() {
  return (
    <>
      <ReducedMotionConfig mode={ReduceMotion.Never} />
      <Stack
        screenOptions={{
          headerShown: false,
        }}
      />
    </>
  );
}