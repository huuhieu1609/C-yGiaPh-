import axios from "axios";

import AsyncStorage from "@react-native-async-storage/async-storage";

const API_URL = "http://10.0.2.2:8000/api";

export const layThanhVien = async () => {

  const token =
    await AsyncStorage.getItem("token");

  const response = await axios.get(
    `${API_URL}/thanh-vien/get-data`,
    {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    }
  );

  return response.data;
};