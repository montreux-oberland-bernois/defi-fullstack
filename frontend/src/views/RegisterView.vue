<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'

const { t } = useI18n()
const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const showPassword = ref(false)

const loading = computed(() => authStore.loading)
const error = computed(() => authStore.error)

const isValid = computed(() => {
  return (
    name.value.length >= 2 &&
    email.value.length > 0 &&
    password.value.length >= 8 &&
    password.value === passwordConfirmation.value
  )
})

const passwordMatch = computed(() => {
  if (!passwordConfirmation.value) return true
  return password.value === passwordConfirmation.value
})

const handleSubmit = async () => {
  if (!isValid.value) return

  const success = await authStore.register({
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
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
            <v-icon icon="mdi-account-plus" size="48" color="primary" class="mr-2" />
            {{ t('auth.register') }}
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
              v-model="name"
              :label="t('auth.name')"
              prepend-inner-icon="mdi-account"
              required
              autocomplete="name"
              :rules="[(v: string) => v.length >= 2 || t('auth.minChars', { count: 2 })]"
            />

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
              autocomplete="new-password"
              :rules="[(v: string) => v.length >= 8 || t('auth.minChars', { count: 8 })]"
            />

            <v-text-field
              v-model="passwordConfirmation"
              :label="t('auth.passwordConfirm')"
              :type="showPassword ? 'text' : 'password'"
              prepend-inner-icon="mdi-lock-check"
              required
              autocomplete="new-password"
              :error="!passwordMatch"
              :error-messages="!passwordMatch ? [t('auth.passwordMismatch')] : []"
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
              {{ t('auth.registerButton') }}
            </v-btn>
          </v-form>

          <v-divider class="my-4" />

          <div class="text-center">
            <span class="text-body-2">{{ t('auth.hasAccount') }}</span>
            <router-link to="/login" class="ml-1">{{ t('auth.loginButton') }}</router-link>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>
