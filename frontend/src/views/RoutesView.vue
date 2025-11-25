<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { routeService } from '@/services/routeService'
import type { Route } from '@/types'

const routes = ref<Route[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const currentPage = ref(1)
const totalPages = ref(1)
const total = ref(0)
const perPage = ref(15)

const headers = [
  { title: 'ID', key: 'id', sortable: false },
  { title: 'Départ', key: 'fromStationId', sortable: true },
  { title: 'Arrivée', key: 'toStationId', sortable: true },
  { title: 'Distance (km)', key: 'distanceKm', sortable: true },
  { title: 'Code analytique', key: 'analyticCode', sortable: true },
  { title: 'Date', key: 'createdAt', sortable: true },
  { title: 'Itinéraire', key: 'path', sortable: false },
]

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleString('fr-FR', {
    dateStyle: 'short',
    timeStyle: 'short',
  })
}

const formatPath = (path: string[]) => {
  if (path.length <= 3) return path.join(' → ')
  return `${path[0]} → ... → ${path[path.length - 1]}`
}

const fetchRoutes = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await routeService.getAll(currentPage.value, perPage.value)
    routes.value = response.data
    totalPages.value = response.meta.last_page
    total.value = response.meta.total
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string } } }
    error.value = axiosError.response?.data?.message ?? 'Erreur lors du chargement des trajets'
  } finally {
    loading.value = false
  }
}

const handlePageChange = (page: number) => {
  currentPage.value = page
  fetchRoutes()
}

onMounted(() => {
  fetchRoutes()
})
</script>

<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-icon icon="mdi-format-list-bulleted" class="mr-2" />
            Historique des trajets
            <v-spacer />
            <v-btn
              color="primary"
              to="/routes/new"
              prepend-icon="mdi-plus"
            >
              Nouveau trajet
            </v-btn>
          </v-card-title>

          <v-card-text>
            <v-alert
              v-if="error"
              type="error"
              variant="tonal"
              class="mb-4"
              closable
            >
              {{ error }}
            </v-alert>

            <v-data-table
              :headers="headers"
              :items="routes"
              :loading="loading"
              :items-per-page="perPage"
              hide-default-footer
              class="elevation-1"
            >
              <template #item.id="{ item }">
                <code class="text-caption">{{ item.id.substring(0, 8) }}...</code>
              </template>

              <template #item.distanceKm="{ item }">
                <v-chip color="primary" size="small">
                  {{ item.distanceKm }} km
                </v-chip>
              </template>

              <template #item.analyticCode="{ item }">
                <v-chip color="secondary" size="small" variant="outlined">
                  {{ item.analyticCode }}
                </v-chip>
              </template>

              <template #item.createdAt="{ item }">
                {{ formatDate(item.createdAt) }}
              </template>

              <template #item.path="{ item }">
                <v-tooltip location="top">
                  <template #activator="{ props }">
                    <span v-bind="props" class="cursor-pointer">
                      {{ formatPath(item.path) }}
                      <v-chip size="x-small" class="ml-1">
                        {{ item.path.length }} stations
                      </v-chip>
                    </span>
                  </template>
                  <span>{{ item.path.join(' → ') }}</span>
                </v-tooltip>
              </template>

              <template #bottom>
                <div class="d-flex align-center justify-space-between pa-4">
                  <span class="text-caption text-grey">
                    {{ total }} trajet(s) au total
                  </span>
                  <v-pagination
                    v-model="currentPage"
                    :length="totalPages"
                    :total-visible="5"
                    @update:model-value="handlePageChange"
                  />
                </div>
              </template>

              <template #no-data>
                <div class="text-center pa-6">
                  <v-icon icon="mdi-train" size="64" color="grey-lighten-1" />
                  <p class="text-h6 text-grey mt-4">Aucun trajet enregistré</p>
                  <v-btn
                    color="primary"
                    to="/routes/new"
                    class="mt-2"
                  >
                    Créer un trajet
                  </v-btn>
                </div>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
