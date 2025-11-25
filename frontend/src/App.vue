<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'
import { setLocale } from '@/plugins/i18n'

const { t, locale } = useI18n()
const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const userName = computed(() => authStore.user?.name ?? '')

const handleLogout = async () => {
  await authStore.logout()
  router.push('/login')
}

const toggleLocale = () => {
  const newLocale = locale.value === 'fr' ? 'en' : 'fr'
  setLocale(newLocale)
}
</script>

<template>
  <v-app>
    <v-app-bar color="primary" elevation="2">
      <v-app-bar-title>
        <v-icon icon="mdi-train" class="mr-2" />
        {{ t('app.title') }}
      </v-app-bar-title>

      <template v-if="isAuthenticated">
        <v-btn to="/" variant="text">
          <v-icon icon="mdi-home" class="mr-1" />
          {{ t('nav.home') }}
        </v-btn>
        <v-btn to="/routes" variant="text">
          <v-icon icon="mdi-map-marker-path" class="mr-1" />
          {{ t('nav.routes') }}
        </v-btn>
        <v-btn to="/stats" variant="text">
          <v-icon icon="mdi-chart-bar" class="mr-1" />
          {{ t('nav.stats') }}
        </v-btn>

        <v-spacer />

        <!-- Language toggle -->
        <v-btn icon variant="text" @click="toggleLocale">
          <v-icon>mdi-translate</v-icon>
          <v-tooltip activator="parent" location="bottom">
            {{ locale === 'fr' ? 'English' : 'Français' }}
          </v-tooltip>
        </v-btn>

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
              <v-list-item-title>{{ t('nav.logout') }}</v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>

      <template v-else>
        <v-spacer />
        <!-- Language toggle -->
        <v-btn icon variant="text" @click="toggleLocale">
          <v-icon>mdi-translate</v-icon>
          <v-tooltip activator="parent" location="bottom">
            {{ locale === 'fr' ? 'English' : 'Français' }}
          </v-tooltip>
        </v-btn>
        <v-btn to="/login" variant="text">
          <v-icon icon="mdi-login" class="mr-1" />
          {{ t('nav.login') }}
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
            {{ t('app.footer') }} &copy; {{ new Date().getFullYear() }}
          </span>
        </v-col>
      </v-row>
    </v-footer>
  </v-app>
</template>
