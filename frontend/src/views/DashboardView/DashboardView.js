import { reactive, computed, watch, ref } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = computed(() => requestStore.pagination)
  const editingRow = ref(null)
  const editedStatus = ref('')
  const returnDateError = ref('')
  const idError = ref('')

  watch(() => filters.destino, (newVal) => {
    requestStore.setFilter('destino', newVal)
    })

  watch(
    () => [filters.start_date, filters.end_date],
    ([start, end]) => {
        if (start && end && end < start) {
        returnDateError.value = 'Data de volta não pode ser menor que a data de ida.'
        } else {
        returnDateError.value = ''
        }
    }
  )

  function applyFilter(key, value) {
    if (key === 'end_date' && returnDateError.value) {      
      return
    }
    requestStore.setFilter(key, value)
  }

  function handleIdInput(e) {
    const val = e.target.value
    if (/^\d*$/.test(val)) {
      idError.value = ''
      filters.id = val
      applyFilter('id', val)
    } else {
      idError.value = 'Só aceita números'
    }
  }



  function formatDateBR(dateStr) {
    if (!dateStr) return ''
    
    const [year, month, day] = dateStr.split('T')[0].split('-')
    return `${day}/${month}/${year}`
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
    handleIdInput,
    formatDateBR,
    editingRow,
    editedStatus,
    updateStatus,
    returnDateError,
    idError
  }
}
