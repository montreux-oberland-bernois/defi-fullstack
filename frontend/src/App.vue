<template>
  <v-app>
    <v-app-bar color="primary">
      <v-app-bar-title>Train Routing & Analytics</v-app-bar-title>
      <v-spacer />
      <v-btn to="/routes" variant="text"> Routes </v-btn>
      <v-btn to="/analytics" variant="text"> Analytics </v-btn>
      <v-btn to="/docs" variant="text"> API Docs </v-btn>
      <template v-if="isLoggedIn">
        <v-btn color="error" variant="text" @click="logout"> Logout </v-btn>
      </template>
      <template v-else>
        <v-btn to="/auth" variant="text"> Login / Register </v-btn>
      </template>
    </v-app-bar>

    <v-main>
      <router-view />
    </v-main>
  </v-app>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const isLoggedIn = ref(!!localStorage.getItem('token'))
const router = useRouter()

function tokenChanged() {
  isLoggedIn.value = !!localStorage.getItem('token')
}

function logout() {
  localStorage.removeItem('token')
  window.dispatchEvent(new Event('token-changed'))
  router.push('/auth')
}

onMounted(() => window.addEventListener('token-changed', tokenChanged))
onUnmounted(() => window.removeEventListener('token-changed', tokenChanged))
</script>

<style scoped></style>
