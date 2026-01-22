import { useState, useEffect } from "react";
import { useParams, Link, useNavigate } from "react-router-dom";

//-----Swiper-----------------------------------------------------------
import { Swiper, SwiperSlide } from "swiper/react";
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/navigation";
import "swiper/css/pagination";
//-----Swiper------------------------------------------------------------

import { RiMapPinFill } from '@remixicon/react';

function FieldDetail() {
  const { field_id } = useParams();

  const API_BASE =
    "http://localhost/football-booking-system/backend-php/details/api.php";
  const [detail, setDetail] = useState([]);
  const [error, setError] = useState(null);
  const [loading, setloading] = useState(false);

  const navigate = useNavigate();

  useEffect(() => {
    const fetchFieldDetail = async () => {
      setloading(true);

      try {
        const res = await fetch(`${API_BASE}?action=get&field_id=${field_id}`);
        if (!res.ok) {
          throw new Error("ERROR HTTP ", res.status);
        }

        const data = await res.json();

        if (data.success) {
          setDetail(data.data);
          setloading(false);
        }
      } catch (err) {
        setError(err.message);
        console.error("Error fetching field error ", err);
      }
    };
    fetchFieldDetail();
  }, [field_id]);

  if (loading)
    return <div className="text-center mt-10">Đang tải dữ liệu...</div>;
  if (error)
    return <div className="text-center mt-10 text-red-500">Lỗi: {error}</div>;

  // Kiểm tra nếu không có dữ liệu
  if (!detail || detail.length === 0) {
    return (
      <>
        <div className="text-center mt-10">
          Không tìm thấy thông tin sân bóng.
        </div>
      </>
    );
  }

  // Lấy thông tin chung từ phần tử đầu tiên (vì field_name, address... giống nhau cho mọi loại sân của cùng 1 field_id)
  const commonInfo = detail[0];

  return (
    <>
      <section className="container mx-auto px-4 w-full h-full">
        <div className="relative w-full h-full">
          <div className="w-full h-full">
            <Swiper
              className="w-full h-156"
              modules={[Navigation, Pagination, Autoplay]}
              slidesPerView={1}
              loop={true}
              autoplay={{ delay: 4500 }}
            >
              <SwiperSlide>
                <img
                  src="../../../assets/pexels-2morrowdrm-12460951.jpg"
                  alt=""
                  className="w-full h-full object-cover"
                />
              </SwiperSlide>
              <SwiperSlide>
                <img
                  src="../../../assets/pexels-andreb1612-1615842.jpg"
                  alt=""
                  className="w-full h-full object-cover"
                />
              </SwiperSlide>
              <SwiperSlide>
                <img
                  src="../../../assets/pexels-julien-goettelmann-44396125-30326893.jpg"
                  alt=""
                  className="w-full h-full object-cover"
                />
              </SwiperSlide>
            </Swiper>
          </div>

          {/* Phần hiển thị thông tin chung của Sân */}
          <div className="fielddetail__content--info absolute z-100 top-0 w-full h-full p-5 flex items-end">
            <div className="fielddetail__content--card flex flex-col p-4 w-fit rounded-md gap-3">
              <div className="flex gap-3 items-center">
                <h1 className="text-3xl font-bold text-white">
                  {commonInfo.field_name}
                </h1>
                <span
                  className={`px-3 py-1 rounded-full text-sm font-semibold w-fit ${commonInfo.field_status === "available" ? "bg-green-100 text-green-800" : "bg-red-100 text-red-800"}`}
                >
                  {commonInfo.field_status === "available"
                    ? "Đang hoạt động/có sẵn"
                    : "Tạm ngưng"}
                </span>
              </div>
              <p className="text-xl text-white">{commonInfo.branch_name}</p>

              <p className="text-white">{commonInfo.address}</p>

              <div className="flex items-center gap-4">
                <p
                className="bg-green-600 text-white w-fit rounded-sm px-4 py-1 cursor-pointer hover:bg-green-900"
                onClick={() =>
                  navigate(`/branchDetail/${commonInfo.branch_id}`)
                }
              >
                Xem chi nhánh
              </p>
              <p
                className="bg-red-600 text-white w-fit rounded-sm px-4 py-1 cursor-pointer flex gap-1 items-center hover:bg-red-900"
                onClick={() =>
                  navigate(`/branchDetail/${commonInfo.branch_id}`)
                }
              >
                <RiMapPinFill /> xem vị trí
              </p>
              </div>
            </div>
          </div>
        </div>

        {/* Phần hiển thị danh sách các loại sân & Bảng giá */}

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {detail.map((item, index) => (
            <div
              key={index}
              className="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow p-6"
            >
              <h3 className="text-xl font-bold text-blue-600 mb-2">
                {item.type_name}
              </h3>
              <div className="text-gray-600 mb-4 space-y-1">
                <p>
                  <strong>Tối đa:</strong> {item.max_players} người
                </p>
                <p>
                  <strong>Đội hình:</strong> {item.players} vs {item.players}
                </p>
              </div>
              <div className="border-t pt-4 mt-2">
                <p className="text-sm text-gray-500">Giá thuê</p>
                <p className="text-2xl font-bold text-red-600">
                  {new Intl.NumberFormat("vi-VN", {
                    style: "currency",
                    currency: "VND",
                  }).format(Number(item.price_per_hour))}
                  <span className="text-sm font-normal text-gray-500">
                    {" "}
                    / giờ
                  </span>
                </p>
                <button className="w-full mt-4 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition duration-200">
                  Đặt sân ngay
                </button>
              </div>
            </div>
          ))}
        </div>
      </section>
    </>
  );
}

export default FieldDetail;
