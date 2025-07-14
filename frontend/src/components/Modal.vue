<template>
  <div class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <slot />
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close'])
function close() {
  emit('close')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  border-radius: 8px;
  max-width: 500px;
  width: 100%;
  padding: 1.5rem;
  box-shadow: 0 0 20px rgba(0,0,0,0.3);
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
</style>
