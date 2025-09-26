<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">ลงทะเบียน</h2>
    <form @submit.prevent="addstudents">
        <div class="mb-2">
        <input v-model="students.student_id" class="form-control" placeholder="รหัสนักศึกษา" required />
      </div>
      <div class="mb-2">
        <input v-model="students.first_name" class="form-control" placeholder="ชื่อ" required />
      </div>
      <div class="mb-2">
        <input v-model="students.last_name" class="form-control" placeholder="นามสกุล" required />
      </div>
      <div class="mb-2">
        <input v-model="students.email" class="form-control" placeholder="อีเมล" required />
      </div>
      <div class="mb-2">
        <input v-model="students.phone" class="form-control" placeholder="เบอร์โทร" required />
      </div>
      
      <div class="text-center mt-4 ">
      <button type="submit" class="btn btn-primary mb-4">บันทึก</button> &nbsp;
      <button type="reset" class="btn btn-secondary mb-4">ยกเลิก</button>
      </div>
    </form>

    <div v-if="message" class="alert alert-info mt-3">
      {{ message }}
    </div>
  </div>
</template>


<script>
import students from './students.vue';

export default {
  data() {
    return {
      students: {
        student_id: "",
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
      },
      message: ""
    };
  },
  methods: {
    async addstudents() {
      try {
        const res = await fetch("http://localhost:8082/project_vue/api.php/api_student.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.students)
        });
        const data = await res.json();
        this.message = data.message;

        if (data.success) {
          // ✅ เคลียร์ข้อมูลใน textbox หลังบันทึกสำเร็จ
          this.students = { student_id : "",first_name: "", last_name: "", email: "", phone: "",   };
        }

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
