<template>
  <v-container class="mt-8">
    <v-card class="mx-auto">
      <v-card-title>Analytics Dashboard</v-card-title>
      <v-card-text>
        <v-row class="mb-4">
          <v-col cols="12" md="3">
            <v-text-field
              v-model="filters.from"
              type="date"
              label="From Date"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-text-field v-model="filters.to" type="date" label="To Date" />
          </v-col>
          <v-col cols="12" md="3">
            <v-select
              v-model="filters.groupBy"
              :items="['none', 'day', 'month', 'year']"
              label="Group By"
            />
          </v-col>
          <v-col cols="12" md="3">
            <v-btn
              color="primary"
              :loading="loading"
              block
              @click="loadAnalytics"
            >
              Load Data
            </v-btn>
          </v-col>
        </v-row>

        <v-alert v-if="error" type="error" dismissible>
          {{ error }}
        </v-alert>

        <v-data-table
          v-if="analytics.items.length > 0"
          :headers="headers"
          :items="analytics.items"
          class="elevation-1"
        >
          <template #no-data>
            <p class="text-center mt-4">No data available</p>
          </template>
        </v-data-table>

        <v-empty-state
          v-else
          headline="No Data"
          text="Load analytics data by clicking the Load Data button"
        />
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { getAnalytics, AnalyticsResponse } from '../services/api'

interface Filters {
  from?: string
  to?: string
  groupBy: string
}

const filters = ref<Filters>({
  groupBy: 'none',
})

const loading = ref(false)
const error = ref('')
const analytics = ref<AnalyticsResponse>({
  groupBy: 'none',
  items: [],
})

const headers = [
  { title: 'Analytic Code', key: 'analyticCode' },
  { title: 'Total Distance (km)', key: 'totalDistanceKm' },
  { title: 'Group', key: 'group' },
]

const loadAnalytics = async () => {
  loading.value = true
  error.value = ''

  try {
    analytics.value = await getAnalytics(
      filters.value.from,
      filters.value.to,
      filters.value.groupBy
    )
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string } } }
    error.value =
      axiosError.response?.data?.message || 'Failed to load analytics'
  } finally {
    loading.value = false
  }
}
</script>
