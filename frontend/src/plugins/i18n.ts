import { createI18n } from 'vue-i18n'
import fr from '@/locales/fr.json'
import en from '@/locales/en.json'

export type MessageSchema = typeof fr

// Detect browser language
const getBrowserLocale = (): string => {
  const navigatorLocale = navigator.language || 'fr'
  const locale = navigatorLocale.split('-')[0]
  return ['fr', 'en'].includes(locale) ? locale : 'fr'
}

// Get saved locale from localStorage or browser
const getSavedLocale = (): string => {
  return localStorage.getItem('locale') || getBrowserLocale()
}

const i18n = createI18n<[MessageSchema], 'fr' | 'en'>({
  legacy: false,
  locale: getSavedLocale(),
  fallbackLocale: 'fr',
  messages: {
    fr,
    en,
  },
})

// Helper to change locale and persist
export const setLocale = (locale: 'fr' | 'en') => {
  i18n.global.locale.value = locale
  localStorage.setItem('locale', locale)
  document.querySelector('html')?.setAttribute('lang', locale)
}

export default i18n
