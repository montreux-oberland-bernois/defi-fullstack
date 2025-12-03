import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import 'vuetify/styles'
import App from './App.vue'
import RouteForm from './pages/RouteForm.vue'
import Analytics from './pages/AnalyticsPage.vue'
import Docs from './pages/DocsPage.vue'
import Auth from './pages/AuthPage.vue'

const routes = [
  { path: '/', redirect: '/auth' },
  { path: '/auth', component: Auth },
  { path: '/routes', component: RouteForm },
  { path: '/analytics', component: Analytics },
  { path: '/docs', component: Docs },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

const vuetify = createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: 'light',
  },
})

const app = createApp(App)
app.use(router)
app.use(vuetify)
app.mount('#app')
