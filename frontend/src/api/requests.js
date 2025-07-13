import api from './index'

export async function getRequests(filters = {}) {
  const response = await api.get('/requests', { params: filters });
  return response.data;
}
