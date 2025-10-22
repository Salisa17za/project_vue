<template>
  <div class="container mt-4">
    <h2 class="mb-3">แสดงข้อมูลพนักงาน</h2>
    
    <div class="mb-3">
      <a class="btn btn-primary" href="/epyadd" role="button">Add+</a>
    </div>

    <!-- ตารางแสดงข้อมูลพนักงาน -->
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>รหัสพนักงาน</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>ชื่อผู้ใช้</th>
          <th>รหัสผ่าน</th>
          <th>รูปภาพ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="employee in employees" :key="employee.employee_id">
          <td>{{ employee.employee_id }}</td>
          <td>{{ employee.first_name }}</td>
          <td>{{ employee.last_name }}</td>
          <td>{{ employee.username }}</td>
          <td>{{ employee.password }}</td>
          <td><img
            :src="'http://localhost/project_vue/api.php/uploads/' + employee.image"
  width="100"
  height="150"
  class="img-thumbnail rounded"
  :alt="employee.first_name"
          ></td>
        </tr>
      </tbody>
    </table>

    <!-- Loading -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
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

    // ฟังก์ชันดึงข้อมูลจาก API ด้วย GET
    const fetchemployees = async () => {
      try {
        const response = await fetch("http://localhost/project_vue/api.php/show_employee.php", {
          method: "GET",
          headers: {
            "Content-Type": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลพนักงานได้");
        }

        const result = await response.json();
        if (result.success) {
          employees.value = result.data;
        } else {
          error.value = result.message;
        }

      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchemployees();
    });
    
    return {
      employees,
      loading,
      error
    };
  }
};
</script>