<template>
  <div class="test-container d-flex justify-content-center">
    <div class="test-card p-4 shadow-lg">
      <div class="header-container custom-header">
        <PageHeader title="EditEtudiant" pageTitle="Dashboard" pageRoute="/" />
      </div>

      <div class="d-flex justify-content-center align-items-center mb-4">
        <!-- TCF Logo -->
        <div class="logo-tcf mx-3">
          <img src="@/assets/images/tcflogo.png" alt="Logo TCF" height="40" class="logo-image" />
        </div>

        <!-- Etawjihi Image
        <div class="logo-etawjihi mx-3">
          <img src="@/assets/images/studyplus.png" alt="Etawjihi" height="40" class="logo-image studyplus-image mt-2" />
        </div>
         -->
      </div>
      <form @submit.prevent="handleSubmit">  <!-- Use a form element -->
        <b-row>
          <b-col md="6">
            <div class="mb-3">
              <label for="nom" class="form-label"
              >Nom / اللقب <span class="text-danger">*</span></label
              >
              <input
                  v-model="datas.nom"
                  type="text"
                  class="form-control"
                  id="nom"
                  placeholder="Votre nom"
                  required
              />
            </div>
          </b-col>
          <b-col md="6">
            <div class="mb-3">
              <label for="nom" class="form-label"
              >Prenom / الاسم <span class="text-danger">*</span></label
              >
              <input
                  v-model="datas.prenom"
                  type="text"
                  class="form-control"
                  id="nom"
                  placeholder="Votre Prenom"
                  required
              />
            </div>
          </b-col>
          <b-col md="6">
            <div class="mb-3">
              <label for="nom" class="form-label"
              > Email <span class="text-danger">*</span></label
              >
              <input
                  v-model="datas.email"
                  type="email"
                  class="form-control"
                  id="email"
                  placeholder="Votre Email"
                  required
              />
            </div>
          </b-col>
          <b-col md="6">
            <div class="mb-3">
              <label for="tel" class="form-label"
              >Téléphone / رقم الهاتف <span class="text-danger">*</span></label
              >
              <input
                  v-model="datas.tel"
                  type="tel"
                  class="form-control"
                  id="tel"
                  placeholder="Téléphone ici..."
                  oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10)"
              />
            </div>
          </b-col>
          <b-col md="6">
            <div class="mb-3">
              <label for="niveau_bac" class="form-label">
                Choisis ton niveau d'études / اختر مستواك الدراسي
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
            </div>
          </b-col>
          <b-col md="6">
            <div class="mb-3">
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
            </div>
          </b-col>
        </b-row>

        <div class="d-flex justify-content-center mt-4">
          <button type="submit" class="btn btn-primary">Submit</button>  <!-- Submit button inside the form -->
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
import axios from 'axios';




export default {
  props: {
    id: {
      type: Number,
      required: true,
    },
  },
  components: {
    Multiselect,PageHeader
  },
  data(){
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

        '1ère année Baccalauréat',
        '2ème année Baccalauréat',
        'BAC+1',
        'BAC+2',
        'BAC+3',
        'BAC+4',
        'BAC+5',
        'BAC+6',
        'Doctorant',
        'Autres',

      ],
      datasDomaine: [
        "العلوم الاقتصادية وإدارة الأعمال / Sciences économiques et gestion d'entreprises",
        "الهندسة / Ingénierie",
        "علم الأحياء / Biologie",
        "الفنون والتصميم / Art et Désign",
        "آخر / Autre",

      ],
      datasEchelle: [
        '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',

      ],
      datasBudget: [
        "Moins de 10 000 Dhs/an",
        "Entre 10 000 Dhs et 20 000 Dhs/an",
        "Plus de 20 000 Dhs/an",

      ],
      datasFamilleChine: [
        "Oui / نعم",
        "Non / لا",

      ],
      datasAvis: [
        "Ils sont pour / هم موافقون",
        "Ils sont contre / هم غير موافقين",
        "Ils ne sont pas au courant / ليسوا على علم",
        "Ils y réfléchissent / يفكرون في الأمر",

      ],
      datas: {
        tel: '',
        email:'',
        nom: '',
        prenom: '',
        niveau_bac: '',
        ville: '',
      },

    };



  },
  mounted() {
    this.fetchEtudiantDetails();
  },
  methods: {
    async fetchEtudiantDetails() {
      try {
        const response = await axios.get(`/etudiant/${this.id}`);
        const etudiantData = response.data;

        this.datas = {
          tel: etudiantData.tel,
          email: etudiantData.email,
          nom: etudiantData.nom,
          prenom: etudiantData.prenom,
          niveau_bac: etudiantData.niveau_bac,
          ville: etudiantData.ville, // Reset audio
        };

      } catch (error) {
        console.error('Error fetching etudiant details:', error);
        alert('Failed to load etudiant details.');
      }
    },

    async handleSubmit() {
      try {
        const DataToSend = new FormData();
        DataToSend.append('tel', this.datas.tel);
        DataToSend.append('email', this.datas.email);
        DataToSend.append('nom', this.datas.nom);
        DataToSend.append('prenom', this.datas.prenom);
        DataToSend.append('niveau_bac', this.datas.niveau_bac);
        DataToSend.append('ville', this.datas.ville);
        console.log('formDataToSend', DataToSend);
        const response = await axios.post(`/edit/etudiant/${this.id}`, DataToSend, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        if (response.status === 200) {
          alert('etudiant modifiée avec succès');
          this.$router.push({name: 'allEtudiants'}); // Navigate back to the index page
        }
      } catch (error) {
        console.error('There was an error sending the data:', error);
        alert('There was an error submitting the form.');
      }
    }
  }



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

/* Header Styles */
.custom-header ::v-deep(.page-title-box) {
  background-color: #1D5F94;
  padding: 15px;
  border-radius: 1px;
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
@media (max-width: 768px) {

  .custom-header ::v-deep(.page-title-box) {
    padding-top: 5px !important;
    padding-bottom: 5px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
    font-size: 0.9rem;
  }
  .logo-image {
    height: 35px;
  }

  .custom-header ::v-deep(.page-title-box h4) {
    font-size: 1rem !important;
  }

  .custom-header ::v-deep(.page-title-box .breadcrumb-item) {
    font-size: 0.7rem;
  }

  .custom-header ::v-deep(.page-title-box .breadcrumb-item a) {
    font-size: 0.7rem !important;
  }
}

@media (max-width: 576px) {
  .custom-header ::v-deep(.page-title-box) {
    padding-top: 3px !important;
    padding-bottom: 3px !important;
    padding-left: 3px !important;
    padding-right: 3px !important;
    font-size: 0.7rem;
  }
  .logo-image {
    height: 30px;
  }

  .h2 {
    font-size: 1.3rem;
  }

  .h3 {
    font-size: 0.9rem;
  }

  .question-box .h2 {
    font-size: 1.1rem;
  }


  .custom-header ::v-deep(.page-title-box h4) {
    font-size: 0.9rem !important;
  }

  .custom-header ::v-deep(.page-title-box .breadcrumb-item) {
    font-size: 0.6rem;
  }

  .custom-header ::v-deep(.page-title-box .breadcrumb-item a) {
    font-size: 0.6rem !important;
  }
}

.studyplus-image {
  height: 120px;
  width: auto;
  margin-left: 10px;
}
</style>
<style src="@vueform/multiselect/themes/default.css"></style>
