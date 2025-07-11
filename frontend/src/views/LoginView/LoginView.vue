<template>
  <div class="login-page d-flex align-items-center justify-content-center">
    <div class="background-image"></div>

    <div class="login-card p-5">
      <div class="text-center mb-4">
        <img src="/logo-onfly.webp" alt="Plane Logo" class="logo" />
        <p class="text-muted">Acesso ao portal de viagens corporativas</p>
      </div>

      <form @submit.prevent="handleLogin" novalidate>
        <div class="mb-4">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input
            v-model="email"
            type="email"
            class="form-control"
            id="email"
            placeholder="seu@email.com"
            required
            :disabled="loading"
          />
        </div>

        <div class="mb-5">
          <label for="password" class="form-label fw-semibold">Senha</label>
          <input
            v-model="password"
            type="password"
            class="form-control"
            id="password"
            placeholder="********"
            required
            :disabled="loading"
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary w-100 fw-bold d-flex justify-content-center align-items-center"
          :disabled="loading"
        >
          <span v-if="!loading">Embarque</span>
          <span v-else>
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            Entrando...
          </span>
        </button>

        <div v-if="error" class="alert alert-danger mt-3" role="alert">{{ error }}</div>
      </form>

      <p class="text-center mt-4 text-muted" style="font-size: 0.85rem;">© 2025 Onfly</p>
    </div>
  </div>
</template>

<script>
import { LoginViewScript }from './LoginView.js';


</script>

<style lang="scss" scoped>
    @import './LoginView.scss'; 
</style>
