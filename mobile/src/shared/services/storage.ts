import AsyncStorage from "@react-native-async-storage/async-storage";

export const luuToken = async (token: string) => {
  await AsyncStorage.setItem("token", token);
};

export const layToken = async () => {
  return await AsyncStorage.getItem("token");
};

export const xoaToken = async () => {
  await AsyncStorage.removeItem("token");
};