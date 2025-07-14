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
          <th>ID</th>
          <th v-if="userStore.isAdmin">Solicitante</th>
          <th>Destino</th>
          <th>Ida</th>
          <th>Volta</th>
          <th>Status</th>
          <th v-if="userStore.isAdmin">Atualizado Por</th>
          <th v-if="userStore.isAdmin">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="req in requestStore.requests" :key="req.id">
          <td>{{ req.id }}</td>
          <td v-if="userStore.isAdmin">{{ req.user?.name || '—' }}</td>
          <td>{{ req.destino }}</td>
          <td>{{ formatDateBR(req.data_ida) }}</td>
          <td>{{ formatDateBR(req.data_volta) }}</td>
          <td>
            <div v-if="editingRow === req.id">
              <select v-model="editedStatus" class="form-select form-select-sm">
                <option value="aprovado">Aprovado</option>
                <option value="cancelado">Cancelado</option>
              </select>
            </div>
            <div v-else>
              <span
                :class="{
                  'badge bg-warning': req.status === 'solicitado',
                  'badge bg-success': req.status === 'aprovado',
                  'badge bg-danger': req.status === 'cancelado',
                }"
              >
                {{ req.status }}
              </span>
            </div>
          </td>
          <td v-if="userStore.isAdmin">{{ req.updated_by_user?.name || '—' }}</td>
          <td v-if="userStore.isAdmin">
            <button class="btn btn-sm btn-outline-success me-1 d-inline-flex align-items-center"
                    v-if="req.status === 'solicitado'"
                    @click="openConfirmDialog(req.id, 'aprovado')">
              <i class="bi bi-check-lg me-1"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger d-inline-flex align-items-center"
                   v-if="req.status === 'solicitado'"
                    @click="openConfirmDialog(req.id, 'cancelado')">
              <i class="bi bi-x-lg me-1"></i>
            </button>
          </td>
        </tr>
        <tr v-if="!requestStore.loading && requestStore.requests.length === 0">
          <td colspan="6" class="text-center text-muted">Nenhum pedido encontrado.</td>
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

    <ConfirmDialog
      v-if="confirmModal"
      :title="confirmTitle"
      :message="confirmMessage"
      :loading="confirmLoading"
      @cancel="confirmModal = false"
      @confirm="handleConfirm"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useDashboardScript } from './DashboardView.js'
import './DashboardView.scss'
import Modal from '../../components/Modal.vue'
import TravelRequestForm from '../../components/TravelRequestForm/TravelRequestForm.vue'
import { useUserStore } from '../../stores/user'
import ConfirmDialog from '../../components/ConfirmDialog.vue'
import { useToast } from 'vue-toast-notification'

const userStore = useUserStore()
const toast = useToast()

const confirmModal = ref(false)
const confirmLoading = ref(false)
const confirmTitle = ref('')
const confirmMessage = ref('')
let selectedAction = ref(null)
let selectedRequestId = ref(null)

const {
  requestStore,
  filters,
  pagination,
  applyFilter,
  formatDateBR,
  editingRow,
  editedStatus,
  updateStatus,
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

function openConfirmDialog(id, action) {
  const request = requestStore.requests.find(r => r.id === id)

  if (action === 'cancelado' && request.status === 'aprovado') {
    toast.error('Não é possível cancelar um pedido que já foi aprovado.')
    return
  }

  selectedRequestId.value = id
  selectedAction.value = action
  confirmTitle.value = action === 'aprovado' ? 'Confirmar Aprovação' : 'Confirmar Cancelamento'
  confirmMessage.value = `Tem certeza que deseja ${action === 'aprovado' ? 'aprovar' : 'cancelar'} este pedido?`
  confirmModal.value = true
}

async function handleConfirm() {
  confirmLoading.value = true
  try {
    await requestStore.updateStatus(selectedRequestId.value, selectedAction.value)

    const item = requestStore.requests.find(r => r.id === selectedRequestId.value)
    if (item) item.status = selectedAction.value

    toast.success(`Pedido ${selectedAction.value === 'aprovado' ? 'aprovado' : 'cancelado'} com sucesso.`)
  } catch (e) {
    toast.error('Erro ao atualizar o status')
    console.error(e)
  } finally {
    confirmLoading.value = false
    confirmModal.value = false
    selectedRequestId.value = null
    selectedAction.value = null
  }
}

</script>
