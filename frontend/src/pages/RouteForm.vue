<template>
  <v-container class="mt-8">
    <v-card class="mx-auto" max-width="600">
      <v-card-title>
        Calculate Route
        <v-chip class="ml-2" :color="stations.length ? 'green' : 'orange'">
          {{ stations.length }} stations
        </v-chip>
      </v-card-title>
      <v-card-text>
        <v-form @submit.prevent="submitForm">
          <v-select
            v-model="form.fromStationId"
            :items="stationOptions"
            item-title="title"
            item-value="value"
            label="From Station"
            required
            class="mb-4"
          />

          <v-select
            v-model="form.toStationId"
            :items="stationOptions"
            item-title="title"
            item-value="value"
            label="To Station"
            required
            class="mb-4"
          />

          <v-text-field
            v-model="form.analyticCode"
            label="Analytic Code"
            required
            class="mb-4"
          />

          <v-btn type="submit" color="primary" :loading="loading" block>
            Calculate Route
          </v-btn>
        </v-form>

        <v-alert v-if="error" type="error" class="mt-4" dismissible>
          {{ error }}
        </v-alert>

        <v-card v-if="result" class="mt-6" variant="outlined" color="green">
          <v-card-title>{{ result.distanceKm.toFixed(2) }} km</v-card-title>
          <v-card-text>{{ result.path.join(' → ') }}</v-card-text>
        </v-card>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { createRoute, Route, getStations } from '../services/api'

interface FormData {
  fromStationId: string
  toStationId: string
  analyticCode: string
}

const form = ref<FormData>({
  fromStationId: '',
  toStationId: '',
  analyticCode: 'web',
})

const loading = ref(false)
const error = ref('')
const result = ref<Route | null>(null)
const stations = ref<
  Array<{ id: string; shortName: string; longName: string }>
>([])

const stationOptions = computed(() =>
  stations.value.map((s) => ({
    title: `${s.shortName} - ${s.longName}`,
    value: s.shortName,
  }))
)

onMounted(async () => {
  try {
    stations.value = await getStations()
  } catch (err) {
    console.error('Failed to load stations:', err)
    error.value = 'Stations not available - check /api/v1/stations endpoint'
  }
})

const submitForm = async () => {
  loading.value = true
  error.value = ''
  result.value = null

  try {
    result.value = await createRoute(form.value)
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string } } }
    error.value =
      axiosError.response?.data?.message || 'Failed to calculate route'
  } finally {
    loading.value = false
  }
}
</script>
