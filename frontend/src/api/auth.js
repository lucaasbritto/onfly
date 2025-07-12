import api from './index'

export async function login(email, password) {
  try {
    const response = await api.post('/login', { email, password })

    return {
      token: response.data.access_token,
      user: response.data.user
    }
  } catch (error) {
    throw new Error(error.response?.data?.error || 'Erro ao tentar fazer login')
  }
}

export function logout() {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
}
