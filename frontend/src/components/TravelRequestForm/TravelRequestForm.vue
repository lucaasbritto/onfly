<template>
  <form @submit.prevent="submitForm" class="travel-form">
    <div class="form-header">
      <h5>Novo Pedido de Viagem</h5>
      <button type="button" class="btn-close" @click="$emit('close')" />
    </div>

    <div class="form-group">
      <label>Destino</label>
      <input v-model="form.destino" type="text" placeholder="Digite o destino" required />
    </div>

    <div class="form-group">
      <label>Data de ida</label>
      <input v-model="form.data_ida" type="date" required />
    </div>

    <div class="form-group">
      <label>Data de volta</label>
      <input v-model="form.data_volta" type="date" required />
      <small v-if="form.data_volta && !isReturnDateValid" class="text-danger">
        A data de volta deve ser após ou igual a data de ida.
      </small>
    </div>

    <div class="form-actions">
      <button type="button" class="btn btn-secondary btn-sm me-2" :disabled="loading" @click="$emit('close')">
        Cancelar
      </button>
      <button
        type="submit"
        class="btn btn-success btn-sm"
        :disabled="!formIsValid || loading"
      >
        <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
        Salvar
      </button>
    </div>
  </form>
</template>

<script setup>
import { defineEmits } from 'vue'
import { useTravelRequestFormScript } from './TravelRequestForm.js'

const emit = defineEmits(['close', 'saved'])
const {
  form,
  loading,
  formIsValid,
  isReturnDateValid,
  submitForm
} = useTravelRequestFormScript(emit)
</script>

<style scoped lang="scss">
@import './TravelRequestForm.scss';
</style>
