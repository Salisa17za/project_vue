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
           <td>
            <!-- เพิ่ม ปุ่มแก้ไข -->
            <button class="btn btn-warning btn-sm" @click="openEditModal(student)"><i class="fa-solid fa-pen-to-square"></i>แก้ไข</button> |      
            <!-- ปุ่มลบ -->
            <button class="btn btn-danger btn-sm" @click="deletestudents(student.student_id)"><i class="fa-solid fa-delete-left"></i></button>
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
 <!-- เพิ่ม Modal แก้ไขข้อมูล -->
    <div class="modal fade" id="editModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">แก้ไขข้อมูลนักเรียน</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updatestudent">
              <div class="mb-3">
                <label class="form-label">ชื่อ</label>
                <input v-model="editstudent.first_name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">นามสกุล</label>
                <input v-model="editstudent.last_name" type="text" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">อีเมล</label>
                <input v-model="editstudent.email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label class="form-label">เบอร์โทร</label>
                <input v-model="editstudent.phone" type="text" class="form-control" required>
              </div>
              <div class="mb-3 text-center">
              <button type="submit" class="btn btn-success">บันทึก</button> </div>
            </form>
          </div>
        </div>
      </div>
    </div>


  </div>
</template>

<script>
import { ref, onMounted } from "vue";
import { Modal } from "bootstrap";   // เพิ่ม ✅ import Modal class

export default {
  name: "studentsList",
  setup() {
    const students = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const editstudent = ref({});   //เพิ่ม
    let editModal;                  //เพิ่ม

    // ฟังก์ชันดึงข้อมูลจาก API ด้วย GET
    const fetchstudents = async () => {
      try {
        const response = await fetch("http://localhost:8082/project_vue/api.php/student_crud.php", {
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
      const modalEl = document.getElementById("editModal");     //เพิ่ม
      editModal = new Modal(modalEl);   // เพิ่ม ✅ ใช้ Modal ที่ import มา
    });
    
    //เพิ่ม เปิด Popup Modal ***
    const openEditModal = (student) => {
      editstudent.value = { ...student };
      editModal.show();
    };
// เพิ่มฟังก์ชั่นการแก้ไขข้อมูล ***
    const updatestudent = async () => {
      try {
        const response = await fetch("http://localhost:8082/project_vue/api.php/student_crud.php", {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(editstudent.value)
        });

        const result = await response.json();

        if (result.success) {
          const index = students.value.findIndex(c => c.student_id === editstudent.value.student_id);
          if (index !== -1) students.value[index] = { ...editstudent.value };

          alert("แก้ไขข้อมูลสำเร็จ");
          editModal.hide();
        } else {
          alert(result.message);
        }
      } catch (err) {
        alert("เกิดข้อผิดพลาด: " + err.message);
      }
    };

  //ฟังก์ชั่นการลบข้อมูล ***
const deletestudents = async (id) => {
  if (!confirm("คุณต้องการลบข้อมูลนี้ใช่หรือไม่?")) return;

  try {
    const response = await fetch("http://localhost:8082/project_vue/api.php/student_crud.php", {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ student_id: id })
    });

    const result = await response.json();

    if (result.success) {
      // ลบออกจาก customers ทันที (ไม่ต้องโหลดใหม่)
      students.value = students.value.filter(s => s.student_id !== id);
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
      editstudent,
      editModal,
      updatestudent,
      openEditModal,
      deletestudents,   //เรียกใช้งานฟังก์ชั่นการลบข้อมูล ***
      error
    };
  
  
  }
};




















</script>