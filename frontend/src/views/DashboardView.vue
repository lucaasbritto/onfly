<template>
  <div class="container py-5">
    <h1 class="mb-4">Bem-vindo, {{ userStore.userName }}</h1>
  </div>
</template>

<script setup>
import { useUserStore } from '../stores/user.js'

const userStore  = useUserStore()


</script>