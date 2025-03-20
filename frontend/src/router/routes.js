

export default [
  

    {
        path: "/Tcf/:etudiantId",
        name: "TCF",
        meta: {
            title: "Tcf Test",
        },
        props: true,
        component: () => import("../views/test.vue"),
    },
    {
        path: "/",
        name: "home",
        meta: {
            title: "Home",
        },
        component: () => import("../views/formulaire.vue"),
    },
    {
        path: "/addQuestion",
        name: "Question",
        meta: {
            title: "addQuestion",
        },
        component: () => import("../views/addQuestion.vue"),
    },
    {
        path: "/AllQuestions",
        name: "AllQuestions",
        meta: {
            title: "AllQuestions",
        },
        component: () => import("../views/questions.vue"),
    },
    {
        path: '/editQuestion/:id',
        name: 'editQuestion',
        component: () => import("../views/editQuestion.vue"),
        props: true
    },
    {
        path: "/addEtudiant",
        name: "addEtudiant",
        meta: {
            title: "addEtudiant",
        },
        component: () => import("../views/addEtudiant.vue"),
    },
    {
        path: "/allEtudiants",
        name: "allEtudiants",
        meta: {
            title: "allEtudiants",
        },
        component: () => import("../views/etudiants.vue"),
    },
    {
        path: '/editEtudiant/:id',
        name: 'editEtudiant',
        component: () => import("../views/editEtudiant.vue"),
        props: true
    },
    {
        path: '/etudiant/:id/tests',
        name: 'etudiantTests',
        component: () => import("../views/etudiantTests.vue"),
        props: true
    },
    {
        path: "/checkExistingTest",
        name: "checkExistingTest",
        meta: {
            title: "checkExistingTest",
        },
        component: () => import("../views/checkEmail.vue"),
    },
    {
        path: "/admin",
        name: "admin",
        meta: {
            title: "admin",
        },
        component: () => import("../views/admin.vue"),
    },
    {
        path: "/login",
        name: "login",
        meta: {
            title: "login",
        },
        component: () => import("../views/adminLogin.vue"),
    },
    {
        path: "/allTests",
        name: "allTests",
        meta: {
            title: "allTests",
        },
        component: () => import("../views/tests.vue"),
    },

    /*
    {
        path: '/login',
        name: "Login",
        meta: {
            title: "Login",
        },
        component: () => import("../views/login.vue"),
    },*/

];
