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
                    <form
                        v-on:submit.prevent="submitForm()"
                        class="form-steps"
                        autocomplete="off"
                    >
                      <div class="text-center pt-3 pb-4 mb-1">
                        <b-link href="#"
                        ><img
                            src="@/assets/images/studyplus.png"
                            alt=""
                            height="150"
                        /></b-link>
                      </div>
                      <div class="step-arrow-nav mb-4">
                        <ul class="nav nav-pills custom-nav nav-justified" role="tablist">
                          <li class="nav-item" role="presentation">
                            <b-button
                                variant="link"
                                class="nav-link active"
                                id="form-tab"
                                type="button"

                            >Add Question</b-button
                            >
                          </li>
                        </ul>
                      </div>
                      <div>
                        <label for="question">Question :</label>
                        <textarea v-model="formData.question" id="question" required></textarea>
                      </div>

                      <div>
                        <label for="type">Type :</label>
                        <div>
                          <input type="radio" id="comprehension_orale" value="Compréhension orale" v-model="formData.type" /> Compréhension orale
                          <input type="radio" id="maitrise_structures" value="Maîtrise des structures de la langue" v-model="formData.type" /> Maîtrise des structures de la langue
                          <input type="radio" id="comprehension_langue_ecrite" value="Compréhension de la langue écrite" v-model="formData.type" /> Compréhension de la langue écrite
                        </div>
                      </div>

                      <div>
                        <label for="difficulteeeee">Difficulté :</label>
                        <div>
                          <input type="radio" id="A1" value="A1" v-model="formData.difficulteeeee" /> A1
                          <input type="radio" id="A2" value="A2" v-model="formData.difficulteeeee" /> A2
                          <input type="radio" id="B1" value="B1" v-model="formData.difficulteeeee" /> B1
                          <input type="radio" id="B2" value="B2" v-model="formData.difficulteeeee" /> B2
                          <input type="radio" id="C1" value="C1" v-model="formData.difficulteeeee" /> C1
                          <input type="radio" id="C2" value="C2" v-model="formData.difficulteeeee" /> C2
                        </div>
                      </div>

                      <div v-if="formData.type === 'Compréhension orale'">
                        <label for="audio">Audio (fichier) :</label>
                        <input type="file" @change="handleFileUpload" id="audio" />
                      </div>

                      <div>
                        <label for="reponseCorrecte">Réponse correcte :</label>
                        <input v-model="formData.reponseCorrecte" type="text" id="reponseCorrecte" />
                      </div>

                      <div>
                        <label for="reponses">Réponses (séparées par des virgules) :</label>
                        <input v-model="formData.reponses" type="text" id="reponses" />
                      </div>

                      <button type="submit">Soumettre</button>

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
</style>