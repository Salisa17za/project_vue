<template>
  <div class="container mt-4">
    <h2 class="mb-3">รายชื่อนักเรียน</h2>
    
    <div class="mb-3">
      <a class="btn btn-primary" href="/stu" role="button">Add+</a>
    </div>

    <!-- ตารางแสดงข้อมูลลูกค้า -->
    <table class="table table-bordered table-striped">
      <thead class="table-primary">
        <tr>
          <th>Student ID</th>
          <th>ชื่อ</th>
          <th>นามสกุล</th>
          <th>Email</th>
          <th>เบอร์โทร</th>
          <th>วันที่ลงทะเบียน</th>
          <th>ลบ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="student in students" :key="student.students_id">
          <td>{{ student.student_id }}</td>
          <td>{{ student.first_name }}</td>
          <td>{{ student.last_name }}</td>
          <td>{{ student.email }}</td>
          <td>{{ student.phone }}</td>
          <td>{{ student.created_at }}</td>
    
          <!--เพิ่มปุ่มลบ -->
      <td>  
  <button class="btn btn-danger btn-sm" @click="deletestudents(student.student_id)">ลบ</button>
</td>
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
  name: "studentsList",
  setup() {
    const students = ref([]);
    const loading = ref(true);
    const error = ref(null);

    // ฟังก์ชันดึงข้อมูลจาก API ด้วย GET
    const fetchstudents = async () => {
      try {
        const response = await fetch("http://localhost:8082/project_vue/api.php/api_student.php", {
          method: "GET",
          headers: {
            "Content-Type": "application/json"
          }
        });

        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        const result = await response.json();
        if (result.success) {
          students.value = result.data;
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
      fetchstudents();
    });
    
    
  //ฟังก์ชั่นการลบข้อมูล ***
const deletestudents = async (id) => {
  if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;

  try {
    const response = await fetch("http://localhost:8082/project_vue/api.php/api_student.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ student_id: id })
    });

    const result = await response.json();

    if (result.success) {
      // ลบออกจาก customers ทันที (ไม่ต้องโหลดใหม่)
      students.value = students.value.filter(c => c.student_id !== id);
      alert(result.message);
    } else {
      alert(result.message);
    }

  } catch (err) {
    alert("เกิดข้อผิดพลาด: " + err.message);
  }
};
 

    return {
      students,
      loading,
      deletestudents,   //เรียกใช้งานฟังก์ชั่นการลบข้อมูล ***
      error
    };
  
  
  }
};
</script>