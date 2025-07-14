import api from './index.js'

export async function getUserProfile() {
  const response = await api.get('/me')
  return response.data
}

export async function getUsers() {
  const response = await api.get('/users')
  return response.data
}

export async function getAdmins() {
  const response = await api.get('/admins')
  return response.data
}