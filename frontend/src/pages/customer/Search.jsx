import { useEffect, useState } from "react";
import { searchAvailableFields } from "../../api/search.api";
import FieldCard from "../../components/customer/FieldCard";

function Search() {

    const API_BASE_SEARCH = "http://localhost/football-booking-system/backend-php/searchs/api.php";
    const [branches, setBranches] = useState([]); // state lưu danh sách Branches 

  const [form, setForm] = useState({
    branch_id: "",
    // field_type_id: "",
  });

  const [loading, setLoading] = useState(false);
  const [fields, setFields] = useState([]);

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };


//TODO: hàm tìm kiếm
  const handleSearch = async () => {
    try {
      setLoading(true);
      const data = await searchAvailableFields(form);
      setFields(data.data);
    } catch (err) {
      console.log("Không tìm được sân", err);
    } finally {
      setLoading(false);
    }
  };

  //TODO Api fetch danh sách branch_id và branch_name
  useEffect(() => {
    const handleBranches = async () => {
    try{
        const res = await fetch(`${API_BASE_SEARCH}?action=get`);
        if(!res.ok) {
            throw new Error("ERROR HTTP: ", res.status);
        }

        const data = await res.json();
        if(data.success) {
            setBranches(data.data);
        }
    } catch(err) {
        console.error("Error fetching branches ", err);
    }
  }
  handleBranches();
  }, []);

  
  //TODO: Fetch danh sách các loại sân, khi đã chọn một chi nhánh cụ thể.
//   const fetchFieldTypes = async () => {
//     try{

//     } catch(err) {

//     }
//   }

  return (
    <div className="p-6 max-w-5xl mx-auto">
      <h1 className="text-2xl font-bold mb-4">Tìm sân bóng trống</h1>

       {/* Yêu cầu người dùng chọn chi nhánh trước, sau đó lọc các file_type theo chi nhánh đó */}

      {/* FORM SEARCH */}
      <div className="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        {/* <input
          name="branch_id"
          placeholder="Chi nhánh ID"
          onChange={handleChange}
          className="border p-2 rounded"
        /> */}
        <select 
            name="branch_id" 
            value={form.branch_id} 
            onChange={handleChange}
            className="border p-2 rounded"
        >
            <option value="">-- Chọn chi nhánh --</option>
            {branches.map(b => (
                <option value={b.branch_id} key={b.branch_id}>{b.branch_name}</option>
            ))}
        </select>

        {/* <input
          name="field_type_id"
          value={form.field_type_id}
          placeholder="Loại sân (5/7/11)"
          onChange={handleChange}
          className="border p-2 rounded"
        /> */}
      </div>

      <button
        onClick={handleSearch}
        className="px-6 py-2 bg-green-600 text-white rounded"
      >
        {loading ? "Đang tìm..." : "Tìm sân"}
      </button>

      {/* RESULT */}
      <div className="grid md:grid-cols-3 gap-4 mt-6">
        {fields.map((field) => (
          <FieldCard key={field.field_id} field={field} />
        ))}
      </div>

      {fields.length === 0 && !loading && (
        <p className="mt-6 text-gray-500">Không có sân trống</p>
      )}
    </div>
  );
}

export default Search;
