<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const showPassword = ref(false)

const loading = computed(() => authStore.loading)
const error = computed(() => authStore.error)

const isValid = computed(() => {
  return email.value.length > 0 && password.value.length > 0
})

const handleSubmit = async () => {
  if (!isValid.value) return

  const success = await authStore.login({
    email: email.value,
    password: password.value,
  })

  if (success) {
    router.push('/')
  }
}
</script>

<template>
  <v-container>
    <v-row justify="center" align="center" class="fill-height">
      <v-col cols="12" sm="8" md="6" lg="4">
        <v-card class="pa-6">
          <v-card-title class="text-h4 text-center mb-4">
            <v-icon icon="mdi-train" size="48" color="primary" class="mr-2" />
            {{ t('auth.login') }}
          </v-card-title>

          <v-alert
            v-if="error"
            type="error"
            variant="tonal"
            class="mb-4"
            closable
          >
            {{ error }}
          </v-alert>

          <v-form @submit.prevent="handleSubmit">
            <v-text-field
              v-model="email"
              :label="t('auth.email')"
              type="email"
              prepend-inner-icon="mdi-email"
              required
              autocomplete="email"
            />

            <v-text-field
              v-model="password"
              :label="t('auth.password')"
              :type="showPassword ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock"
              :append-inner-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
              @click:append-inner="showPassword = !showPassword"
              required
              autocomplete="current-password"
            />

            <v-btn
              type="submit"
              color="primary"
              block
              size="large"
              :loading="loading"
              :disabled="!isValid"
              class="mt-4"
            >
              {{ t('auth.loginButton') }}
            </v-btn>
          </v-form>

          <v-divider class="my-4" />

          <div class="text-center">
            <span class="text-body-2">{{ t('auth.noAccount') }}</span>
            <router-link to="/register" class="ml-1">{{ t('auth.registerButton') }}</router-link>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
