<template>
  <div class="dashboard-layout d-flex">
    <button
      class="btn btn-primary btn-sm toggle-open-btn"
      v-if="isSidebarCollapsed"
      @click="toggleSidebar"
      aria-label="Abrir menu de filtros"
    >
      <i class="bi bi-chevron-right"></i>
    </button>

    <aside class="dashboard-sidebar" :class="{ collapsed: isSidebarCollapsed }" aria-label="Filtros de pesquisa">
      <div class="sidebar-header d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 text-white">Filtros</h5>
        <button
          class="btn btn-sm btn-outline-light d-none d-lg-inline-flex"
          @click="toggleSidebar"
          aria-label="Fechar menu de filtros"
        >
          <i class="bi" :class="isSidebarCollapsed ? 'bi-chevron-right' : 'bi-chevron-left'"></i>
        </button>
      </div>

      <div class="p-3">
        <div class="mb-3">
          <label class="form-label text-white" for="filter-status">Status</label>
          <select id="filter-status" class="form-select" v-model="filters.status" @change="applyFilter('status', filters.status)">
            <option value="">Todos</option>
            <option value="aprovado">Aprovado</option>
            <option value="cancelado">Cancelado</option>
            <option value="solicitado">Solicitado</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="filter-destino" class="form-label text-white">Destino</label>
          <input id="filter-destino" v-model="filters.destino" class="form-control" placeholder="Digite o destino" @input="applyFilter('destino', filters.destino)" />
        </div>

        <div class="mb-3">
          <label for="filter-start_date" class="form-label text-white">Data ida</label>
          <input id="filter-start_date" type="date" v-model="filters.start_date" class="form-control" @change="applyFilter('start_date', filters.start_date)" />
        </div>

        <div class="mb-3">
          <label for="filter-end_date" class="form-label text-white">Data volta</label>
          <input id="filter-end_date" type="date" v-model="filters.end_date" class="form-control" @change="applyFilter('end_date', filters.end_date)" />
          <small v-if="returnDateError" class="text-danger">{{ returnDateError }}</small>
        </div>

        <template v-if="userStore.isAdmin">
          <div class="mb-3">
            <label for="filter-id" class="form-label text-white">ID</label>
            <input id="filter-id" type="text" v-model="filters.id" class="form-control" placeholder="Digite o número ID" @input="handleIdInput" />
            <small v-if="idError" class="text-danger">{{ idError }}</small>
          </div>

          <div class="mb-3">
            <label for="filter-user_id" class="form-label text-white">Solicitante</label>
            <select id="filter-user_id" v-model="filters.user_id" class="form-select" @change="applyFilter('user_id', filters.user_id)">
              <option value="">Todos</option>
              <option v-for="user in usersList" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="filter-admin_id" class="form-label text-white">Atualizado por</label>
            <select id="filter-admin_id" v-model="filters.admin_id" class="form-select" @change="applyFilter('admin_id', filters.admin_id)">
              <option value="">Todos</option>
              <option v-for="admin in adminsList" :key="admin.id" :value="admin.id">{{ admin.name }}</option>
            </select>
          </div>
        </template>
      </div>
    </aside>

    <main class="dashboard-content flex-grow-1 p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Pedidos de Viagem</h4>
        <button class="btn btn-primary btn-sm" @click="showModal = true" aria-label="Novo pedido de viagem">
          <i class="bi bi-plus-circle me-1"></i> Novo pedido
        </button>
      </div>

      <div v-if="requestStore.loading" class="text-center py-5">
        <div class="spinner-border text-primary" role="status" aria-label="Carregando..."></div>
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
            <th v-if="userStore.isAdmin">Atualizado por</th>
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
              <span
                :class="{
                  'badge bg-warning': req.status === 'solicitado',
                  'badge bg-success': req.status === 'aprovado',
                  'badge bg-danger': req.status === 'cancelado',
                }"
              >
                {{ req.status }}
              </span>
            </td>
            <td v-if="userStore.isAdmin">{{ req.updated_by_user?.name || '—' }}</td>
            <td v-if="userStore.isAdmin">
              <button
                class="btn btn-sm btn-outline-success me-1 d-inline-flex align-items-center"
                v-if="req.status === 'solicitado'"
                @click="openConfirmDialog(req.id, 'aprovado')"
                aria-label="Aprovar pedido"
                title="Aprovar pedido"
              >
                <i class="bi bi-check-lg me-1"></i>
              </button>
              <button
                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center"
                v-if="req.status === 'solicitado'"
                @click="openConfirmDialog(req.id, 'cancelado')"
                aria-label="Cancelar pedido"
                title="Cancelar pedido"
              >
                <i class="bi bi-x-lg me-1"></i>
              </button>
            </td>
          </tr>
          <tr v-if="!requestStore.loading && requestStore.requests.length === 0">
            <td colspan="8" class="text-center text-muted">Nenhum pedido encontrado.</td>
          </tr>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap">
        <div v-if="pagination.lastPage > 1" class="text-muted small mb-2 mb-md-0">
          Página {{ pagination.currentPage }} de {{ pagination.lastPage }} — {{ pagination.total }} registros
        </div>

        <nav v-if="pagination.lastPage > 1" aria-label="Navegação de páginas">
          <ul class="pagination mb-0">
            <li class="page-item" :class="{ disabled: pagination.currentPage === 1 }">
              <button class="page-link" @click="requestStore.changePage(pagination.currentPage - 1)">Anterior</button>
            </li>
            <li
              class="page-item"
              v-for="page in pagination.lastPage"
              :key="page"
              :class="{ active: page === pagination.currentPage }"
            >
              <button class="page-link" @click="requestStore.changePage(page)">{{ page }}</button>
            </li>
            <li class="page-item" :class="{ disabled: pagination.currentPage === pagination.lastPage }">
              <button class="page-link" @click="requestStore.changePage(pagination.currentPage + 1)">Próxima</button>
            </li>
          </ul>
        </nav>
      </div>
    </main>

    <Modal v-if="showModal" @close="closeModal">
      <TravelRequestForm @close="closeModal" @saved="handleSaved" />
    </Modal>

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
import ConfirmDialog from '../../components/ConfirmDialog.vue'
import { useUserStore } from '../../stores/user'
import { useToast } from 'vue-toast-notification'
import { getUsers, getAdmins } from '../../api/user'

const userStore = useUserStore()
const toast = useToast()
const isSidebarCollapsed = ref(false)

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
  handleIdInput,
  formatDateBR,
  editingRow,
  editedStatus,
  updateStatus,
  returnDateError, 
  idError
} = useDashboardScript()

const showModal = ref(false)
const usersList = ref([])
const adminsList = ref([])

onMounted(() => {  
  if (userStore.isAdmin) {
    loadFiltersData()
  }
  requestStore.fetchRequests()
})

function closeModal() {
  showModal.value = false
}

function handleSaved() {
  closeModal()
}

async function loadFiltersData() {
  try {
    usersList.value = await getUsers()
  } catch (e) {
    toast.error('Erro ao carregar usuários')
  }

  try {
    adminsList.value = await getAdmins()
  } catch (e) {
    toast.error('Erro ao carregar administradores')
  }
}

function toggleSidebar() {
  isSidebarCollapsed.value = !isSidebarCollapsed.value
}

function openConfirmDialog(id, action) {
  const request = requestStore.requests.find((r) => r.id === id)

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

    const item = requestStore.requests.find((r) => r.id === selectedRequestId.value)
    if (item) item.status = selectedAction.value

    toast.success(
      `Pedido ${selectedAction.value === 'aprovado' ? 'aprovado' : 'cancelado'} com sucesso.`
    )
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
