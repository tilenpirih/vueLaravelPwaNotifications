<script setup lang="ts">
import { ref } from 'vue'
import api from '../api'

const name = ref('')
const email = ref('')
const password = ref('')
const error = ref('')
const success = ref('')

function register() {
  error.value = ''
  success.value = ''
  api.post('/auth/register', {
    name: name.value,
    email: email.value,
    password: password.value,
  })
    .then(() => {
      success.value = 'Registered!'
    })
    .catch((e: any) => {
      error.value = e.response?.data?.error || e.response?.data?.message || 'Registration failed'
    })
}
</script>

<template>
  <div>
    <h2>Register</h2>
    <form @submit.prevent="register">
      <div>
        <input v-model="name" placeholder="Name" required>
      </div>
      <div>
        <input v-model="email" placeholder="Email" type="email" required>
      </div>
      <div>
        <input v-model="password" placeholder="Password" type="password" required>
      </div>
      <button type="submit">
        Register
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