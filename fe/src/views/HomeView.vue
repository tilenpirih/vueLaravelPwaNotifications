<script setup lang="ts">
import router from '@/router';
</script>

<template>
  <div>
    <button @click="router.push('/register')">Register</button>
    <button @click="router.push('/login')">Login</button>
    <button @click="router.push('/me')">Me, notification panel</button>
  </div>
</template>
