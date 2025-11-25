import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Station } from '@/types'
import { stationService } from '@/services/stationService'

export const useStationsStore = defineStore('stations', () => {
  const stations = ref<Station[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  const fetchStations = async (search?: string) => {
    loading.value = true
    error.value = null
    try {
      stations.value = await stationService.getAll(search)
    } catch (err: unknown) {
      const axiosError = err as { response?: { data?: { message?: string } } }
      error.value = axiosError.response?.data?.message ?? 'Erreur lors du chargement des stations'
    } finally {
      loading.value = false
    }
  }

  const getStationByShortName = (shortName: string): Station | undefined => {
    return stations.value.find(s => s.shortName === shortName)
  }

  return {
    stations,
    loading,
    error,
    fetchStations,
    getStationByShortName,
  }
})
