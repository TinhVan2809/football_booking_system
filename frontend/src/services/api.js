// BASE_API 
// import API from ../../example.jsx => để call api ở backend-node


import axios from 'axios';

const API = axios.create({
    baseURL: "http://localhost:8081", // cổng được thiết lập ở backend node.js (.env)
    withCredentials: true
});

export default API;