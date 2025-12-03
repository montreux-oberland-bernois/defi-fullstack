<template>
  <v-container class="mt-8">
    <v-card class="mx-auto" max-width="1200">
      <v-card-title>API Docs (OpenAPI)</v-card-title>
      <v-card-text>
        <div id="redoc-container" />
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'

onMounted(() => {
  const script = document.createElement('script')
  script.src = 'https://cdn.redoc.ly/redoc/latest/bundles/redoc.standalone.js'
  script.onload = () => {
    const w = window as typeof window & {
      Redoc?: {
        init: (url: string, options: object, el: HTMLElement | null) => void
      }
    }
    if (typeof w.Redoc !== 'undefined') {
      w.Redoc.init(
        '/api/v1/doc.json',
        {},
        document.getElementById('redoc-container')
      )
    }
  }
  document.head.appendChild(script)
})
</script>

<style>
#redoc-container {
  height: 100%;
  min-height: 70vh;
}
</style>
