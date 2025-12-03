import { test, expect } from '@playwright/test'
import { loginTestUser } from './helpers/auth'

test.describe('Routes page', () => {
  test.beforeEach(async ({ page }) => {
    await loginTestUser(page)
  })

  test('should display route form', async ({ page }) => {
    await page.goto('/routes')
    await page.waitForLoadState('networkidle')
    await expect(page).toHaveURL('/routes')

    await expect(
      page.locator('.v-card-title', { hasText: /calculate route/i })
    ).toBeVisible()
    await expect(
      page.locator('.v-select').filter({ hasText: 'From Station' })
    ).toBeVisible()
    await expect(
      page.locator('.v-select').filter({ hasText: 'To Station' })
    ).toBeVisible()
  })

  test('should calculate route between two stations', async ({ page }) => {
    await page.goto('/routes')
    await page.waitForLoadState('networkidle')

    // Select from station (Montreux)
    await page.locator('.v-select').filter({ hasText: 'From Station' }).click()
    await page.keyboard.type('MX')
    await page.keyboard.press('Enter')
    await page.keyboard.press('Escape') // Close dropdown

    // Select to station (Blonay)
    await page.locator('.v-select').filter({ hasText: 'To Station' }).click()
    await page.keyboard.type('BLON')
    await page.keyboard.press('Enter')
    await page.keyboard.press('Escape') // Close dropdown
    await page.waitForTimeout(200) // Wait for Vuetify overlay to fully close

    // Click calculate
    await page.getByRole('button', { name: /calculate/i }).click()

    // Wait for result - should show distance in km format
    await expect(page.getByText(/km$/i)).toBeVisible({ timeout: 10000 })
  })

  test('should handle same station selection', async ({ page }) => {
    await page.goto('/routes')
    await page.waitForLoadState('networkidle')

    // Select same station
    await page.locator('.v-select').filter({ hasText: 'From Station' }).click()
    await page.keyboard.type('MX')
    await page.keyboard.press('Enter')
    await page.keyboard.press('Escape') // Close dropdown

    await page.locator('.v-select').filter({ hasText: 'To Station' }).click()
    await page.keyboard.type('MX')
    await page.keyboard.press('Enter')
    await page.keyboard.press('Escape') // Close dropdown
    await page.waitForTimeout(200) // Wait for Vuetify overlay to fully close

    await page.getByRole('button', { name: /calculate/i }).click()

    // Wait for result - same station should return 0.00 km
    await expect(page.locator('.v-card.mt-6')).toBeVisible({ timeout: 5000 })
  })
})
