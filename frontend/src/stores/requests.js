import { defineStore } from 'pinia'
import { getRequests, createRequest } from '../api/requests'

export const useRequestStore = defineStore('requests', {
  state: () => ({
    requests: [],
    pagination: {},
    loading: false,
    filters: {
      status: '',
      destino: '',
      start_date: '',
      end_date: '',
    },
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

    setFilter(key, value) {
      this.filters[key] = value
      this.fetchRequests(1)
    },
    changePage(page) {
      this.fetchRequests(page);
    },
  }
})
