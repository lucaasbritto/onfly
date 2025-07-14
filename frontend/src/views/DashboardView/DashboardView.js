import { reactive, computed, watch, ref } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = computed(() => requestStore.pagination)
  const editingRow = ref(null)
  const editedStatus = ref('')
  
  let debounceTimeout
  watch(() => filters.destino, (newVal) => {
    clearTimeout(debounceTimeout)
    debounceTimeout = setTimeout(() => {
      requestStore.setFilter('destino', newVal)
    }, 500)
  })

  function applyFilter(key, value) {
    requestStore.setFilter(key, value)
  }

  function formatDateBR(dateStr) {
    const date = new Date(dateStr)
    if (isNaN(date)) return ''
    return new Intl.DateTimeFormat('pt-BR').format(date)
  }

  
  async function updateStatus(id, status) {
    try {
        await requestStore.updateStatus(id, status)

        const item = requestStore.requests.find(r => r.id === id)
        if (item) item.status = status
    } catch (e) {
        console.error('Erro ao atualizar status', e)
    }
   }

  return {
    requestStore,
    filters,
    pagination,
    applyFilter,
    formatDateBR,
    editingRow,
    editedStatus,
    updateStatus,
  }
}
