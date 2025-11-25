<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useStationsStore } from '@/stores/stations'
import { routeService } from '@/services/routeService'
import RouteMap from '@/components/RouteMap.vue'
import type { Route, Station } from '@/types'

const { t } = useI18n()
const router = useRouter()
const stationsStore = useStationsStore()

const fromStationId = ref('')
const toStationId = ref('')
const analyticCode = ref('')
const loading = ref(false)
const error = ref<string | null>(null)
const calculatedRoute = ref<Route | null>(null)

const analyticCodes = computed(() => [
  { value: 'FRET', title: t('analyticCodes.FRET'), icon: 'mdi-truck-delivery' },
  { value: 'PASS', title: t('analyticCodes.PASS'), icon: 'mdi-account-group' },
  { value: 'MAINT', title: t('analyticCodes.MAINT'), icon: 'mdi-wrench' },
  { value: 'TEST', title: t('analyticCodes.TEST'), icon: 'mdi-flask' },
])

const stations = computed(() => stationsStore.stations)
const stationsLoading = computed(() => stationsStore.loading)

const isValid = computed(() => {
  return (
    fromStationId.value.length > 0 &&
    toStationId.value.length > 0 &&
    analyticCode.value.length > 0 &&
    fromStationId.value !== toStationId.value
  )
})

const fromStationName = computed(() => {
  const station = stations.value.find((s: Station) => s.shortName === fromStationId.value)
  return station?.longName ?? ''
})

const toStationName = computed(() => {
  const station = stations.value.find((s: Station) => s.shortName === toStationId.value)
  return station?.longName ?? ''
})

onMounted(async () => {
  await stationsStore.fetchStations()
})

const handleSubmit = async () => {
  if (!isValid.value) return

  loading.value = true
  error.value = null
  calculatedRoute.value = null

  try {
    const route = await routeService.calculate({
      fromStationId: fromStationId.value,
      toStationId: toStationId.value,
      analyticCode: analyticCode.value,
    })
    calculatedRoute.value = route
  } catch (err: unknown) {
    const axiosError = err as { response?: { data?: { message?: string; details?: string[] } } }
    error.value = axiosError.response?.data?.details?.join(', ') ??
                 axiosError.response?.data?.message ??
                 t('errors.calculation')
  } finally {
    loading.value = false
  }
}

const resetForm = () => {
  fromStationId.value = ''
  toStationId.value = ''
  analyticCode.value = ''
  calculatedRoute.value = null
  error.value = null
}

const goToRoutes = () => {
  router.push('/routes')
}

const swapStations = () => {
  const temp = fromStationId.value
  fromStationId.value = toStationId.value
  toStationId.value = temp
}
</script>

