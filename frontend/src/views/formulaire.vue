<template>
  <div class="test-container d-flex justify-content-center">
    <div class="test-card p-4 shadow-lg">
      <!--
      <div class="header-container custom-header">
        <PageHeader title="TCF" pageTitle="HOME" pageRoute="/" />
      </div>
      -->

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
              src="@/assets/images/studyplus.png"
              alt="Etawjihi"
              class="logo-image studyplus-image"
          />
        </div>
      </div>
      <div v-if="emailError" class="text-danger">{{ emailError }}</div>
      <br />
      <form @submit.prevent="handleSubmit">
        <!-- Use a form element -->
        <b-row>
          <b-col md="6" class="mb-3">
            <label for="nom" class="form-label"
            >Nom <span class="text-danger">*</span></label
            >
            <input
                v-model="datas.nom"
                type="text"
                class="form-control"
                id="nom"
                placeholder="Votre nom"
                required
            />
          </b-col>
          <b-col md="6" class="mb-3">
            <label for="prenom" class="form-label"
            >Prenom <span class="text-danger">*</span></label
            >
            <input
                v-model="datas.prenom"
                type="text"
                class="form-control"
                id="prenom"
                placeholder="Votre Prenom"
                required
            />
          </b-col>
          <b-col md="6" class="mb-3">
            <label for="email" class="form-label"
            >Email <span class="text-danger">*</span></label
            >
            <input
                v-model="datas.email"
                type="email"
                class="form-control"
                id="email"
                placeholder="Votre Email"
                required
            />
          </b-col>
          <b-col md="6" class="mb-3">
            <label for="tel" class="form-label"
            >Téléphone<span class="text-danger">*</span></label
            >
            <input
                v-model="datas.tel"
                type="tel"
                class="form-control"
                id="tel"
                placeholder="Téléphone ici..."
                oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10)"
            />
          </b-col>
          <b-col md="6" class="mb-3">
            <label for="niveau_bac" class="form-label">
              Choisis ton niveau d'études
              <span class="text-danger">*</span>
            </label>
            <Multiselect
                v-model="datas.niveau_bac"
                :options="datasNiveauBac || []"
                :close-on-select="true"
                :searchable="true"
                placeholder="Sélectionnez votre niveau..."
                class="custom-multiselect"
            />
          </b-col>
          <b-col md="6" class="mb-3">
            <label for="ville" class="form-label"
            >Ville <span class="text-danger">*</span></label
            >
            <input
                v-model="datas.ville"
                type="text"
                class="form-control"
                id="ville"
                placeholder="Entrez votre ville..."
                required
            />
          </b-col>
        </b-row>

        <div class="d-flex justify-content-center mt-4">
          <button type="submit" class="btn btn-primary">soumettre</button>
          <!-- Submit button inside the form -->
        </div>
        <div class="d-flex justify-content-center mt-4">
          <router-link to="/checkExistingTest"> Déjà soumis le test ? </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Multiselect from "@vueform/multiselect";
import PageHeader from "@/components/page-header";
import "prismjs";
import "prismjs/themes/prism.css";
import axios from "axios";

export default {
  components: {
    Multiselect,
    PageHeader,
  },
  data() {
    return {
      datasFiliere: [
        "Sciences Math A",
        "Sciences Math B",
        "Sciences Physique",
        "SVT",
        "Sciences et technologies électriques",
        "Sciences et technologies mécaniques",
        "Sciences économique",
        "Sciences gestion comptable",
        "Sciences agronomiques",
        "Lettres",
        "Sciences humaines",
        "Sciences de la chariaa",
        "Arts Appliqués",
      ],
      datasNiveauBac: [
        "1ère année Baccalauréat",
        "2ème année Baccalauréat",
        "BAC+1",
        "BAC+2",
        "BAC+3",
        "BAC+4",
        "BAC+5",
        "BAC+6",
        "Doctorant",
        "Autres",
      ],
      datasDomaine: [
        "العلوم الاقتصادية وإدارة الأعمال / Sciences économiques et gestion d'entreprises",
        "الهندسة / Ingénierie",
        "علم الأحياء / Biologie",
        "الفنون والتصميم / Art et Désign",
        "آخر / Autre",
      ],
      datasEchelle: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"],
      datasBudget: [
        "Moins de 10 000 Dhs/an",
        "Entre 10 000 Dhs et 20 000 Dhs/an",
        "Plus de 20 000 Dhs/an",
      ],
      datasFamilleChine: ["Oui / نعم", "Non / لا"],
      datasAvis: [
        "Ils sont pour / هم موافقون",
        "Ils sont contre / هم غير موافقين",
        "Ils ne sont pas au courant / ليسوا على علم",
        "Ils y réfléchissent / يفكرون في الأمر",
      ],
      datas: {
        tel: "",
        email: "",
        nom: "",
        prenom: "",
        niveau_bac: "",
        ville: "",
      },
      emailError: null,
    };
  },
  methods: {
    async handleSubmit() {
      this.emailError = null;
      try {
        const response = await axios.post("/etudiant/create", this.datas);

        console.log("Data sent successfully!", response.data);

        this.datas = {
          tel: "",
          email: "",
          nom: "",
          prenom: "",
          niveau_bac: "",
          ville: "",
        };
        this.$router.push({
          name: "TCF",
          params: { etudiantId: response.data.etudiantId },
        });
      } catch (error) {
        console.error("There was an error sending the data:", error);
        if (error.response && error.response.status === 409) {
          // Email already exists error
          this.emailError = "Cet email est déjà utilisé."; // Set the error message
        } else {
          // Other errors
          this.emailError = "Une erreur est survenue. Veuillez réessayer.";
        }
      }
    },
  },
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
  min-height: 100vh;
  width: 100vw;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px; /* Add padding for small screens */
}

.test-card {
  background-color: white;
  padding: 20px; /* Reduce padding on smaller screens */
  border-radius: 15px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
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
  height: 40px;
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
  height: 120px;
  width: auto;
  margin-left: 10px;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .test-card {
    padding: 15px; /* Further reduce padding */
  }

  .logo-image {
    height: 30px; /* Adjust logo size */
  }

  .studyplus-image {
    height: 100px; /* Adjust studyplus logo size */
  }
}

@media (max-width: 576px) {
  .test-card {
    padding: 10px; /* Even smaller padding for mobile */
  }

  .logo-image {
    height: 25px; /* Further adjust logo size */
  }

  .studyplus-image {
    height: 100px; /* Further adjust studyplus logo size */
  }
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>