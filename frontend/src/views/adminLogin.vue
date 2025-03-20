<template>
  <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <div class="auth-page-content overflow-hidden pt-lg-5">
      <b-container>
        <b-row>
          <b-col lg="12">
            <b-card no-body class="overflow-hidden">
              <b-row class="g-0">
                <b-col lg="12">
                  <div class="p-lg-5 p-4">
                    <h3 class="text-center mb-4">Admin Page !</h3>
                    <br>
                    <div v-if="errorMessage" class="alert alert-danger">{{ errorMessage }}</div>


                    <form @submit.prevent="login">
                      <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            v-model="email"
                            type="email"
                            class="form-control"
                            id="email"
                            placeholder="Enter your email"
                            required
                        />
                      </div>

                      <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            v-model="password"
                            type="password"
                            class="form-control"
                            id="password"
                            placeholder="Enter your password"
                            required
                        />
                      </div>

                      <div class="mt-4">
                        <button type="submit" class="btn btn-primary w-100">Log In</button>
                      </div>
                    </form>
                  </div>
                </b-col>
              </b-row>
            </b-card>
          </b-col>
        </b-row>
      </b-container>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      email: '',
      password: '',
      csrfToken: null,
      errorMessage: null
    };
  },
  methods: {
    async fetchCsrfToken() {
      try {
        const response = await axios.get('/api/csrf-token');
        this.csrfToken = response.data.token;
      } catch (error) {
        console.error('Failed to fetch CSRF token:', error);
        this.errorMessage = 'Failed to fetch CSRF token. Please try again.';
      }
    },
    async login() {
      this.errorMessage = null;

      try {
        const params = new URLSearchParams();
        params.append('_username', this.email);
        params.append('_password', this.password);
        params.append('_csrf_token', this.csrfToken);

        const response = await axios.post('/login', params, {
          withCredentials: true,
        });

        this.$router.push('/admin');
      } catch (error) {
        console.error('Login failed:', error);

        if (error.response && error.response.status === 401) {
          this.errorMessage = 'Invalid email or password.';
        } else {
          this.errorMessage = 'Login failed. Please try again.';
        }
      }
    }
  },
  mounted() {
    this.fetchCsrfToken();
  },
};
</script>

<style scoped>
/* Use the same test-container, test-card, and other styles from your original component */
/* to maintain a consistent look and feel */
.test-container {
  background-image: url("../assets/images/test26.png");
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  /*min-height: 100vh;*/ /* Remove min-height */
  height: 100vh; /*Set height to 10vh*/
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

/* New styles for the login form */
.auth-page-wrapper {
  background-image: url('@/assets/images/test26.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
}

.auth-page-content {
  /* Add any additional styling for the content area */
}
</style>