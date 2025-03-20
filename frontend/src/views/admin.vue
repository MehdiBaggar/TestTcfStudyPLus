<template>
  <Layout>
    <div class="header-container custom-header">
      <PageHeader title="DASHBOARD" pageTitle="HOME" pageRoute="/admin" />
    </div>
    <br>
    <BRow>
      <BCol xxl="3" sm="6">
        <BCard no-body class="card-animate">
          <BCardBody>
            <div class="d-flex justify-content-between">
              <div>
                <p class="fw-semibold text-muted mb-0">TOTAL ETUDIANTS</p>
                <h2 class="mt-4 ff-secondary fw-semibold">
                  {{ etudiants.length }}
                </h2>
                <p class="mb-0 text-muted">
                  <BBadge class="bg-light text-success mb-0">
                    <router-link to="/allEtudiants" style="color: inherit; text-decoration: none;">
                      <i class="ri-arrow-right-line"></i>
                    </router-link>
                  </BBadge>
                  Check all etudiants
                </p>
              </div>
              <div>
                <div class="avatar-sm flex-shrink-0">
                  <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                    <i class="ri-user-2-fill"></i>
                  </span>
                </div>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol xxl="3" sm="6">
        <BCard no-body class="card-animate">
          <BCardBody>
            <div class="d-flex justify-content-between">
              <div>
                <p class="fw-semibold text-muted mb-0">TOTAL QUESTIONS</p>
                <h2 class="mt-4 ff-secondary fw-semibold">
                  {{ questions.length }}
                </h2>
                <p class="mb-0 text-muted">
                  <BBadge class="bg-light text-success mb-0">
                    <router-link to="/AllQuestions" style="color: inherit; text-decoration: none;"> <i class="ri-arrow-right-line"></i>
                    </router-link>
                  </BBadge>
                  Check all questions
                </p>
              </div>
              <div>
                <div class="avatar-sm flex-shrink-0">
                  <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                    <i class="ri-question-line"></i>
                  </span>
                </div>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>
      <BCol xxl="3" sm="6">
        <BCard no-body class="card-animate">
          <BCardBody>
            <div class="d-flex justify-content-between">
              <div>
                <p class="fw-semibold text-muted mb-0">TOTAL TESTS</p>
                <h2 class="mt-4 ff-secondary fw-semibold">
                  {{ tests.length }}
                </h2>
                <p class="mb-0 text-muted">
                  <BBadge class="bg-light text-success mb-0">
                    <router-link to="/AllQuestions" style="color: inherit; text-decoration: none;">
                    <i class="ri-arrow-right-line"></i> </router-link>
                  </BBadge>
                  Check all Tests
                </p>
              </div>
              <div>
                <div class="avatar-sm flex-shrink-0">
                  <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-4">
                    <i class="ri-bill-line"></i>
                  </span>
                </div>
              </div>
            </div>
          </BCardBody>
        </BCard>
      </BCol>

    </BRow>
    <BTabs content-class="mt-3">
      <BTab title="Questions" active>
        <div class="table-responsive table-card mt-4">
          <table class="table align-middle table-nowrap mb-0">
            <thead class="table-light text-muted">
            <tr>
              <th>ID</th>
              <th>Question</th>
              <th>Type</th>
              <th>Difficulty</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="question in limitedQuestions" :key="question.id">
              <td>{{ question.id }}</td>
              <td>{{ question.question }}</td>
              <td>{{ question.type }}</td>
              <td>{{ question.difficulteeeee }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </BTab>
      <BTab title="Etudiants">
        <div class="table-responsive table-card mt-4">
          <table class="table align-middle table-nowrap mb-0">
            <thead class="table-light text-muted">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="etudiant in limitedEtudiants" :key="etudiant.id">
              <td>{{ etudiant.id }}</td>
              <td>{{ etudiant.nom }} {{ etudiant.prenom }}</td>
              <td>{{ etudiant.email }}</td>
            </tr>
            </tbody>
          </table>
        </div>
      </BTab>

    </BTabs>



  </Layout>
</template>

<script>
import Layout from "@/layouts/main.vue";
import axios from 'axios';
import PageHeader from "@/components/page-header";


export default {
  components: { Layout,PageHeader },
  data() {
    return {
      questions: [],
      etudiants: [],
      tests: [],
    };
  },
  mounted() {
    this.fetchQuestions();
    this.fetchEtudiants();
    this.fetchTests();
  },
  computed: {
    limitedQuestions() {
      return this.questions.slice(0, 8); // Get only the first 8 questions
    },
    limitedEtudiants() {
      return this.etudiants.slice(0, 8); // Get only the first 8 etudiants
    },
  },
  methods: {
    async fetchQuestions() {
      try {
        const response = await axios.get('/get/questions'); // Your API endpoint
        this.questions = response.data;
      } catch (error) {
        console.error('Error fetching questions:', error);
      }
    },
    async fetchEtudiants() {
      try {
        const response = await axios.get('/get/etudiants'); // Your API endpoint
        this.etudiants = response.data;
      } catch (error) {
        console.error('Error fetching etudiants:', error);
      }
    },
    async fetchTests() {  // Add a method to fetch tests
      try {
        const response = await axios.get('/tests'); // Replace with your actual API endpoint for tests
        this.tests = response.data;
      } catch (error) {
        console.error('Error fetching tests:', error);
      }
    },

  },
};
</script>