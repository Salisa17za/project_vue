<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายการพนักงาน</h2>

    <div class="mb-3">
      <button class="btn btn-primary" @click="openAddModal">เพิ่มพนักงาน+</button>
    </div>

    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>ชื่อผู้ใช้</th>
          <th>รหัสผ่าน</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.employee_id">
          <td>{{ employee.employee_id }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.username }}</td>
          <td>
            <button class="btn btn-warning btn-sm me-2" @click="openEditModal(employee)">
              แก้ไข
            </button>
            <button class="btn btn-danger btn-sm" @click="deleteEmployee(employee.employee_id)">
              ลบ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal สำหรับเพิ่ม / แก้ไขพนักงาน -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditMode ? "แก้ไขพนักงาน" : "เพิ่มพนักงานใหม่" }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveEmployee">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editForm.first_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editForm.last_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">ชื่อผู้ใช้</label>
                <input v-model="editForm.username" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">รหัสผ่าน</label>
                <input v-model="editForm.password" type="password" class="form-control" required />
              </div>

              <button type="submit" class="btn btn-success">
                {{ isEditMode ? "บันทึกการแก้ไข" : "บันทึกพนักงานใหม่" }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from "vue";

export default {
  name: "EmployeeList",
  setup() {
    const employees = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const isEditMode = ref(false); // ✅ เช็คโหมด
    const editForm = ref({
      employee_id: null,
      first_name: "",
      last_name: "",
      username: "",
      password: "",
    });
    let modalInstance = null;

    // โหลดข้อมูลพนักงาน
    const fetchEmployees = async () => {
      try {
        const res = await fetch("http://localhost:8082/project_vue/api.php/api_employee.php");
        const data = await res.json();
        employees.value = data.success ? data.data : [];
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เปิด Modal สำหรับเพิ่มพนักงาน
    const openAddModal = () => {
      isEditMode.value = false;
      editForm.value = {
        employee_id: null,
        first_name: "",
        last_name: "",
        username: "",
        password: "",
      };
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    // เปิด Modal สำหรับแก้ไขพนักงาน
    const openEditModal = (employee) => {
      isEditMode.value = true;
      editForm.value = { ...employee };
      const modalEl = document.getElementById("editModal");
      modalInstance = new window.bootstrap.Modal(modalEl);
      modalInstance.show();
    };

    // บันทึกข้อมูลพนักงาน (เพิ่ม / แก้ไข)
    const saveEmployee = async () => {
      const formData = new FormData();
      formData.append("action", isEditMode.value ? "update" : "add");
      if (isEditMode.value) formData.append("employee_id", editForm.value.employee_id);
      formData.append("first_name", editForm.value.first_name);
      formData.append("last_name", editForm.value.last_name);
      formData.append("username", editForm.value.username);
      formData.append("password", editForm.value.password);

      try {
        const res = await fetch("http://localhost:8082/project_vue/api.php/api_employee.php", {
          method: "POST",
          body: formData,
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          fetchEmployees();
          modalInstance.hide();
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    // ลบพนักงาน
    const deleteEmployee = async (id) => {
      if (!confirm("คุณแน่ใจหรือไม่ที่จะลบพนักงานนี้?")) return;

      const formData = new FormData();
      formData.append("action", "delete");
      formData.append("employee_id", id);

      try {
        const res = await fetch("http://localhost:8082/project_vue/api.php/api_employee.php", {
          method: "POST",
          body: formData,
        });
        const result = await res.json();
        if (result.message) {
          alert(result.message);
          employees.value = employees.value.filter((e) => e.employee_id !== id);
        } else if (result.error) {
          alert(result.error);
        }
      } catch (err) {
        alert(err.message);
      }
    };

    onMounted(fetchEmployees);

    return {
      employees,
      loading,
      error,
      editForm,
      isEditMode,
      openAddModal,
      openEditModal,
      saveEmployee,
      deleteEmployee,
    };
  },
};
</script>
