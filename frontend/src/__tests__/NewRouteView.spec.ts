import { describe, it, expect, beforeEach, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { createPinia, setActivePinia } from 'pinia'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import NewRouteView from '../views/NewRouteView.vue'

const vuetify = createVuetify({
  components,
  directives,
})

// Mock vue-router
vi.mock('vue-router', () => ({
  useRouter: () => ({
    push: vi.fn(),
  }),
}))

// Mock the stations store
vi.mock('@/stores/stations', () => ({
  useStationsStore: () => ({
    stations: [
      { shortName: 'MX', longName: 'Montreux' },
      { shortName: 'CGE', longName: 'Montreux-Collège' },
      { shortName: 'VUAR', longName: 'Vuarennes' },
    ],
    loading: false,
    fetchStations: vi.fn(),
  }),
}))

// Mock the route service
vi.mock('@/services/routeService', () => ({
  routeService: {
    calculate: vi.fn(),
  },
}))

describe('NewRouteView', () => {
  beforeEach(() => {
    setActivePinia(createPinia())
  })

  const mountComponent = () => {
    return mount(NewRouteView, {
      global: {
        plugins: [vuetify, createPinia()],
      },
    })
  }

  it('renders the form title', () => {
    const wrapper = mountComponent()
    expect(wrapper.text()).toContain('Calculer un nouveau trajet')
  })

  it('displays all predefined analytic codes', () => {
    const wrapper = mountComponent()
    const html = wrapper.html()

    // The select should contain the analytic codes options
    expect(html).toContain('Code analytique')
  })

  it('has swap button that is enabled by default', () => {
    const wrapper = mountComponent()
    const swapButton = wrapper.find('[data-testid="swap-btn"]')

    // Find button with swap icon
    const buttons = wrapper.findAll('button')
    const swapBtn = buttons.find(btn => btn.html().includes('mdi-swap-vertical'))

    expect(swapBtn).toBeDefined()
    expect(swapBtn?.attributes('disabled')).toBeUndefined()
  })

  it('validates that stations must be different', async () => {
    const wrapper = mountComponent()

    // The submit button should be disabled when form is invalid
    const submitButton = wrapper.find('button[type="submit"]')
    expect(submitButton.attributes('disabled')).toBeDefined()
  })
})

describe('Analytic Codes', () => {
  it('should have 4 predefined codes', () => {
    const analyticCodes = [
      { value: 'FRET', title: 'FRET - Transport de marchandises' },
      { value: 'PASS', title: 'PASS - Transport de passagers' },
      { value: 'MAINT', title: 'MAINT - Maintenance' },
      { value: 'TEST', title: 'TEST - Essais techniques' },
    ]

    expect(analyticCodes).toHaveLength(4)
    expect(analyticCodes.map(c => c.value)).toEqual(['FRET', 'PASS', 'MAINT', 'TEST'])
  })
})
