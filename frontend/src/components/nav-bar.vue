<script setup>
import { ref, onMounted, inject } from "vue";
import { useRouter } from 'vue-router';  // Import useRouter
import simplebar from "simplebar-vue";
import i18n from "../i18n";
import axios from 'axios'; // Import Axios

const lan = ref(i18n.locale);
const user = ref(null); // Initialize user as null
const router = useRouter();

const toggleHamburgerMenu = () => {
  // ... (Your existing hamburger menu logic)
};

const initFullScreen = () => {
  // ... (Your existing fullscreen logic)
};

const toggleDarkMode = () => {
  // ... (Your existing dark mode logic)
};

const logout = () => {
  localStorage.removeItem('token');
  router.push('/login');
};

onMounted(async () => {
  if (process.env.VUE_APP_I18N_LOCALE) {
    lan.value = process.env.VUE_APP_I18N_LOCALE;
  }

  document.addEventListener("scroll", () => {
    const pageTopbar = document.getElementById("page-topbar");
    if (pageTopbar) {
      document.body.scrollTop >= 50 || document.documentElement.scrollTop >= 50
          ? pageTopbar.classList.add("topbar-shadow")
          : pageTopbar.classList.remove("topbar-shadow");
    }
  });

  const topnavHamburgerIcon = document.getElementById("topnav-hamburger-icon");
  if (topnavHamburgerIcon) {
    topnavHamburgerIcon.addEventListener("click", toggleHamburgerMenu);
  }

  // Fetch user data from the backend
  try {
    const response = await axios.get('/api/user'); // Replace with your actual API endpoint
    user.value = response.data; // Assign the user data to the reactive variable
    console.log(user.value); // Log the user data
  } catch (error) {
    console.error("Error fetching user:", error);
    // Handle errors (e.g., redirect to login if unauthorized)
    router.push('/login');
  }
});

</script>

