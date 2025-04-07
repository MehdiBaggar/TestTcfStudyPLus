<template>
  <div class="test-container d-flex justify-content-center">
    <div class="test-card p-4 shadow-lg">


      <div class="d-flex justify-content-center align-items-center mb-4">
        <!-- TCF Logo -->
        <div class="logo-tcf mx-3">
          <img src="@/assets/images/tcflogo.png" alt="Logo TCF" height="40" class="logo-image" />
        </div>

        <!-- Etawjihi Image -->
        <div class="logo-etawjihi mx-3">
          <img src="@/assets/images/Logo-studyplus.png" alt="Etawjihi" height="40" class="logo-image studyplus-image mt-2" />
        </div>
      </div>
      <div v-if="showAlert" class="alert alert-danger mt-2" role="alert">
        No test found with that email address. Please add the email to do the test!
      </div>

      <form @submit.prevent="checkTestByEmail">
        <div class="mb-3">
          <label for="email" class="form-label">
            Email:
            <span class="text-danger">*</span>
          </label>
          <input
              v-model="email"
              type="email"
              class="form-control"
              id="email"
              placeholder="Enter your email"
              required
          />
        </div>

        <div class="d-flex justify-content-center mt-4">
          <button type="submit" class="btn btn-primary">soumettre</button>
        </div>
      </form>

    </div>
  </div>
</template>

<script>
import PageHeader from "@/components/page-header";
import axios from 'axios';

export default {
  components: { PageHeader },
  data() {
    return {
      email: '',
      showAlert: false,
    };
  },
  methods: {
    async checkTestByEmail() {
      try {
        const response = await axios.post(
            '/checkEmail',
            { email: this.email },
            {
              headers: {
                'Content-Type': 'application/json'
              }
            }
        );
        console.log('Response:', response.data);

        if (response.data.etudiantId) {
          this.$router.push({ name: 'TCF', params: { etudiantId: response.data.etudiantId } });
          this.showAlert = false;
        }
      } catch (error) {
        if (error.response && error.response.status === 404) {
          this.showAlert = true;
        } else {
          // Handle other errors
          console.error('Error checking email:', error);
          alert('There was an error checking the email.');
        }
      }
    },  },
};
</script>

<style scoped>
/* General Styles */
.test-container {
  background-image: url("../assets/images/backgroundstudyplus.jpg");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  /*min-height: 100vh;*/ /* Remove min-height */
  height: 100vh; /*Set height to 100vh*/
  width: 100vw;
  /*padding-top: 100px;*/ /* Remove padding */
  /*padding-bottom: 100px;*/ /* Remove padding */
  display: flex;
  justify-content: center;
  align-items: center;
}

.test-card {
  background-color: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
}



/* Logo Styles */
.logo-item {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo-image {
  height: 50px;
  max-width: 100%;
  object-fit: contain;
  margin: 0 auto;
  display: block;
}

.primary-text {
  color: #1D5F94;
}

.secondary-text {
  color: #495057;
}

.btn-primary {
  background-color: #F8B63C !important;
  border-color: #F8B63C !important;
  color: white !important;
}

.btn-primary:hover {
  background-color: #F8B63C !important;
  border-color: #14436a !important;
}


.studyplus-image {
  height:80px;
  width: auto;
  margin-left: 100px;
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
