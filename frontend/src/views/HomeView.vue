<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const userName = computed(() => authStore.user?.name ?? 'Utilisateur')
</script>

<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8">
        <v-card class="pa-6">
          <v-card-title class="text-h4 text-center mb-4">
            <v-icon icon="mdi-train" size="48" color="primary" class="mr-3" />
            Bienvenue, {{ userName }}!
          </v-card-title>

          <v-card-text class="text-center text-body-1">
            <p class="mb-4">
              Cette application vous permet de calculer des trajets ferroviaires
              et de consulter des statistiques par code analytique.
            </p>
          </v-card-text>

          <v-divider class="my-4" />

          <v-row justify="center" class="mt-4">
            <v-col cols="12" sm="6" md="4">
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

            <v-col cols="12" sm="6" md="4">
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
                  Consulter l'historique des trajets calculés
                </v-card-text>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6" md="4">
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
                  Visualiser les statistiques par code analytique
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
