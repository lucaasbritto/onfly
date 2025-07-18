import api from './index'

export async function getRequests(filters = {}) {
  const response = await api.get('/requests', { params: filters });
  return response.data;
}

export async function createRequest(data) {
  const response = await api.post('/requests', data);
  return response.data.data;
}

export async function updateRequestStatus(id, payload) {
  const response = await api.patch(`/requests/${id}`, payload)
  return response.data.data
}
