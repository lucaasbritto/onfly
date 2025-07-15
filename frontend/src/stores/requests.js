import { defineStore } from 'pinia'
import { getRequests, createRequest, updateRequestStatus } from '../api/requests'

export const useRequestStore = defineStore('requests', {
  state: () => ({
    requests: [],
    pagination: {},
    loading: false,
    filters: {
      id:'',
      status: '',
      destino: '',
      start_date: '',
      end_date: '',
      user_id: '',
      admin_id: '',
    },
    _debounceTimeouts: {},
  }),

  actions: {    
    async fetchRequests(page = 1) {
      this.loading = true;
      try {
        const payload = { ...this.filters, page };
        const res = await getRequests(payload);
        this.requests = res.data;
        this.pagination = {
          currentPage: res.current_page,
          lastPage: res.last_page,
          perPage: res.per_page,
          total: res.total,
        };
      } catch (e) {
        console.error('Erro ao buscar pedidos:', e)
      } finally {
        this.loading = false;
      }
    },

    async createRequest(payload) {
      try {
        const created = await createRequest(payload)
        this.requests.unshift(created)
        
        if (this.requests.length > this.pagination.perPage) {
            this.requests.pop()
        }
        
        return created
      } catch (e) {
        console.error('Erro ao criar pedido:', e)
        throw e
      }
    },

    async updateStatus(id, status) {
      try {
        const updated = await updateRequestStatus(id, { status })
        const index = this.requests.findIndex((r) => r.id === id)
        if (index !== -1) {
          this.requests[index] = updated
        }
      } catch (e) {
        console.error('Erro ao atualizar status:', e)
        throw e
      }
    },

    setFilter(key, value) {
      this.filters[key] = value
      clearTimeout(this._debounceTimeouts[key])

      this._debounceTimeouts[key] = setTimeout(() => {
        this.fetchRequests(1)
      }, 500)      
    },
    changePage(page) {
      this.fetchRequests(page);
    },
  }
})
