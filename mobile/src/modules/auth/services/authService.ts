import api from "../../../shared/services/api";

export const dangNhap = async (
  email: string,
  mat_khau: string
) => {
  const response = await api.post("/login", {
    email,
    mat_khau,
  });

  return response.data;
};