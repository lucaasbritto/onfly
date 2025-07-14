<template>
  <div class="modal-backdrop" v-if="visible">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ title }}</h5>
        </div>
        <div class="modal-body">
          <p>{{ message }}</p>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" @click="$emit('cancel')" :disabled="loading">Cancelar</button>
          <button class="btn btn-primary" @click="$emit('confirm')" :disabled="loading">
            <span v-if="loading" class="spinner-border spinner-border-sm"></span>
            <span v-else>Confirmar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
  title: String,
  message: String,
  loading: Boolean,
  visible: {
    type: Boolean,
    default: true
  }
})
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}
.modal-dialog {
  background: white;
  border-radius: 5px;
  width: 400px;
  max-width: 90%;
  padding: 0;
}
.modal-header, .modal-footer {
  padding: 1rem;
  border-bottom: 1px solid #dee2e6;
}
.modal-body {
  padding: 1rem;
}
.modal-footer {
  border-top: 1px solid #dee2e6;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}
</style>
