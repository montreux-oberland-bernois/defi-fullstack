<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const userName = computed(() => authStore.user?.name ?? '')

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <v-app>
    <v-app-bar color="primary" elevation="2">
      <v-app-bar-title>
        <v-icon icon="mdi-train" class="mr-2" />
        Train Routing
      </v-app-bar-title>

      <template v-if="isAuthenticated">
        <v-btn to="/" variant="text">
          <v-icon icon="mdi-home" class="mr-1" />
          Accueil
        </v-btn>
        <v-btn to="/routes" variant="text">
          <v-icon icon="mdi-map-marker-path" class="mr-1" />
          Trajets
        </v-btn>
        <v-btn to="/stats" variant="text">
          <v-icon icon="mdi-chart-bar" class="mr-1" />
          Statistiques
        </v-btn>

        <v-spacer />

        <v-menu>
          <template #activator="{ props }">
            <v-btn v-bind="props" variant="text">
              <v-icon icon="mdi-account" class="mr-1" />
              {{ userName }}
              <v-icon icon="mdi-chevron-down" />
            </v-btn>
          </template>
          <v-list>
            <v-list-item @click="handleLogout">
              <template #prepend>
                <v-icon icon="mdi-logout" />
              </template>
              <v-list-item-title>Déconnexion</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>

      <template v-else>
        <v-spacer />
        <v-btn to="/login" variant="text">
          <v-icon icon="mdi-login" class="mr-1" />
          Connexion
        </v-btn>
      </template>
    </v-app-bar>

    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>

    <v-footer app color="grey-lighten-4">
      <v-row justify="center" no-gutters>
        <v-col class="text-center" cols="12">
          <span class="text-body-2 text-grey">
            Défi Full Stack - Train Routing &copy; {{ new Date().getFullYear() }}
          </span>
        </v-col>
      </v-row>
    </v-footer>
  </v-app>
</template>
