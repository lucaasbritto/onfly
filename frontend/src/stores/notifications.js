import { defineStore } from 'pinia'
import { getNotifications, markAllNotificationsRead,markNotificationRead  } from '../api/notifications'

export const useNotificationStore = defineStore('notifications', {
  state: () => ({
    items: [],
    unreadCount: 0,
  }),
  actions: {
    async fetch() {
      const data = await getNotifications()
      this.items = data
      this.unreadCount = data.filter(n => !n.read_at).length
    },

    async markAllRead() {
      await markAllNotificationsRead()
      this.items = this.items.map(n => ({
        ...n,
        read_at: new Date().toISOString(),
      }))
      this.unreadCount = 0
    },

    async markOneRead(id) {
        const notif = this.items.find(n => n.id === id)
        if (notif && !notif.read_at) {
            notif.read_at = new Date().toISOString()
            this.unreadCount = this.items.filter(n => !n.read_at).length

            try {
            await markNotificationRead(id)
            } catch (error) {            
            notif.read_at = null
            this.unreadCount = this.items.filter(n => !n.read_at).length
            throw error
            }
        }
    }
  }
})
