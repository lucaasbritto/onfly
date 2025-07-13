import { reactive, computed, watch } from 'vue'
import { useRequestStore } from '../../stores/requests'

export function useDashboardScript() {
  const requestStore = useRequestStore()
  const filters = reactive(requestStore.filters)
  const pagination = computed(() => requestStore.pagination)

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

  function criarPedido() {
    //
  }

  return {
    requestStore,
    filters,
    pagination,
    applyFilter,
    formatDateBR,
    criarPedido
  }
}
