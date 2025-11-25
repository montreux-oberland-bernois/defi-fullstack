import { describe, it, expect, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import App from '../App.vue'

const vuetify = createVuetify({
  components,
  directives,
})

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', component: { template: '<div>Home</div>' } },
    { path: '/login', component: { template: '<div>Login</div>' } },
  ],
})

describe('App', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  it('renders the app bar with title', async () => {
    const wrapper = mount(App, {
      global: {
        plugins: [vuetify, router, createPinia()],
        stubs: {
          'router-view': true,
        },
      },
    })

    await router.isReady()

    expect(wrapper.text()).toContain('Train Routing')
  })

  it('shows login button when not authenticated', async () => {
    const wrapper = mount(App, {
      global: {
        plugins: [vuetify, router, createPinia()],
        stubs: {
          'router-view': true,
        },
      },
    })

    await router.isReady()

    expect(wrapper.text()).toContain('Connexion')
  })
})
