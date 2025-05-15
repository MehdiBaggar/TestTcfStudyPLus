<script>
import axios from 'axios';

export default {
  data() {
    return {
      formData: {
        question: '',
        type: '',
        difficulteeeee: '',
        reponseCorrecte: '',
        reponses: [],
        audio: null, // To store the file
      },
    };
  },
  methods: {
    handleFileUpload(event) {
      this.formData.audio = event.target.files[0]; // Save the file in form data
    },
    async submitForm() {
      try {
        // Convert reponses string to an array
        this.formData.reponses = this.formData.reponses.split(',');

        const formDataToSend = new FormData();
        formDataToSend.append('question', this.formData.question);
        formDataToSend.append('type', this.formData.type);
        formDataToSend.append('difficulteeeee', this.formData.difficulteeeee);
        formDataToSend.append('reponseCorrecte', this.formData.reponseCorrecte);
        formDataToSend.append('reponses', JSON.stringify(this.formData.reponses));

        // If the type is "Compréhension orale", append the audio file
        if (this.formData.type === 'Compréhension orale' && this.formData.audio) {
          formDataToSend.append('audio', this.formData.audio);
        }

        // Send the form data to Symfony API
        const response = await axios.post('/questions', formDataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });

        if (response.status === 201) {
          alert('Question ajoutée avec succès');
          this.$router.push({ name: 'AllQuestions' });
        }
      } catch (error) {
        console.error('Erreur lors de l\'ajout de la question:', error);
        alert('Échec de l\'ajout de la question.');
      }
    },
  },
};
</script>
<template>
  <div
      class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100"
  >
    <div class="bg-overlay"></div>
    <div class="auth-page-content overflow-hidden pt-lg-5">
      <b-container>
        <b-row>
          <b-col lg="12">
            <b-card no-body class="overflow-hidden">
              <b-row class="g-0">
                <b-col lg="12">
                  <div class="p-lg-5 p-4">
                    <form v-on:submit.prevent="submitForm()" class="form-steps" autocomplete="off">
                      <div class="text-center pt-3 pb-4 mb-1">
                        <b-link href="#">
                          <img src="@/assets/images/tcflogo.png" alt="StudyPlus Logo" height="80" />
                        </b-link>
                      </div>

                      <div class="mb-4">
                        <h4 class="text-center">Ajouter une Question</h4>
                      </div>

                      <div class="form-group mb-3">
                        <label for="question" class="form-label">Question :</label>
                        <textarea v-model="formData.question" id="question" class="form-control" rows="4" required></textarea>
                      </div>

                      <div class="form-group mb-3">
                        <label class="form-label">Type :</label>
                        <div class="form-check" v-for="(label, value) in {
      'Compréhension orale': 'Compréhension orale',
      'Maîtrise des structures de la langue': 'Maîtrise des structures de la langue',
      'Compréhension de la langue écrite': 'Compréhension de la langue écrite'
    }" :key="value">
                          <input class="form-check-input" type="radio" :id="value" :value="value" v-model="formData.type" />
                          <label class="form-check-label" :for="value">{{ label }}</label>
                        </div>
                      </div>

                      <div class="form-group mb-3">
                        <label class="form-label">Difficulté :</label>
                        <div class="form-check form-check-inline" v-for="level in ['A1', 'A2', 'B1', 'B2', 'C1', 'C2']" :key="level">
                          <input class="form-check-input" type="radio" :id="level" :value="level" v-model="formData.difficulteeeee" />
                          <label class="form-check-label" :for="level">{{ level }}</label>
                        </div>
                      </div>

                      <div v-if="formData.type === 'Compréhension orale'" class="form-group mb-3">
                        <label for="audio" class="form-label">Audio (fichier) :</label>
                        <input type="file" class="form-control" @change="handleFileUpload" id="audio" />
                      </div>

                      <div class="form-group mb-3">
                        <label for="reponseCorrecte" class="form-label">Réponse correcte :</label>
                        <input v-model="formData.reponseCorrecte" type="text" id="reponseCorrecte" class="form-control" />
                      </div>

                      <div class="form-group mb-4">
                        <label for="reponses" class="form-label">Réponses (séparées par des virgules) :</label>
                        <input v-model="formData.reponses" type="text" id="reponses" class="form-control" />
                      </div>

                      <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Soumettre la Question</button>
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

<style scoped>
.auth-page-wrapper {
  background-image: url('@/assets/images/backgroundstudyplus.jpg'); /* Adjust the path as needed */
  background-size: cover;   /* Ensures the image covers the whole page */
  background-position: center; /* Centers the background image */
}
form {
  padding: 30px;
}
</style>