<template>
  <v-container>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card class="pa-6">
          <v-card-title class="text-h5 mb-4">
            <v-icon icon="mdi-map-marker-path" class="mr-2" />
            {{ t('route.calculate') }}
          </v-card-title>

          <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mb-4"
            closable
            @click:close="error = null"
          >
            {{ error }}
          </v-alert>

          <v-form @submit.prevent="handleSubmit">
            <v-autocomplete
              v-model="fromStationId"
              :items="stations"
              item-title="longName"
              item-value="shortName"
              :label="t('route.fromStation')"
              prepend-inner-icon="mdi-map-marker"
              :loading="stationsLoading"
              clearable
              :disabled="!!calculatedRoute"
            >
              <template #item="{ props, item }">
                <v-list-item v-bind="props">
                  <template #subtitle>
                    {{ item.raw.shortName }}
                  </template>
                </v-list-item>
              </template>
            </v-autocomplete>

            <div class="d-flex justify-center my-n2">
              <v-btn
                icon
                variant="text"
                size="small"
                :disabled="!!calculatedRoute"
                @click="swapStations"
              >
                <v-icon icon="mdi-swap-vertical" />
                <v-tooltip activator="parent" location="end">
                  {{ t('route.swapStations') }}
                </v-tooltip>
              </v-btn>
            </div>

            <v-autocomplete
              v-model="toStationId"
              :items="stations"
              item-title="longName"
              item-value="shortName"
              :label="t('route.toStation')"
              prepend-inner-icon="mdi-map-marker-check"
              :loading="stationsLoading"
              clearable
              :disabled="!!calculatedRoute"
            >
              <template #item="{ props, item }">
                <v-list-item v-bind="props">
                  <template #subtitle>
                    {{ item.raw.shortName }}
                  </template>
                </v-list-item>
              </template>
            </v-autocomplete>

            <v-select
              v-model="analyticCode"
              :items="analyticCodes"
              item-title="title"
              item-value="value"
              :label="t('route.analyticCode')"
              prepend-inner-icon="mdi-tag"
              :disabled="!!calculatedRoute"
            >
              <template #item="{ props, item }">
                <v-list-item v-bind="props">
                  <template #prepend>
                    <v-icon :icon="item.raw.icon" />
                  </template>
                </v-list-item>
              </template>
            </v-select>

            <v-row class="mt-2">
              <v-col>
                <v-btn
                  v-if="!calculatedRoute"
                  type="submit"
                  color="primary"
                  block
                  size="large"
                  :loading="loading"
                  :disabled="!isValid"
                >
                  <v-icon icon="mdi-calculator" class="mr-2" />
                  {{ t('route.calculateButton') }}
                </v-btn>
                <v-btn
                  v-else
                  color="secondary"
                  block
                  size="large"
                  @click="resetForm"
                >
                  <v-icon icon="mdi-refresh" class="mr-2" />
                  {{ t('route.newCalculation') }}
                </v-btn>
              </v-col>
            </v-row>
          </v-form>

          <!-- Result -->
          <v-expand-transition>
            <v-card
              v-if="calculatedRoute"
              color="success"
              variant="tonal"
              class="mt-6 pa-4"
            >
              <v-card-title class="text-h6">
                <v-icon icon="mdi-check-circle" class="mr-2" />
                {{ t('route.success') }}
              </v-card-title>

              <v-card-text>
                <v-list density="compact" bg-color="transparent">
                  <v-list-item>
                    <template #prepend>
                      <v-icon icon="mdi-map-marker" />
                    </template>
                    <v-list-item-title>{{ t('route.departure') }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ fromStationName }} ({{ calculatedRoute.fromStationId }})
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template #prepend>
                      <v-icon icon="mdi-map-marker-check" />
                    </template>
                    <v-list-item-title>{{ t('route.arrival') }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ toStationName }} ({{ calculatedRoute.toStationId }})
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template #prepend>
                      <v-icon icon="mdi-map-marker-distance" />
                    </template>
                    <v-list-item-title>{{ t('route.distance') }}</v-list-item-title>
                    <v-list-item-subtitle>
                      <strong>{{ calculatedRoute.distanceKm }} km</strong>
                    </v-list-item-subtitle>
                  </v-list-item>

                  <v-list-item>
                    <template #prepend>
                      <v-icon icon="mdi-tag" />
                    </template>
                    <v-list-item-title>{{ t('route.analyticCode') }}</v-list-item-title>
                    <v-list-item-subtitle>
                      {{ calculatedRoute.analyticCode }}
                    </v-list-item-subtitle>
                  </v-list-item>
                </v-list>

                <v-divider class="my-3" />

                <!-- Map -->
                <div class="text-subtitle-2 mb-2">
                  <v-icon icon="mdi-map" size="small" class="mr-1" />
                  {{ t('route.map') }}
                </div>
                <RouteMap :path="calculatedRoute.path" class="mb-4" />

                <div class="text-subtitle-2 mb-2">
                  <v-icon icon="mdi-transit-connection-variant" size="small" class="mr-1" />
                  {{ t('route.itinerary', { count: calculatedRoute.path.length }) }}
                </div>
                <v-chip-group>
                  <v-chip
                    v-for="(station, index) in calculatedRoute.path"
                    :key="index"
                    size="small"
                    variant="outlined"
                  >
                    {{ station }}
                    <v-icon
                      v-if="index < calculatedRoute.path.length - 1"
                      icon="mdi-chevron-right"
                      size="small"
                      class="ml-1"
                    />
                  </v-chip>
                </v-chip-group>
              </v-card-text>

              <v-card-actions>
                <v-btn
                  color="primary"
                  variant="elevated"
                  @click="goToRoutes"
                >
                  <v-icon icon="mdi-format-list-bulleted" class="mr-2" />
                  {{ t('route.viewAll') }}
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-expand-transition>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
