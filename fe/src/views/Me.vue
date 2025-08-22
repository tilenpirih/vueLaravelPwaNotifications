<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api'

const user = ref(null)

onMounted(() => {
  api.get('/auth/me')
    .then(response => {
      user.value = response.data.user
    })
    .catch(error => {
      console.error('Error fetching user:', error)
    })
})
</script>

<template>
  <div>
    <h2>Me</h2>
    <div v-if="user">
      <p>Name: {{ user.name }}</p>
      <p>Email: {{ user.email }}</p>
    </div>
  </div>
</template>

<style scoped></style>