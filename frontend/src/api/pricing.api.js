// Call API lấy danh sách bảng giá theo field_field_type_id 

import axios from "axios";

const API_URL = "http://localhost:8081/api";

export const getPricingByFieldFieldType = async (fieldFieldTypeId) => {
  const res = await axios.get(`${API_URL}/pricing/${fieldFieldTypeId}`);
  return res.data;
};
