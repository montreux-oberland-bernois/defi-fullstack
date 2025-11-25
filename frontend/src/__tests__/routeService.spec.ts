import { describe, it, expect } from 'vitest'
import type { RouteFilters } from '@/services/routeService'

describe('RouteFilters', () => {
  it('should allow filtering by analytic code', () => {
    const filters: RouteFilters = {
      analyticCode: 'FRET',
    }

    expect(filters.analyticCode).toBe('FRET')
    expect(filters.from).toBeUndefined()
    expect(filters.to).toBeUndefined()
  })

  it('should allow filtering by date range', () => {
    const filters: RouteFilters = {
      from: '2024-01-01',
      to: '2024-12-31',
    }

    expect(filters.from).toBe('2024-01-01')
    expect(filters.to).toBe('2024-12-31')
    expect(filters.analyticCode).toBeUndefined()
  })

  it('should allow combining all filters', () => {
    const filters: RouteFilters = {
      analyticCode: 'PASS',
      from: '2024-06-01',
      to: '2024-06-30',
    }

    expect(filters.analyticCode).toBe('PASS')
    expect(filters.from).toBe('2024-06-01')
    expect(filters.to).toBe('2024-06-30')
  })

  it('should accept all valid analytic codes', () => {
    const validCodes = ['FRET', 'PASS', 'MAINT', 'TEST']

    validCodes.forEach(code => {
      const filters: RouteFilters = { analyticCode: code }
      expect(filters.analyticCode).toBe(code)
    })
  })
})
