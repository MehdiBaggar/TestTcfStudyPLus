<template>

  <router-view></router-view>
</template>

<script>
import axios from 'axios';

export default {
  beforeMount() {
    console.log('beforeMount() in App.vue is running!');
    const token = localStorage.getItem('Authorization');
    if (token) {
      console.log('JWT Token found in localStorage:', token);
      axios.defaults.headers.common['Authorization'] = token;
      console.log('Axios Authorization header set:', axios.defaults.headers.common['Authorization']);
    } else {
      console.log('No JWT Token found in localStorage.');
    }
  }
};
</script>