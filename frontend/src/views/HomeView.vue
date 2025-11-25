<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { statsService, type DashboardStats } from '@/services/statsService'

const authStore = useAuthStore()
const userName = computed(() => authStore.user?.name ?? 'Utilisateur')

const stats = ref<DashboardStats | null>(null)
const loading = ref(false)

const codeLabels: Record<string, string> = {
  FRET: 'Fret',
  PASS: 'Passagers',
  MAINT: 'Maintenance',
  TEST: 'Tests',
}

const codeColors: Record<string, string> = {
  FRET: 'orange',
  PASS: 'blue',
  MAINT: 'purple',
  TEST: 'teal',
}

const formatMonth = (month: string) => {
  const [year, m] = month.split('-')
  const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
    'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
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
            Bienvenue, {{ userName }}!
          </v-card-title>
          <v-card-text class="text-center text-body-1">
            Application de calcul de trajets ferroviaires et statistiques
          </v-card-text>
        </v-card>

        <!-- KPI Cards -->
        <v-row v-if="stats" class="mb-6">
          <v-col cols="6" md="3">
            <v-card color="primary" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-train" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.routesThisMonth }}</div>
              <div class="text-caption">Trajets ce mois</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="success" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-map-marker-distance" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.distanceThisMonth }}</div>
              <div class="text-caption">km ce mois</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="info" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-history" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.totalRoutes }}</div>
              <div class="text-caption">Trajets total</div>
            </v-card>
          </v-col>
          <v-col cols="6" md="3">
            <v-card color="warning" variant="tonal" class="pa-4 text-center">
              <v-icon icon="mdi-road-variant" size="32" class="mb-2" />
              <div class="text-h4 font-weight-bold">{{ stats.totalDistance }}</div>
              <div class="text-caption">km total</div>
            </v-card>
          </v-col>
        </v-row>

        <!-- Distribution Card -->
        <v-card v-if="stats && stats.distribution.length > 0" class="mb-6 pa-4">
          <v-card-title class="text-h6">
            <v-icon icon="mdi-chart-pie" class="mr-2" />
            Répartition {{ formatMonth(stats.month) }}
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
                  <div class="text-overline">{{ codeLabels[item.code] || item.code }}</div>
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
              <v-card-title class="text-h6">Nouveau Trajet</v-card-title>
              <v-card-text>
                Calculer un trajet entre deux stations
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
              <v-card-title class="text-h6">Mes Trajets</v-card-title>
              <v-card-text>
                Consulter l'historique des trajets
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
              <v-card-title class="text-h6">Statistiques</v-card-title>
              <v-card-text>
                Statistiques par code analytique
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
  </v-container>
</template>
