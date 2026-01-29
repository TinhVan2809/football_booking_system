// Call API tìm kiếm sân

import axios from "axios";

const API_URL = "http://localhost:8081/api";

// route serach fields
export const searchAvailableFields = async (params) => {
  const res = await axios.get(`${API_URL}/search/available-fields`, {
    params,
  });
  return res.data;
};
