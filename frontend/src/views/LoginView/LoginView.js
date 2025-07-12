import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../../stores/user.js'

export function loginViewScript() {
    const email = ref('')
    const password = ref('')
    const error = ref(null)
    const loading = ref(false)

    const userStore = useUserStore()
    const router = useRouter()

    const handleLogin = async () => {
    error.value = null
    loading.value = true
    try {
        await userStore.login(email.value, password.value)
        router.push('/')
    } catch (err) {
        error.value = err.message
    } finally {
        loading.value = false
    }
    }

    return {
        email,
        password,
        error,
        loading,
        handleLogin
    }
}