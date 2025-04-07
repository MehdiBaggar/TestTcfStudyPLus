<template>
  <div class="test-container d-flex justify-content-center">
    <div class="test-card p-4 shadow-lg">
      <div class="header-container custom-header">
        <PageHeader title="TCF" pageTitle="HOME" pageRoute="/" />
      </div>

      <div class="d-flex justify-content-center align-items-center mb-4 logo-container">
        <!-- TCF Logo -->
        <div class="logo-tcf mx-3 logo-item">
          <img
              src="@/assets/images/tcflogo.png"
              alt="Logo TCF"
              class="logo-image"
          />
        </div>

        <!-- Etawjihi Image -->
        <div class="logo-etawjihi mx-3 logo-item">
          <img
              src="@/assets/images/Logo-studyplus.png"
              alt="Etawjihi"
              class="logo-image studyplus-image"
          />
        </div>
      </div>

      <!-- Start Test Button -->
      <div v-if="!isTestStarted" class="instruction-card mt-4">
        <h2 class="h2 text-center primary-text">Bienvenue au Test</h2>
        <p class="test-description text-center">
          Vous êtes prêt à relever le défi ? Passez un test dans des conditions
          identiques à celles du TCF et évaluez vos performances. Vous pouvez
          refaire le test autant de fois que vous le souhaitez, c'est
          entièrement gratuit.
          <br /><br />
          Pour vous aider à vous préparer de manière optimale au TCF, nous vous
          proposons un test de 20 minutes, conçu pour reproduire les conditions
          exactes d'une session officielle. Attention, bien que les résultats
          de ce test ne garantissent pas le même score lors du TCF officiel, il
          constitue une excellente préparation pour maximiser vos chances de
          réussite.
        </p>
        <button
            @click="startTest"
            class="btn btn-primary large-button d-block mx-auto"
        >
          Commencer le test
        </button>
      </div>

      <!-- Container for Timer and Question Number -->
      <div
          v-if="isTestStarted"
          class="d-flex justify-content-between align-items-center mb-4"
      >
        <button type="button" class="btn btn-info text-white">
          Question {{ currentQuestionIndex + 1 }} / {{ questions.length }}
        </button>
        <button type="button" class="btn btn-info text-white">
          {{ formattedTime }}
        </button>
      </div>

      <!-- Show the current question -->
      <div
          v-if="isTestStarted && !isSubmitted && !reviewMode"
          class="question-box"
      >
        <div v-if="questions.length > 0 && questions[currentQuestionIndex]">
          <p class="h2 question-text">{{ currentQuestion.question }}</p>
          <hr class="question-divider" />

          <div v-if="questions[currentQuestionIndex].audio" class="audio-container">
            <h3 class="audio-title">Écoutez l'exemple audio</h3>
            <audio
                ref="audioPlayer"
                :src="questions[currentQuestionIndex].audio"
                controls
                autoplay
                class="audio-player"
                style="display: none"
                @canplay="checkAudio()"
            ></audio>

            <div class="audio-controls">
              <!-- Restart Audio -->
              <i @click="restartAudio" class="ri-restart-line"></i>

              <!-- Play/Pause Control -->
              <i v-if="!isPlaying" @click="togglePlayPause" class="ri-play-line"></i>
              <i v-if="isPlaying" @click="togglePlayPause" class="ri-pause-line"></i>
            </div>
          </div>

          <div
              v-for="(response, i) in questions[currentQuestionIndex].shuffledReponses"
              :key="i"
              class="h3 answer-option"
              :class="{
              selected: answers[questions[currentQuestionIndex].id] === response,
            }"
              @click="answers[questions[currentQuestionIndex].id] = response"
          >
            <label>
              <input
                  type="radio"
                  :name="'response-' + questions[currentQuestionIndex].id"
                  :value="response"
                  v-model="answers[questions[currentQuestionIndex].id]"
                  hidden
              />
              <span class="h3">{{ response }}</span>
            </label>
          </div>

          <div
              class="d-flex justify-content-between align-items-center mb-4"
          >
            <button
                @click="previousQuestion"
                :disabled="currentQuestionIndex === 0"
                class="btn btn-secondary large-button"
            >
              précédent
            </button>
            <button
                @click="nextQuestion"
                :disabled="!answers[questions[currentQuestionIndex].id]"
                class="btn btn-primary large-button"
            >
              {{
                currentQuestionIndex === questions.length - 1 ? 'Submit' : 'Suivant'
              }}
            </button>
          </div>
        </div>
      </div>

      <!-- Show results after submitting -->
      <div v-if="isSubmitted && !reviewMode" class="result-box text-center mt-4">
        <p class="primary-text">Votre niveau et score :</p>
        <p class="secondary-text">
          Score: {{ score }} - Niveau: {{ niveau }}
        </p>
        <button @click="downloadCertificate" class="btn btn-primary mt-3 mr-2 download-button">
          Télécharger le certificat
        </button>

        <!-- Button to show answers -->
        <button @click="showAnswers = !showAnswers" class="btn btn-secondary mt-3">
          Voir les réponses
        </button>

        <!-- Show answers as buttons (1 to 40) -->
        <div v-if="showAnswers" class="mt-4 answer-buttons-container">
          <div v-for="(question, index) in questions" :key="question.id" class="mb-2">
            <button
                @click="reviewQuestion(index)"
                :class="{
                'correct-answer-button': isCorrectAnswer(question.id),
                'wrong-answer-button': !isCorrectAnswer(question.id) &&
                  answers[question.id],
              }"
                class="btn"
                style="width: 50px; margin-right: 5px"
            >
              {{ index + 1 }}
            </button>
          </div>
        </div>
      </div>

      <!-- Correction View (Review Mode) -->
      <div v-if="reviewMode && isSubmitted" class="question-box">
        <div v-if="questions.length > 0 && questions[currentQuestionIndex]">
          <p class="h2 question-text">{{ currentQuestion.question }}</p>
          <hr class="question-divider" />

          <div v-if="questions[currentQuestionIndex].audio" class="audio-container">
            <h3 class="audio-title">Écoutez l'exemple audio</h3>
            <audio
                ref="audioPlayer"
                :src="questions[currentQuestionIndex].audio"
                controls
                autoplay
                class="audio-player"
                style="display: none"
                @canplay="checkAudio()"
            ></audio>

            <div class="audio-controls">
              <!-- Restart Audio -->
              <i @click="restartAudio" class="ri-restart-line"></i>

              <!-- Play/Pause Control -->
              <i v-if="!isPlaying" @click="togglePlayPause" class="ri-play-line"></i>
              <i v-if="isPlaying" @click="togglePlayPause" class="ri-pause-line"></i>
            </div>
          </div>

          <div
              v-for="(response, i) in questions[currentQuestionIndex].shuffledReponses"
              :key="i"
              class="h3 answer-option"
              :class="{
              'correct-answer':
                questions[currentQuestionIndex].reponse_correcte === response,
              'wrong-answer':
                answers[questions[currentQuestionIndex].id] === response &&
                questions[currentQuestionIndex].reponse_correcte !== response,
              selected:
                answers[questions[currentQuestionIndex].id] === response &&
                questions[currentQuestionIndex].reponse_correcte === response,
            }"
          >
            <label>
              <input
                  type="radio"
                  :name="'response-' + questions[currentQuestionIndex].id"
                  :value="response"
                  :checked="answers[questions[currentQuestionIndex].id] === response"
                  disabled
                  hidden
              />
              <span class="h3">{{ response }}</span>
            </label>
          </div>

          <div
              class="d-flex justify-content-between align-items-center mb-4"
          >
            <button
                @click="previousQuestionReview"
                :disabled="currentQuestionIndex === 0"
                class="btn btn-primary large-button d-block mx-auto"
            >
              précédent
            </button>
            <button
                @click="nextQuestionReview"
                :disabled="currentQuestionIndex === questions.length - 1"
                class="btn btn-primary large-button d-block mx-auto"
            >
              suivant
            </button>

            <button
                @click="exitReviewMode"
                class="btn btn-secondary large-button d-block mx-auto"
            >
              Exit Review
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, computed, watch, nextTick, onMounted } from 'vue';
import PageHeader from '@/components/page-header';
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  components: { PageHeader },
  props: {
    etudiantId: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    // Receive props here

    // Reactive state variables
    const questions = ref([]);
    const answers = ref({});
    const result = ref(null);
    const currentQuestion = computed(
        () => questions.value[currentQuestionIndex.value] || {},
    );
    const currentQuestionIndex = ref(0);
    const timer = ref(20 * 60); // 20 minutes in seconds
    let timerInterval;
    const isTestStarted = ref(false); // Track if the test has started
    const isSubmitted = ref(false);
    const responsesDetails = ref([]);// Track if the test is submitted
    const showAnswers = ref(false); // Control the visibility of answer buttons
    const reviewMode = ref(false); // Correction reviewMode
    const score = ref(null);
    const niveau = ref(null);
    // Access the audio player ref
    const audioPlayer = ref(null);
    const isPlaying = ref(true);
    const etudiantId = ref(props.etudiantId); // Access the prop here

    watch(currentQuestionIndex, (newIndex) => {
      console.log('Current Question Index Updated:', newIndex);
    });

    // Fetch questions from the API
    const fetchQuestions = async () => {
      try {
        const response = await axios.get('/test/questions');
        questions.value = response.data; // Assuming the response is in JSON format

        // Shuffle the answers for each question once
        questions.value.forEach((question) => {
          question.shuffledReponses = shuffleAnswers(question.reponses);
        });
      } catch (error) {
        console.error('Error fetching questions:', error);
      }
    };
    const successmsg = () => {
      Swal.fire('bravo!', 'Test soumis avec succès!', 'success');
    };

    const checkAudio = () => {
      if (audioPlayer.value && audioPlayer.value.readyState >= 3) {
        // If audio is ready, show the play/stop buttons accordingly
        if (audioPlayer.value.paused) {
          isPlaying.value = false;
        } else {
          isPlaying.value = true;
        }
      } else {
        // If audio is not ready (not loaded or broken), show play
        isPlaying.value = false;
      }
    };

    const restartAudio = () => {
      if (audioPlayer.value) {
        audioPlayer.value.currentTime = 0; // Rewind to the beginning
        audioPlayer.value.play(); // Start the audio again
        isPlaying.value = true; // Set play status
      }
    };

    const stopAudio = () => {
      if (audioPlayer.value) {
        audioPlayer.value.pause(); // Pause the audio
        audioPlayer.value.currentTime = 0; // Reset the time to the beginning
        isPlaying.value = false; // Set pause status
      }
    };
    const togglePlayPause = () => {
      if (audioPlayer.value) {
        try {
          if (isPlaying.value) {
            audioPlayer.value.pause();
          } else {
            audioPlayer.value.play();
          }
          isPlaying.value = !isPlaying.value;
        } catch (error) {
          console.error('Error toggling playback:', error);
        }
      }
    };

    // Shuffle the answers for random order
    const shuffleAnswers = (responses) => {
      return responses.sort(() => Math.random() - 0.5);
    };

    // Start the test and timer
    const startTest = () => {
      isTestStarted.value = true;
      fetchQuestions();
      startTimer();
    };

    // Move to the next question or submit the test
    const nextQuestion = async () => {
      if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++;
        await nextTick();
        console.log('New question:', questions.value[currentQuestionIndex.value]);
      } else {
        await submitTest(); // If last question, submit the test
      }
    };
    const previousQuestion = () => {
      if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
      }
    };

    // Correction next question review
    const nextQuestionReview = () => {
      if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++;
      }
    };

    const previousQuestionReview = () => {
      if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--;
      }
    };
    const isCorrectAnswer = (questionId) => {
      const question = questions.value.find((q) => q.id === questionId);

      if (!question) {
        console.warn(`Question not found with ID: ${questionId}`);
        return false; // Or handle the error appropriately
      }

      return answers.value[questionId] === question.reponse_correcte;
    };

    // Submit answers
    const submitTest = async () => {
      clearInterval(timerInterval);
      const answersArray = Object.keys(answers.value).map((questionId) => ({
        questionId: questionId,
        response: answers.value[questionId],
      }));

      console.log('Submitting answers:', answersArray);
      successmsg();

      try {
        const response = await axios.post(`/test/submit/${props.etudiantId}`, { // Use prop here
          answers: answersArray,
        });

        result.value = response.data; // Save the result from the response
        isSubmitted.value = true; // Mark the test as submitted
        console.log('Test submission response:', response.data);

        if (response.data.responsesDetails) {
          responsesDetails.value = response.data.responsesDetails;
        }
        console.log('Responses details:', responsesDetails.value);

        score.value = response.data.score;
        niveau.value = response.data.niveau;
      } catch (error) {
        console.error('Error submitting test:', error);
      }
    };

    const reviewQuestion = (index) => {
      currentQuestionIndex.value = index;
      reviewMode.value = true;
    };

    const exitReviewMode = () => {
      reviewMode.value = false;
      showAnswers.value = false;
      currentQuestionIndex.value = 0;
    };
    const downloadCertificate = () => {
      window.open(`/pdf/test/${props.etudiantId}`, '_blank');
    };
    // Start the timer
    const startTimer = () => {
      timerInterval = setInterval(() => {
        if (timer.value > 0) {
          timer.value--;
        } else {
          clearInterval(timerInterval);
          submitTest(); // Auto submit when time is over
        }
      }, 1000);
    };

    // Format the timer as minutes:seconds
    const formattedTime = computed(() => {
      const minutes = Math.floor(timer.value / 60);
      const seconds = timer.value % 60;
      return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    });

    // Return reactive properties and methods to the template
    return {
      questions,
      answers,
      result,
      showAnswers,
      currentQuestionIndex,
      responsesDetails,
      reviewMode,
      reviewQuestion,
      timer,
      audioPlayer,
      fetchQuestions,
      startTest,
      nextQuestion,
      previousQuestion,
      submitTest,
      formattedTime,
      isTestStarted,
      isSubmitted,
      checkAudio,
      stopAudio,
      restartAudio,
      togglePlayPause,
      successmsg,
      currentQuestion,
      isPlaying,// Add isSubmitted to the return
      nextQuestionReview,
      previousQuestionReview,
      exitReviewMode,
      isCorrectAnswer,
      etudiantId,
      score,
      niveau,
      downloadCertificate,
    };
  },
  mounted() {
    console.log('Etudiant ID:', this.etudiantId);
  },
};
</script>
<style scoped>
/* General Styles */
.test-container {
  background-image: url('../assets/images/backgroundstudyplus.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  min-height: 100vh;
  width: 100vw;
  padding-top: 100px;
  padding-bottom: 100px;
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
  margin-top: 0;
}

/* Header Styles */
.custom-header ::v-deep(.page-title-box) {
  background-color: #1D5F94;
  margin-top: -30px;
  padding: 15px;
  border-radius: 0px;
  color: white;
}

.custom-header ::v-deep(.page-title-box h4) {
  color: white !important;
}

.custom-header ::v-deep(.page-title-box .breadcrumb-item) {
  color: white;
}

.custom-header ::v-deep(.page-title-box .breadcrumb-item a) {
  color: white !important;
}

.custom-header ::v-deep(.page-title-box .breadcrumb-item.active) {
  color: white;
}

/* Logo Styles */
.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap; /* Allows logos to wrap on smaller screens */
}

