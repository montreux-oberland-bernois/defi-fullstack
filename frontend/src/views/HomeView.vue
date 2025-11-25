<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { statsService, type DashboardStats } from '@/services/statsService'

const { t, locale } = useI18n()
const authStore = useAuthStore()
const userName = computed(() => authStore.user?.name ?? 'Utilisateur')

const stats = ref<DashboardStats | null>(null)
const loading = ref(false)

const codeColors: Record<string, string> = {
  FRET: 'orange',
  PASS: 'blue',
  MAINT: 'purple',
  TEST: 'teal',
}

const formatMonth = (month: string) => {
  const [year, m] = month.split('-')
  const monthNames = locale.value === 'fr'
    ? ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
    : ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
  return `${monthNames[parseInt(m) - 1]} ${year}`
}

onMounted(async () => {
  loading.value = true
  try {
    stats.value = await statsService.getDashboard()
  } catch {
    // Silently fail - stats will just not show
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" lg="10">
        <!-- Welcome Card -->
        <v-card class="pa-6 mb-6">
          <v-card-title class="text-h4 text-center mb-2">
            <v-icon icon="mdi-train" size="48" color="primary" class="mr-3" />
            {{ t('home.welcome', { name: userName }) }}
          </v-card-title>
          <v-card-text class="text-center text-body-1">
            {{ t('home.description') }}
          </v-card-text>
        </v-card>

        <!-- KPI Cards -->
        <v-row v-if="stats" class="mb-6">
          <v-col cols="6" md="3">
            <v-card color="primary" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-train" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.routesThisMonth }}</div>
              <div class="text-caption">{{ t('dashboard.routesThisMonth') }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="success" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-map-marker-distance" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.distanceThisMonth }}</div>
              <div class="text-caption">{{ t('dashboard.distanceThisMonth') }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="info" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-history" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.totalRoutes }}</div>
              <div class="text-caption">{{ t('dashboard.totalRoutes') }}</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="warning" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-road-variant" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.totalDistance }}</div>
              <div class="text-caption">{{ t('dashboard.totalDistance') }}</div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Distribution Card -->
        <v-card v-if="stats && stats.distribution.length > 0" class="mb-6 pa-4">
          <v-card-title class="text-h6">
            <v-icon icon="mdi-chart-pie" class="mr-2" />
            {{ t('dashboard.distribution', { month: formatMonth(stats.month) }) }}
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col
                v-for="item in stats.distribution"
                :key="item.code"
                cols="6"
                sm="3"
              >
                <v-card :color="codeColors[item.code]" variant="outlined" class="pa-3 text-center">
                  <div class="text-overline">{{ t(`analyticCodes.labels.${item.code}`) }}</div>
                  <div class="text-h5 font-weight-bold">{{ item.count }}</div>
                  <div class="text-caption">{{ item.distance }} km</div>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <!-- Loading state -->
        <v-row v-if="loading" class="mb-6">
          <v-col cols="12" class="text-center">
            <v-progress-circular indeterminate color="primary" />
          </v-col>
        </v-row>

        <!-- Navigation Cards -->
        <v-row>
          <v-col cols="12" sm="4">
            <v-card
              to="/routes/new"
              color="primary"
              variant="tonal"
              class="pa-4 text-center h-100"
              hover
            >
              <v-icon icon="mdi-plus-circle" size="48" color="primary" />
              <v-card-title class="text-h6">{{ t('home.newRoute') }}</v-card-title>
              <v-card-text>
                {{ t('home.newRouteDesc') }}
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card
              to="/routes"
              color="secondary"
              variant="tonal"
              class="pa-4 text-center h-100"
              hover
            >
              <v-icon icon="mdi-format-list-bulleted" size="48" color="secondary" />
              <v-card-title class="text-h6">{{ t('home.myRoutes') }}</v-card-title>
              <v-card-text>
                {{ t('home.myRoutesDesc') }}
              </v-card-text>
            </v-card>
          </v-col>

          <v-col cols="12" sm="4">
            <v-card
              to="/stats"
              color="success"
              variant="tonal"
              class="pa-4 text-center h-100"
              hover
            >
              <v-icon icon="mdi-chart-bar" size="48" color="success" />
              <v-card-title class="text-h6">{{ t('home.statistics') }}</v-card-title>
              <v-card-text>
                {{ t('home.statisticsDesc') }}
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>
