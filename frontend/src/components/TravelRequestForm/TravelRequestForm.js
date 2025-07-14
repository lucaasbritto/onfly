import { reactive, ref, computed } from 'vue'
import { useRequestStore } from '../../stores/requests'
import { useToast } from 'vue-toast-notification'

export function useTravelRequestFormScript(emit) {
  const requestStore = useRequestStore()
  const toast = useToast()
  const loading = ref(false)

  const form = reactive({
    destino: '',
    data_ida: '',
    data_volta: ''
  })

  const isReturnDateValid = computed(() => {
    if (!form.data_ida || !form.data_volta) return true
    return new Date(form.data_volta) >= new Date(form.data_ida)
  })

  const formIsValid = computed(() => {
    return (
      form.destino.trim() !== '' &&
      form.data_ida &&
      form.data_volta &&
      isReturnDateValid.value
    )
  })

  async function submitForm() {
    if (!formIsValid.value) return

    loading.value = true
    try {
      await requestStore.createRequest(form)
      toast.success('Pedido criado com sucesso!')
      emit('saved')
    } catch (e) {
      toast.error('Erro ao salvar pedido')
      console.error('Erro ao salvar:', e)
    } finally {
      loading.value = false
    }
  }

  return {
    form,
    loading,
    formIsValid,
    isReturnDateValid,
    submitForm
  }
}
