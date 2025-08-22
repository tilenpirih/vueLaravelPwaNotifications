<script setup lang="ts">
import { onMounted, ref } from 'vue'
import api from '@/api'

const user = ref(null)

onMounted(() => {
  api.get('/auth/me')
    .then(data => {
      user.value = data.data
    })
    .catch(() => {
      user.value = null
    })
})
</script>

<template>
  <div>
    <h2>User Information</h2>
    <pre>{{ user }}</pre>
  </div>
</template>
