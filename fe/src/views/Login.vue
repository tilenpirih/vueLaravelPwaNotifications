<script setup lang="ts">
import { ref } from 'vue'
import api from '../api'

const email = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

function login() {
  error.value = ''
  success.value = ''
  api.post('/auth/login', {
    email: email.value,
    password: password.value,
  })
    .then(() => {
      success.value = 'Logged in!'
    })
    .catch((e: any) => {
      error.value = e.response?.data?.error || e.response?.data?.message || 'Login failed'
    })
}
</script>

<template>
  <div>
    <h2>Login</h2>
    <form @submit.prevent="login">
      <div>
        <input v-model="email" placeholder="Email" type="email" required>
      </div>
      <div>
        <input v-model="password" placeholder="Password" type="password" required>
      </div>
      <button type="submit">
        Login
      </button>
    </form>
    <div v-if="error" style="color:red">
      {{ error }}
    </div>
    <div v-if="success" style="color:green">
      {{ success }}
    </div>
  </div>
</template>

<style scoped>

</style>