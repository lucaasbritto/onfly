<template>
  <div class="dashboard container py-5">
    <div class="card shadow-sm mb-4 border-0">
      <div class="card-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label fw-semibold">Status</label>
            <select v-model="filters.status" @change="applyFilter('status', filters.status)" class="form-select">
              <option value="">Todos</option>
              <option value="aprovado">Aprovado</option>
              <option value="cancelado">Cancelado</option>
              <option value="solicitado">Solicitado</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Destino</label>
            <input v-model="filters.destino" class="form-control" placeholder="Digite o destino" />
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Data ida</label>
            <input type="date" v-model="filters.start_date" @change="applyFilter('start_date', filters.start_date)" class="form-control" />
          </div>
          <div class="col-md-3">
            <label class="form-label fw-semibold">Data volta</label>
            <input type="date" v-model="filters.end_date" @change="applyFilter('end_date', filters.end_date)" class="form-control" />
          </div>
        </div>
      </div>
    </div>

    <div class="text-end mb-2">
        <button class="btn btn-primary btn-sm d-inline-flex align-items-center gap-1" @click="showModal = true">
            <i class="bi bi-plus-circle"></i> Novo pedido
        </button>
    </div>

    <Modal v-if="showModal" >
      <TravelRequestForm  
        @close="closeModal"
        @saved="handleSaved" 
      />
    </Modal>

    <div v-if="requestStore.loading" class="text-center py-5">
      <div class="spinner-border text-primary"></div>
    </div>

    <table v-else class="table table-hover shadow-sm">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Destino</th>
          <th>Ida</th>
          <th>Volta</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="req in requestStore.requests" :key="req.id">
          <td>{{ req.id }}</td>
          <td>{{ req.destino }}</td>
          <td>{{ formatDateBR(req.data_ida) }}</td>
          <td>{{ formatDateBR(req.data_volta) }}</td>
          <td>
            <span
              :class="{
                'badge bg-warning': req.status === 'solicitado',
                'badge bg-success': req.status === 'aprovado',
                'badge bg-danger': req.status === 'cancelado',
              }"
            >{{ req.status }}</span>
          </td>
        </tr>
        <tr v-if="!requestStore.loading && requestStore.requests.length === 0">
          <td colspan="5" class="text-center text-muted">Nenhum pedido encontrado.</td>
        </tr>
      </tbody>
    </table>

    <nav v-if="pagination.lastPage > 1" aria-label="Page navigation">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
          <button class="page-link" @click="requestStore.changePage(pagination.currentPage - 1)">Anterior</button>
        </li>
        <li class="page-item"
            v-for="page in [...Array(pagination.lastPage).keys()].map(i => i + 1)"
            :key="page"
            :class="{ active: page === pagination.currentPage }">
          <button class="page-link" @click="requestStore.changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
          <button class="page-link" @click="requestStore.changePage(pagination.currentPage + 1)">Próxima</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useDashboardScript } from './DashboardView.js'
import './DashboardView.scss'
import Modal from '../../components/Modal.vue'
import TravelRequestForm from '../../components/TravelRequestForm/TravelRequestForm.vue'

const {
  requestStore,
  filters,
  pagination,
  applyFilter,
  formatDateBR
} = useDashboardScript()

const showModal = ref(false)
onMounted(() => {
  requestStore.fetchRequests()
})

function closeModal() {
  showModal.value = false
}

function handleSaved() {
  closeModal()
}
</script>
