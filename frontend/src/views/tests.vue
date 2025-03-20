<template>
  <Layout>
    <div class="header-container custom-header">
      <PageHeader title="Gestion des Tests" pageTitle="HOME" pageRoute="/admin" />
    </div>
    <br>

    <div v-if="tests.length > 0" class="table-responsive table-card mt-4">
      <table class="table align-middle table-nowrap mb-0" id="testsTable">
        <thead class="table-light text-muted">
        <tr>
          <th>ID</th>
          <th>Date</th>
          <th>Score</th>
          <th>Niveau</th>
          <!-- Add other test properties here -->
        </tr>
        </thead>
        <tbody>
        <tr v-for="test in tests" :key="test.id">
          <td>{{ test.id }}</td>
          <td>{{ test.data }}</td>
          <td>{{ test.scoreTotal }}</td>
          <td>{{ test.niveau }}</td>
          <!-- Add other test properties here -->
        </tr>
        </tbody>
      </table>
    </div>

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
      tests: [],
    };
  },
  mounted() {
    this.fetchTests();
  },
  methods: {
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
