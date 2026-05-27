import axios from "axios";

import { layToken }
from "./storage";

const api = axios.create({
  baseURL: "http://192.168.1.8:8000/api",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

// AUTO GẮN TOKEN
api.interceptors.request.use(
  async (config) => {

    const token =
      await layToken();

    if (token) {

      config.headers.Authorization =
        `Bearer ${token}`;

    }

    return config;

  },
  (error) => {
    return Promise.reject(error);
  }
);

export default api;