.logo-item {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  min-width: 100px; /* Minimum width to prevent extreme shrinking */
}

.logo-image {
  height: 50px;
  max-width: 100%;
  object-fit: contain;
  margin: 0 auto;
  display: block;
}

/* Instruction Card Styles */
.instruction-card {
  background-color: #f8f9fa;
  padding: 20px;
  border-radius: 10px;
}

.primary-text {
  color: #1D5F94;
}

.secondary-text {
  color: #495057;
}

.large-button {
  font-size: 0.9rem;
  padding: 0.6rem 1.2rem;
  border-radius: 0.5rem;
  min-width: 120px;
  transition: all 0.2s ease-in-out;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

.btn-primary {
  background-color: #1D5F94 !important;
  border-color: #1D5F94 !important;
  color: white !important;
}

.btn-primary:hover {
  background-color: #1D5F94 !important;
  border-color: #F8B63C !important;
}

.btn-secondary {
  background-color: #1D5F94 !important;
  border-color: #1D5F94 !important;
  color: white !important;
}

.btn-secondary:hover {
  background-color: #1D5F94 !important;
  border-color: #d0942c !important;
}

.btn-info {
  background-color: transparent !important;
  border-color: #F8B63C !important;
  color: #14436a !important;
}

.btn-info:hover {
  background-color: #F8B63C !important;
  border-color: #F8B63C !important;
}

.download-button {
  display: inline-block;
  margin-right: 0.5rem !important;
}

/* Question Box Styles */
.question-box {
  margin-top: 20px;
}

.question-divider {
  border-top: 2px solid #1D5F94;
}

.answer-option {
  padding: 15px;
  margin-bottom: 10px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.selected {
  background-color: #F8B63C;
  color: #212529;
}

/* Audio Styles */
.audio-container {
  margin-top: 30px;
  text-align: center;
}

.audio-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #F8B63C;
  margin-bottom: 15px;
}

.audio-player {
  width: 80%;
  max-width: 600px;
  border-radius: 10px;
  background-color: #f1f1f1;
  padding: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.audio-controls i {
  font-size: 36px;
  cursor: pointer;
  margin: 0 10px;
  transition: transform 0.3s ease;
  color: #F8B63C;
}

.audio-controls i:hover {
  transform: scale(1.2);
}

/* Result Box Styles */
.result-box {
  margin-top: 20px;
  font-size: 1.3rem;
}

/* Answer Buttons Styles */
.answer-buttons-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
}

.answer-buttons-container button {
  margin: 5px;
  background-color: white;
  color: #212529;
  border-color: #ced4da;
}

.btn-success {
  border-color: lightgreen;
  background-color: white;
  color: black;
}

.btn-danger {
  border-color: red;
  background-color: white;
  color: black;
}

/* Correct and Wrong Answer Styles */
.correct-answer {
  border: 3px solid lightgreen !important;
  background-color: transparent !important;
  color: black;
}

.wrong-answer {
  border: 3px solid red !important;
  background-color: transparent !important;
  color: black;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .test-card {
    padding: 20px;
  }

  .logo-image {
    height: 35px;
  }

  .large-button {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
    min-width: 100px;
  }

  .test-description {
    font-size: 0.9rem;
  }

  .h2 {
    font-size: 1.2rem;
  }

  .h3 {
    font-size: 0.8rem;
  }

  .audio-title {
    font-size: 1.2rem;
  }

  .audio-player {
    width: 90%;
  }
}

@media (max-width: 576px) {
  .test-card {
    padding: 10px;
  }

  .large-button {
    font-size: 0.7rem;
    padding: 0.4rem 0.8rem;
    min-width: 80px;
  }

  .logo-image {
    height: 30px;
  }

  .test-description {
    font-size: 0.8rem;
  }

  .h2 {
    font-size: 1rem;
  }

  .h3 {
    font-size: 0.7rem;
  }

  .question-text {
    font-size: 1rem;
  }

  .audio-title {
    font-size: 1rem;
  }

  .audio-controls i {
    font-size: 24px;
    margin: 0 5px;
  }
}

.correct-answer-button {
  background-color: lightgreen !important;
  color: black !important;
  border-color: lightgreen !important;
}

.wrong-answer-button {
  background-color: red !important;
  color: black !important;
  border-color: red !important;
}

.studyplus-image {
  height: auto; /* Adjust the height */
  width: 80px;/* Keep the aspect ratio */
  margin-left: 100px;
}
</style>