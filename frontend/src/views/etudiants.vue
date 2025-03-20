<template>
  <Layout>
    <div class="header-container custom-header">
      <PageHeader title="Gestion des etudiants" pageTitle="HOME" pageRoute="/admin" />
    </div>
    <br>

    <div class="table-responsive table-card mt-4">
      <table class="table align-middle table-nowrap mb-0" id="tasksTable">
        <thead class="table-light text-muted">
        <tr>
          <th>ID</th>
          <th>nom</th>
          <th>prenom</th>
          <th>email</th>
          <th>tel</th>
          <th>niveauBac</th>
          <th>ville</th>
          <th>Tests</th>
          <th>action</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="etudiant in etudiants" :key="etudiant.id">
          <td>{{ etudiant.id }}</td>
          <td>{{ etudiant.nom }}</td>
          <td>{{ etudiant.prenom }}</td>
          <td>{{ etudiant.email }}</td>
          <td>{{ etudiant.tel }}</td>
          <td>{{ etudiant.niveauBac }}</td>
          <td>{{ etudiant.ville }}</td>
          <td><li class="list-inline-item" @click.prevent="showEtudiantTests(etudiant)">
            <a href="#"><i class="ri-eye-fill align-bottom me-2 text-muted"></i></a>
          </li>
          </td>


          <td>
            <ul class="list-inline tasks-list-menu mb-0">
              <li class="list-inline-item" @click.prevent="editDetails(etudiant)">
                <a href="#"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i></a>
              </li>
              <li class="list-inline-item">
                <a class="remove-item-btn" href="javascript:void(0);" @click.prevent="deleteForm(etudiant)">
                  <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                </a>
              </li>
            </ul>
          </td>
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
      etudiants: [],
    };
  },
  mounted() {
    this.fetchEtudiants();
  },
  methods: {
    async fetchEtudiants() {
      try {
        const response = await axios.get('/get/etudiants');
        this.etudiants = response.data;
      } catch (error) {
        console.error('Error fetching Etudiants:', error);
      }
    },
    editDetails(etudiant) {
      this.$router.push({ name: 'editEtudiant', params: { id: parseInt(etudiant.id, 10) } });
    },
    async deleteForm(etudiant) {
      try {
        await axios.delete(`/delete/etudiant/${etudiant.id}`);
        this.fetchEtudiants(); // Refresh the list
      } catch (error) {
        console.error('Error deleting form:', error);
        // Handle the error appropriately (e.g., display an error message)
      }
    },
    showEtudiantTests(etudiant) {
      this.$router.push({ name: 'etudiantTests', params: { id: parseInt(etudiant.id, 10) } });
    },
  },
};
</script>
