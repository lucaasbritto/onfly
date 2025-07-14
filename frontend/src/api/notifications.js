import api from './index.js';

export async function getNotifications() {
  const res = await api.get('/notifications');
  return res.data;
}

export async function markAllNotificationsRead() {
  await api.patch('/notifications/read');
}

export async function markNotificationRead(id) {
  await api.patch(`/notifications/${id}/read`)
}