<template>
  <header id="page-topbar">
    <div class="layout-width">
      <div class="navbar-header">
        <div class="d-flex">
          <!-- LOGO -->
          <div class="navbar-brand-box horizontal-logo">
            <router-link to="/" class="logo logo-dark">
              <span class="logo-sm">
                <img src="@/assets/images/studyplus.png" alt="" height="22" />
              </span>
              <span class="logo-lg">
                <img src="@/assets/images/studyplus.png" alt="" height="17" />
              </span>
            </router-link>

            <router-link to="/" class="logo logo-light mt-4">
              <span class="logo-sm">
                <img src="@/assets/images/studyplus.png" alt="" height="50" />
              </span>
              <span class="logo-lg">
                <img src="@/assets/images/studyplus.png" alt="" height="130" />
              </span>
            </router-link>
          </div>

          <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                  id="topnav-hamburger-icon">
            <span class="hamburger-icon">
              <span></span>
              <span></span>
              <span></span>
            </span>
          </button>

          <!-- App Search-->
        </div>

        <div class="d-flex align-items-center">


          <!-- Fullscreen -->
          <div class="ms-1 header-item d-none d-sm-flex">
            <BButton type="button" variant="ghost-secondary" class="btn-icon btn-topbar rounded-circle"
                     data-toggle="fullscreen" @click="initFullScreen">
              <i class="bx bx-fullscreen fs-22"></i>
            </BButton>
          </div>
          <!-- Dark/Light -->
          <div class="ms-1 header-item d-none d-sm-flex">
            <BButton type="button" variant="ghost-secondary" class="btn-icon btn-topbar rounded-circle light-dark-mode"
                     @click="toggleDarkMode">
              <i class="bx bx-moon fs-22"></i>
            </BButton>
          </div>

          <BDropdown variant="ghost-dark" dropstart class="ms-1 dropdown"
                     :offset="{ alignmentAxis: 57, crossAxis: 0, mainAxis: -42 }"
                     toggle-class="btn-icon btn-topbar rounded-circle arrow-none" id="page-header-notifications-dropdown"
                     menu-class="dropdown-menu-lg dropdown-menu-end p-0" auto-close="outside">
            <template #button-content>
              <i class='bx bx-bell fs-22'></i>
              <span class="position-absolute topbar-badge fs-10 translate-middle badge rounded-pill bg-danger"><span
                  class="notification-badge">0</span><span class="visually-hidden">unread
                  messages
                </span>
              </span>
            </template>
            <div class="dropdown-head bg-primary bg-pattern rounded-top dropdown-menu-lg">
              <div class="p-3">
                <BRow class="align-items-center">
                  <BCol>
                    <h6 class="m-0 fs-16 fw-semibold text-white">
                      Notifications
                    </h6>
                  </BCol>
                  <BCol cols="auto" class="dropdown-tabs">
                    <!--<BBadge variant="light-subtle" class="bg-light-subtle text-body fs-13"> 4 New</BBadge>-->
                  </BCol>
                </BRow>
              </div>
            </div>
            <BTabs nav-class="dropdown-tabs nav-tab-custom bg-primary px-2 pt-2">
              <BTab title=" tous " class="tab-pane fade py-2 ps-2 show" id="all-noti-tab" role="tabpanel">
                <simplebar data-simplebar style="max-height: 300px" class="pe-2">
                  <div class="text-reset notification-item d-block dropdown-item position-relative">
                    <div class="d-flex">
                      <img src="@/assets/images/baggar.jpg" class="me-3 rounded-circle avatar-xs"
                           alt="user-pic" />
                      <div class="flex-grow-1">
                        <BLink href="#!" class="stretched-link">
                          <h6 class="mt-0 mb-1 fs-13 fw-semibold">
                            Baggar El Mehdi
                          </h6>
                        </BLink>
                        <div class="fs-13 text-muted">
                          <p class="mb-1">
                            bonjour !
                          </p>
                        </div>
                      </div>
                      <div class="px-2 fs-15">
                        <input class="form-check-input" type="checkbox" />
                      </div>
                    </div>
                  </div>

                  <div class="my-3 text-center">
                    <BButton type="button" variant="soft-success">
                      View All Notifications
                      <i class="ri-arrow-right-line align-middle"></i>
                    </BButton>
                  </div>
                </simplebar>
              </BTab>

              <BTab title="Messages" class="tab-pane fade py-2 ps-2" id="messages-tab" role="tabpanel"
                    aria-labelledby="messages-tab">
                <simplebar data-simplebar style="max-height: 300px" class="pe-2">
                  <div class="text-reset notification-item d-block dropdown-item position-relative">
                    <div class="d-flex">
                      <img src="@/assets/images/baggar.jpg" class="me-3 rounded-circle avatar-xs"
                           alt="user-pic" />
                      <div class="flex-grow-1">
                        <BLink href="#!" class="stretched-link">
                          <h6 class="mt-0 mb-1 fs-13 fw-semibold">
                            Baggar El Mehdi
                          </h6>
                        </BLink>
                        <div class="fs-13 text-muted">
                          <p class="mb-1">
                            bonjour !
                          </p>
                        </div>
                      </div>
                      <div class="px-2 fs-15">
                        <input class="form-check-input" type="checkbox" />
                      </div>
                    </div>
                  </div>

                  <div class="my-3 text-center">
                    <BButton type="button" variant="soft-success">
                      View All Messages
                      <i class="ri-arrow-right-line align-middle"></i>
                    </BButton>
                  </div>
                </simplebar>
              </BTab>

              <BTab title="Alerts" class="p-4">
                <simplebar data-simplebar style="max-height: 300px" class="pe-2">
                  <div class="w-25 w-sm-50 pt-3 mx-auto">
                    <img src="@/assets/images/svg/bell.svg" class="img-fluid" alt="user-pic" />
                  </div>
                  <div class="text-center pb-5 mt-2">
                    <h6 class="fs-18 fw-semibold lh-base">
                      Hey! You have no any notifications
                    </h6>
                  </div>
                </simplebar>
              </BTab>
            </BTabs>
          </BDropdown>

          <!-- Profile-->

          <BDropdown variant="link" class="ms-sm-3 header-item topbar-user" toggle-class="rounded-circle arrow-none"
                     menu-class="dropdown-menu-end" :offset="{ alignmentAxis: -14, crossAxis: 0, mainAxis: 0 }">
            <template #button-content>
              <span class="d-flex align-items-center">
                <img class="rounded-circle header-profile-user" src="@/assets/images/noImage.png"
                     alt="Header Avatar">
              </span>
            </template>
            <h6 class="dropdown-header">Salam {{ user?.nom }} {{ user?.prenom }} !</h6>
            <router-link class="dropdown-item" to="/profile"><i
                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i>
              <span class="align-middle"> Profile</span>
              <hr>
            </router-link>
            <a class="dropdown-item" @click.prevent="logout"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i>
              <span class="align-middle" data-key="t-logout">se deconnecter</span>
            </a>
          </BDropdown>
        </div>
      </div>
    </div>
  </header>
</template>