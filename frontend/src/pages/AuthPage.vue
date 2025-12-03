<template>
  <v-container class="mt-8">
    <v-card class="mx-auto" max-width="480">
      <v-card-title>User registration & login (demo)</v-card-title>
      <v-card-text>
        <v-form @submit.prevent="onLogin">
          <v-text-field v-model="username" label="Username" required />
          <v-text-field
            v-model="password"
            label="Password"
            type="password"
            required
          />

          <v-row class="mt-4">
            <v-col cols="6">
              <v-btn
                color="primary"
                block
                :loading="loading"
                @click="onRegister"
              >
                Register
              </v-btn>
            </v-col>
            <v-col cols="6">
              <v-btn color="secondary" block type="submit" :loading="loading">
                Login
              </v-btn>
            </v-col>
          </v-row>
        </v-form>

        <v-alert v-if="message" :type="messageType" class="mt-4">
          {{ message }}
        </v-alert>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const username = ref('')
const password = ref('')
const loading = ref(false)
const message = ref('')
const messageType = ref<'error' | 'info' | 'success' | 'warning'>('info')

const router = useRouter()

const onRegister = async () => {
  loading.value = true
  message.value = ''
  try {
    const res = await axios.post('/api/v1/auth/register', {
      username: username.value,
      password: password.value,
    })
    if (res.status === 201) {
      messageType.value = 'success'
      message.value = 'Registered successfully — now you can login.'
    }
  } catch (err: unknown) {
    messageType.value = 'error'
    const axiosError = err as { response?: { data?: { message?: string } } }
    message.value = axiosError.response?.data?.message || 'Registration failed'
  } finally {
    loading.value = false
  }
}

const onLogin = async () => {
  loading.value = true
  message.value = ''
  try {
    const res = await axios.post('/api/v1/auth/login', {
      username: username.value,
      password: password.value,
    })
    const token = res.data?.token
    if (token) {
      localStorage.setItem('token', token)
      // notify other tabs/components that token changed
      window.dispatchEvent(new Event('token-changed'))
      messageType.value = 'success'
      message.value = 'Login successful — token stored in localStorage.'
      // navigate to the protected route form
      router.push('/routes')
    } else {
      messageType.value = 'error'
      message.value = 'Login did not return a token.'
    }
  } catch (err: unknown) {
    messageType.value = 'error'
    const axiosError = err as { response?: { data?: { message?: string } } }
    message.value = axiosError.response?.data?.message || 'Login failed'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped></style>
