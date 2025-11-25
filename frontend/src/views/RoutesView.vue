<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { routeService, type RouteFilters } from '@/services/routeService'
import type { Route } from '@/types'

const { t, locale } = useI18n()

const routes = ref<Route[]>([])
const loading = ref(false)
const error = ref<string | null>(null)
const currentPage = ref(1)
const totalPages = ref(1)
const total = ref(0)
const perPage = ref(15)

// Filters
const filterAnalyticCode = ref<string | null>(null)
const filterFrom = ref<string | null>(null)
const filterTo = ref<string | null>(null)

const analyticCodes = computed(() => [
  { value: 'FRET', title: t('analyticCodes.FRET') },
  { value: 'PASS', title: t('analyticCodes.PASS') },
  { value: 'MAINT', title: t('analyticCodes.MAINT') },
  { value: 'TEST', title: t('analyticCodes.TEST') },
])

const headers = computed(() => [
  { title: t('table.id'), key: 'id', sortable: false },
  { title: t('table.departure'), key: 'fromStationId', sortable: true },
  { title: t('table.arrival'), key: 'toStationId', sortable: true },
  { title: t('table.distance'), key: 'distanceKm', sortable: true },
  { title: t('table.analyticCode'), key: 'analyticCode', sortable: true },
  { title: t('table.date'), key: 'createdAt', sortable: true },
  { title: t('table.itinerary'), key: 'path', sortable: false },
])

const formatDate = (dateStr: string) => {
  return new Date(dateStr).toLocaleString(locale.value === 'fr' ? 'fr-FR' : 'en-US', {
    dateStyle: 'short',
    timeStyle: 'short',
  })
}

const formatPath = (path: string[]) => {
  if (path.length <= 3) return path.join(' → ')
  return `${path[0]} → ... → ${path[path.length - 1]}`
}

const getFilters = (): RouteFilters => ({
  analyticCode: filterAnalyticCode.value || undefined,
  from: filterFrom.value || undefined,
  to: filterTo.value || undefined,
})

const fetchRoutes = async () => {
  loading.value = true
  error.value = null
  try {
    const response = await routeService.getAll(currentPage.value, perPage.value, getFilters())
    routes.value = response.data
    totalPages.value = response.meta.last_page
    total.value = response.meta.total
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string } } }
    error.value = axiosError.response?.data?.message ?? t('errors.loading')
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  currentPage.value = 1
  fetchRoutes()
}

const resetFilters = () => {
  filterAnalyticCode.value = null
  filterFrom.value = null
  filterTo.value = null
  currentPage.value = 1
  fetchRoutes()
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
            {{ t('route.history') }}
            <v-spacer />
            <v-btn
              color="primary"
              to="/routes/new"
              prepend-icon="mdi-plus"
            >
              {{ t('nav.newRoute') }}
            </v-btn>
          </v-card-title>

          <v-card-text>
            <!-- Filters -->
            <v-row class="mb-4" dense>
              <v-col cols="12" sm="4">
                <v-select
                  v-model="filterAnalyticCode"
                  :items="analyticCodes"
                  item-title="title"
                  item-value="value"
                  :label="t('filters.analyticCode')"
                  prepend-inner-icon="mdi-tag"
                  clearable
                  density="compact"
                  hide-details
                />
              </v-col>
              <v-col cols="12" sm="3">
                <v-text-field
                  v-model="filterFrom"
                  type="date"
                  :label="t('filters.from')"
                  prepend-inner-icon="mdi-calendar"
                  density="compact"
                  hide-details
                  clearable
                />
              </v-col>
              <v-col cols="12" sm="3">
                <v-text-field
                  v-model="filterTo"
                  type="date"
                  :label="t('filters.to')"
                  prepend-inner-icon="mdi-calendar"
                  density="compact"
                  hide-details
                  clearable
                />
              </v-col>
              <v-col cols="12" sm="2" class="d-flex align-center gap-1">
                <v-btn
                  color="primary"
                  variant="tonal"
                  size="small"
                  @click="applyFilters"
                >
                  <v-icon icon="mdi-filter" />
                </v-btn>
                <v-btn
                  variant="text"
                  size="small"
                  @click="resetFilters"
                >
                  <v-icon icon="mdi-filter-off" />
                </v-btn>
              </v-col>
            </v-row>

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
                        {{ t('route.stations', { count: item.path.length }) }}
                      </v-chip>
                    </span>
                  </template>
                  <span>{{ item.path.join(' → ') }}</span>
                </v-tooltip>
              </template>

              <template #bottom>
                <div class="d-flex align-center justify-space-between pa-4">
                  <span class="text-caption text-grey">
                    {{ t('route.total', { count: total }) }}
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
                  <p class="text-h6 text-grey mt-4">{{ t('route.noRoutes') }}</p>
                  <v-btn
                    color="primary"
                    to="/routes/new"
                    class="mt-2"
                  >
                    {{ t('route.create') }}
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
