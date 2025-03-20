<template>
  <Layout>
    <h1>Tests for Etudiant ID: {{ etudiantId }}</h1>

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
    <div v-else>
      <p>No tests found for this etudiant.</p>
    </div>
  </Layout>
</template>

<script>
import Layout from "@/layouts/main.vue";
import axios from 'axios';

export default {
  components: { Layout },
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      tests: [],
    };
  },
  mounted() {
    this.fetchTestsForEtudiant();
  },
  methods: {
    async fetchTestsForEtudiant() {
      try {
        const response = await axios.get(`/etudiant/${this.id}/tests`);
        this.tests = response.data;
      } catch (error) {
        console.error('Error fetching tests:', error);
        alert('Failed to load tests.');
      }
    },
  },
};
</script>