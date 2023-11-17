<template>
  <h1 class="text-center">
    Student's Dashboard | VUE JS and PHP
    <span class="fs-6">By: Eslam Nasser</span>
  </h1>

  <div class="container">
    <div class="row">
      <!-- Show Add Student's button-->
      <div class="col-md-12" v-if="!isDbConnected">
        <div
          class="alert alert-danger alert-dismissible fade show"
          role="alert"
        >
          {{ isDbConnectedMsg }}
          <button
            type="button"
            class="btn-close"
            data-bs-dimiss="alert"
            aria-label="Close"
          ></button>
        </div>
      </div>

      <div class="col-md-12 mb-3">
        <button class="btn float-end btn-primary" @click="addModalDialog(true)">
          <Ficons :icon="['fas', 'user']" /> Add New Student
        </button>
        <button
          class="btn float-start btn-danger"
          @click="deleteAllModalDialog(true)"
          v-if="studentsData.length > 0"
        >
          <Ficons :icon="['fas', 'user-times']" /> Delete All Student's
        </button>
        <div class="clearfix"></div>
        <hr class="bg-info" />
        <div
          class="alert alert-danger alert-dismissible fade show"
          role="alert"
          v-if="errorMessage"
        >
          {{ errorMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dimiss="alert"
            aria-label="Close"
          ></button>
        </div>
        <!-- Show success Message-->
        <div
          class="alert alert-success alert-dismissible fade show"
          role="alert"
          v-if="successMessage"
        >
          {{ successMessage }}
          <button
            type="button"
            class="btn-close"
            data-bs-dimiss="alert"
            aria-label="Close"
          ></button>
        </div>
        <!-- Show Error Message-->
      </div>

      <!-- Show Add Student's Details : Tabels-->
      <div class="col-md-12">
        <table class="table table-bordered table-striped">
          <caption>
            List Registered Students ({{
              studentsData.length
            }})
          </caption>
          <thead class="bg-success text-light text-center">
            <tr>
              <th><Ficons :icon="['fas', 'id-badge']" /> ID</th>
              <th><Ficons :icon="['fas', 'user']" /> Name</th>
              <th><Ficons :icon="['fas', 'envelope']" /> Email</th>
              <th><Ficons :icon="['fas', 'phone']" /> Phone</th>
              <th><Ficons :icon="['fas', 'cog']" /> Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr
              class="text-center"
              v-for="(student, i) in studentsData"
              :key="i"
            >
              <td>{{ student.id }}</td>
              <td>{{ student.name }}</td>
              <td>{{ student.email }}</td>
              <td>{{ student.phone }}</td>
              <td class="actions-btns">
                <button
                  type="button"
                  class="btn btn-danger"
                  @click="
                    deleteModalDialog(true);
                    selectStudent(student);
                  "
                >
                  <Ficons :icon="['fas', 'user-times']" /> Delete
                </button>
                <button
                  type="button"
                  class="btn btn-warning"
                  @click="
                    updateModalDialog(true);
                    selectStudent(student);
                  "
                >
                  <Ficons :icon="['fas', 'user-edit']" /> Update
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Show Add New Student Modal-->
      <div id="overlay" v-if="showAddModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-primary">Add New User</h5>
              <button
                type="button"
                class="btn-close"
                aria-hidden="true"
                @click="addModalDialog(false)"
              >
                <Ficons :icon="['fas', 'times']" />
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @click.prevent>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.name.$error }"
                >
                  <input
                    type="text"
                    class="form-control"
                    id="studentNameFloat"
                    placeholder="jone doe"
                    v-model="newStudent.name"
                  />
                  <label for="studentNameFloat"
                    ><Ficons :icon="['fas', 'user']" /> Student Name</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.newStudent.name.$error"
                    >{{ v$.newStudent.name.$errors[0].message }}</span
                  >
                </div>

                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.email.$error }"
                >
                  <input
                    type="email"
                    class="form-control"
                    id="studentEmailFloat"
                    placeholder="jone@gmail.com"
                    v-model="newStudent.email"
                  />
                  <label for="studentEmailFloat"
                    ><Ficons :icon="['fas', 'envelope']" /> Student Email</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.newStudent.email.$error"
                    >{{ v$.newStudent.email.$errors[0].message }}</span
                  >
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.newStudent.phone.$error }"
                >
                  <input
                    type="tel"
                    class="form-control"
                    id="studentPhoneloat"
                    placeholder="01300616485"
                    v-model="newStudent.phone"
                  />
                  <label for="studentPhoneloat"
                    ><Ficons :icon="['fas', 'phone']" /> Student Number</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.newStudent.phone.$error"
                    >{{ v$.newStudent.phone.$errors[0].message }}</span
                  >
                </div>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-primary" @click="addNewUser()">
                    Add New User
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Show Edit Student Modal-->
      <div id="overlay" v-if="showUpdateModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-warning">Update User</h5>
              <button
                type="button"
                class="btn-close"
                aria-hidden="true"
                @click="updateModalDialog(false)"
              >
                <Ficons :icon="['fas', 'times']" />
              </button>
            </div>
            <div class="modal-body p-4">
              <form action="#" method="post" @click.prevent>
                <div
                  class="form-floating mb-3"
                  :class="{ 'form-group-error': v$.currentStudent.name.$error }"
                >
                  <input
                    type="text"
                    class="form-control"
                    id="studentNameFloat"
                    placeholder="jone doe"
                    v-model.trim="currentStudent.name"
                  />
                  <label for="studentNameFloat"
                    ><Ficons :icon="['fas', 'user']" /> Student Name</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.name.$error"
                    >{{ v$.currentStudent.name.$errors[0].message }}</span
                  >
                </div>

                <div
                  class="form-floating mb-3"
                  :class="{
                    'form-group-error': v$.currentStudent.email.$error,
                  }"
                >
                  <input
                    type="email"
                    class="form-control"
                    id="studentEmailFloat"
                    placeholder="jone@gmail.com"
                    v-model.trim="currentStudent.email"
                  />
                  <label for="studentEmailFloat"
                    ><Ficons :icon="['fas', 'envelope']" /> Student Email</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.email.$error"
                    >{{ v$.currentStudent.email.$errors[0].message }}</span
                  >
                </div>
                <div
                  class="form-floating mb-3"
                  :class="{
                    'form-group-error': v$.currentStudent.phone.$error,
                  }"
                >
                  <input
                    type="tel"
                    class="form-control"
                    id="studentPhoneloat"
                    placeholder="01300616485"
                    v-model.trim="currentStudent.phone"
                  />
                  <label for="studentPhoneloat"
                    ><Ficons :icon="['fas', 'phone']" /> Student Number</label
                  >
                  <span
                    class="error-feedback"
                    v-if="v$.currentStudent.phone.$error"
                    >{{ v$.currentStudent.phone.$errors[0].message }}</span
                  >
                </div>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-warning" @click="updateUser()">
                    Update Student
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Show delete Student Modal-->
      <div id="overlay" v-if="showDeleteModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger">Delete User</h5>
              <button
                type="button"
                class="btn-close"
                aria-hidden="true"
                @click="deleteModalDialog(false)"
              >
                <Ficons :icon="['fas', 'times']" />
              </button>
            </div>
            <div class="modal-body p-4">
              <h6 class="text-danger">
                Are You Sure You Want Delete This User ?
              </h6>
              <p>
                <span>
                  <Ficons :icon="['fas', 'user']" /> {{ currentStudent.name }}
                </span>
                <br />
                <span>
                  <Ficons :icon="['fas', 'envelope']" />
                  {{ currentStudent.email }}
                </span>
                <br />
                <span>
                  <Ficons :icon="['fas', 'phone']" /> {{ currentStudent.phone }}
                </span>
              </p>
              <form action="#" method="post" @click.prevent>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-danger" @click="deleteUser()">
                    Delete Student
                  </button>
                  <button
                    class="btn btn-success"
                    @click="deleteModalDialog(false)"
                  >
                    No, Don't Delete
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- Show delete All Student Modal-->
      <div id="overlay" v-if="showDeleteAllModal">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-danger">Delete All Students</h5>
              <button
                type="button"
                class="btn-close"
                aria-hidden="true"
                @click="deleteAllModalDialog(false)"
              >
                <Ficons :icon="['fas', 'times']" />
              </button>
            </div>
            <div class="modal-body p-4">
              <h6 class="text-danger">
                Are You Sure You Want Delete All Students ?
              </h6>

              <form action="#" method="post" @click.prevent>
                <hr class="bg-info" />
                <div class="d-grid gap-2">
                  <button class="btn btn-danger" @click="deleteAllUser()">
                    Delete All Students
                  </button>
                  <button
                    class="btn btn-success"
                    @click="deleteAllModalDialog(false)"
                  >
                    No, Don't Delete
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import useVuelidate from "@vuelidate/core";
import { required, email, minLength } from "@vuelidate/validators";
export default {
  name: "RegisterUsers",
  data() {
    return {
      v$: useVuelidate(),
      errorMessage: "",
      successMessage: "",
      isDbConnected: "",
      isDbConnectedMsg: "",
      studentsData: [],
      showAddModal: false,
      showUpdateModal: false,
      showDeleteModal: false,
      showDeleteAllModal: false,
      newStudent: {
        name: "",
        email: "",
        phone: "",
      },
      currentStudent: {},
    };
  },
  validations() {
    return {
      newStudent: {
        name: { required, minLength: minLength(5) },
        email: { required, email },
        phone: { required, minLength: minLength(10) },
      },
      currentStudent: {
        name: { required, minLength: minLength(5) },
        email: { required, email },
        phone: { required, minLength: minLength(10) },
      },
    };
  },
  methods: {
    addModalDialog(val) {
      this.showAddModal = val;
    },
    updateModalDialog(val) {
      this.showUpdateModal = val;
    },
    deleteModalDialog(val) {
      this.showDeleteModal = val;
    },
    deleteAllModalDialog(val) {
      this.showDeleteAllModal = val;
    },
    async getStudentsData() {
      try {
        const response = await axios.get(
          "http://localhost/students-dashboard/src/api/students.php?action=read"
        );
        if (response.status === 200) {
          const resData = response.data;
          if (resData.error) {
            this.errorMessage = resData.message;
          } else {
            this.studentsData = resData.students;
          }
        } else {
          this.errorMessage = "Error: Server returned a non-200 status code";
        }
      } catch (error) {
        this.errorMessage = "Network Error: " + error.message;
      }
    },
    async addNewUser() {
      this.clearOldMsg();
      this.v$.newStudent.$validate();

      if (!this.v$.newStudent.$error) {
        try {
          this.addModalDialog(false);
          const formData = this.toFormData(this.newStudent);
          const response = await axios.post(
            "http://localhost/students-dashboard/src/api/students.php?action=create",
            formData
          );

          if (response.status === 200) {
            const resData = response.data;

            if (resData.error) {
              this.errorMessage = resData.message;
            } else {
              this.newStudent = { name: "", email: "", phone: "" };
              this.successMessage = resData.message;
              this.getStudentsData();
              this.v$.newStudent.$reset();
            }
          } else {
            this.errorMessage = "Error: Server returned a non-200 status code";
          }
        } catch (error) {
          this.errorMessage = "Network Error: " + error.message;
        }
      } else {
        console.error("Add new user: Validation error");
      }
    },
    async updateUser() {
      this.v$.currentStudent.$validate();
      if (!this.v$.currentStudent.$error) {
        this.updateModalDialog(false);
        this.clearOldMsg();
        try {
          const formData = this.toFormData(this.currentStudent);
          const response = await axios.post(
            "http://localhost/students-dashboard/src/api/students.php?action=update",
            formData
          );
          if (response.status === 200) {
            const resData = response.data;
            if (resData.error) {
              this.errorMessage = resData.message;
            } else {
              this.currentStudent = {};
              this.successMessage = resData.message;
              this.getStudentsData();
              this.v$.currentStudent.$reset();
            }
          } else {
            this.errorMessage = "Error: Server returned a non-200 status code";
          }
        } catch (error) {
          this.errorMessage = "Network Error: " + error.message;
        }
      } else {
        console.log("Update user: error");
      }
    },
    selectStudent(std) {
      this.currentStudent = std;
    },
    async dbConnection() {
      let res = await axios.get(
        "http://localhost/students-dashboard/src/api/students.php"
      );
      const resData = res.data;
      this.isDbConnected = resData.is_db_connected;
      this.isDbConnectedMsg = resData.connection_msg;
    },
    async deleteAllUser() {
      try {
        this.deleteAllModalDialog(false);
        this.clearOldMsg;
        const response = await axios.get(
          "http://localhost/students-dashboard/src/api/students.php?action=deleteAll"
        );
        if (response.status === 200) {
          const resData = response.data;
          if (resData.error) {
            this.errorMessage = resData.message;
          } else {
            this.successMessage = resData.message;

            this.studentsData = "";
          }
        } else {
          this.errorMessage = "Error: Server returned a non-200 status code";
        }
      } catch (error) {
        this.errorMessage = "Network Error: " + error.message;
      }
    },
    clearOldMsg() {
      this.errorMessage = "";
      this.successMessage = "";
    },
    async deleteUser() {
      try {
        this.deleteModalDialog(false);
        this.clearOldMsg(); // Added parentheses to call the function
        const formData = this.toFormData(this.currentStudent);

        const response = await axios.post(
          "http://localhost/students-dashboard/src/api/students.php?action=delete",
          formData
        );
        if (response.status === 200) {
          const resData = response.data;
          if (resData.error) {
            this.errorMessage = resData.message;
          } else {
            let newStudentsData = this.studentsData.filter(
              (ele) => ele.id != this.currentStudent.id
            );
            console.table(newStudentsData);
            this.successMessage = resData.message;
            this.currentStudent = {};
            this.studentsData = newStudentsData;
            // this.getStudentsData();
          }
        } else {
          this.errorMessage = "Error: Server returned a non-200 status code";
        }
      } catch (error) {
        this.errorMessage = "Network Error: " + error.message;
      }
    },
    toFormData(obj) {
      var fd = new FormData(); // Corrected the capitalization
      for (var i in obj) {
        console.log(i + " =>  " + obj[i]);
        fd.append(i, obj[i]);
      }
      return fd; // Added return statement
    },
  },
  mounted() {
    this.getStudentsData();
    this.dbConnection();
  },
};
</script>
<style scoped>
h1 {
  margin: 0 0 20px;
  padding: 10px 0;
  background: #000;
  color: #fff;
}
table {
  width: 100%;
}
button {
  margin: 0;
}
.actions-btns button {
  margin: 0 10px;
}
.modal-dialog {
  max-width: 500px;
  background: #fff;
  padding: 15px;
  border-radius: 15px;
  margin: 50px auto;
}
#overlay {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.5);
}

.form-group-error {
  color: red;
}
.form-group-error input {
  border-color: red;
}
.table-striped > tbody > tr:nth-of-type(odd) > * {
  vertical-align: middle;
}
</style>
