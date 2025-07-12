import api from './index.js'

export async function getUserProfile() {
  const response = await api.get('/me')
  return response.data
}