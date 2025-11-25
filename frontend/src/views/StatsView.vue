<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
  type ChartData,
  type ChartOptions
} from 'chart.js'
import { statsService, type StatsParams } from '@/services/statsService'
import type { AnalyticDistanceList } from '@/types'

ChartJS.register(CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend)

const stats = ref<AnalyticDistanceList | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const fromDate = ref<string>('')
const toDate = ref<string>('')
const groupBy = ref<'none' | 'day' | 'month' | 'year'>('none')

const groupByOptions = [
  { title: 'Sans groupement', value: 'none' },
  { title: 'Par jour', value: 'day' },
  { title: 'Par mois', value: 'month' },
  { title: 'Par année', value: 'year' },
]

const chartData = computed<ChartData<'bar'>>(() => {
  if (!stats.value || stats.value.items.length === 0) {
    return { labels: [], datasets: [] }
  }

  const labels = stats.value.items.map(item => {
    if (item.group) {
      return `${item.analyticCode} (${item.group})`
    }
    return item.analyticCode
  })

  const data = stats.value.items.map(item => item.totalDistanceKm)

  return {
    labels,
    datasets: [
      {
        label: 'Distance totale (km)',
        data,
        backgroundColor: 'rgba(25, 118, 210, 0.7)',
        borderColor: 'rgba(25, 118, 210, 1)',
        borderWidth: 1,
      },
    ],
  }
})

const chartOptions: ChartOptions<'bar'> = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    title: {
      display: true,
      text: 'Distances par code analytique',
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      title: {
        display: true,
        text: 'Distance (km)',
      },
    },
    x: {
      title: {
        display: true,
        text: 'Code analytique',
      },
    },
  },
}

const totalDistance = computed(() => {
  if (!stats.value) return 0
  return stats.value.items.reduce((sum, item) => sum + item.totalDistanceKm, 0).toFixed(2)
})

const uniqueCodes = computed(() => {
  if (!stats.value) return 0
  const codes = new Set(stats.value.items.map(item => item.analyticCode))
  return codes.size
})

const fetchStats = async () => {
  loading.value = true
  error.value = null
  try {
    const params: StatsParams = {
      groupBy: groupBy.value,
    }
    if (fromDate.value) params.from = fromDate.value
    if (toDate.value) params.to = toDate.value

    stats.value = await statsService.getDistances(params)
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string } } }
    error.value = axiosError.response?.data?.message ?? 'Erreur lors du chargement des statistiques'
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  fromDate.value = ''
  toDate.value = ''
  groupBy.value = 'none'
  fetchStats()
}

onMounted(() => {
  fetchStats()
})

watch([fromDate, toDate, groupBy], () => {
  fetchStats()
})
</script>

<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card class="mb-4">
          <v-card-title>
            <v-icon icon="mdi-filter" class="mr-2" />
            Filtres
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="fromDate"
                  label="Date de début"
                  type="date"
                  prepend-inner-icon="mdi-calendar"
                  clearable
                />
              </v-col>
              <v-col cols="12" sm="4">
                <v-text-field
                  v-model="toDate"
                  label="Date de fin"
                  type="date"
                  prepend-inner-icon="mdi-calendar"
                  clearable
                />
              </v-col>
              <v-col cols="12" sm="4">
                <v-select
                  v-model="groupBy"
                  :items="groupByOptions"
                  label="Groupement"
                  prepend-inner-icon="mdi-group"
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col class="d-flex gap-2">
                <v-btn
                  color="primary"
                  :loading="loading"
                  @click="fetchStats"
                >
                  <v-icon icon="mdi-refresh" class="mr-2" />
                  Actualiser
                </v-btn>
                <v-btn
                  color="secondary"
                  variant="outlined"
                  @click="resetFilters"
                >
                  <v-icon icon="mdi-filter-remove" class="mr-2" />
                  Réinitialiser
                </v-btn>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="4">
        <v-card color="primary" variant="tonal">
          <v-card-text class="text-center">
            <v-icon icon="mdi-map-marker-distance" size="48" />
            <div class="text-h4 mt-2">{{ totalDistance }} km</div>
            <div class="text-subtitle-1">Distance totale</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="secondary" variant="tonal">
          <v-card-text class="text-center">
            <v-icon icon="mdi-tag-multiple" size="48" />
            <div class="text-h4 mt-2">{{ uniqueCodes }}</div>
            <div class="text-subtitle-1">Codes analytiques</div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="4">
        <v-card color="success" variant="tonal">
          <v-card-text class="text-center">
            <v-icon icon="mdi-chart-bar" size="48" />
            <div class="text-h4 mt-2">{{ stats?.items.length ?? 0 }}</div>
            <div class="text-subtitle-1">Entrées</div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon icon="mdi-chart-bar" class="mr-2" />
            Graphique des distances
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

            <div v-if="loading" class="d-flex justify-center pa-8">
              <v-progress-circular indeterminate color="primary" />
            </div>

            <div v-else-if="stats && stats.items.length > 0" style="height: 400px">
              <Bar :data="chartData" :options="chartOptions" />
            </div>

            <div v-else class="text-center pa-8">
              <v-icon icon="mdi-chart-box-outline" size="64" color="grey-lighten-1" />
              <p class="text-h6 text-grey mt-4">Aucune donnée à afficher</p>
              <p class="text-body-2 text-grey">
                Créez des trajets pour voir les statistiques ici.
              </p>
              <v-btn
                color="primary"
                to="/routes/new"
                class="mt-2"
              >
                Créer un trajet
              </v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row class="mt-4">
      <v-col cols="12">
        <v-card>
          <v-card-title>
            <v-icon icon="mdi-table" class="mr-2" />
            Détail des statistiques
          </v-card-title>
          <v-card-text>
            <v-data-table
              :headers="[
                { title: 'Code analytique', key: 'analyticCode' },
                { title: 'Distance totale (km)', key: 'totalDistanceKm' },
                { title: 'Période début', key: 'periodStart' },
                { title: 'Période fin', key: 'periodEnd' },
                { title: 'Groupe', key: 'group' },
              ]"
              :items="stats?.items ?? []"
              :loading="loading"
              class="elevation-1"
            >
              <template #item.totalDistanceKm="{ item }">
                <v-chip color="primary" size="small">
                  {{ item.totalDistanceKm }} km
                </v-chip>
              </template>

              <template #item.analyticCode="{ item }">
                <v-chip color="secondary" size="small" variant="outlined">
                  {{ item.analyticCode }}
                </v-chip>
              </template>

              <template #item.group="{ item }">
                <span v-if="item.group">{{ item.group }}</span>
                <span v-else class="text-grey">-</span>
              </template>

              <template #no-data>
                <div class="text-center pa-4">
                  <p class="text-grey">Aucune statistique disponible</p>
                </div>
              </template>
            </v-data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
