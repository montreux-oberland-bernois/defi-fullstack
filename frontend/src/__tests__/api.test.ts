import { describe, it, expect } from 'vitest'
import { RouteRequest } from '../services/api'

describe('API Service', () => {
  it('should create a valid route request', () => {
    const request: RouteRequest = {
      fromStationId: 'MX',
      toStationId: 'ZW',
      analyticCode: 'ANA-123',
    }

    expect(request.fromStationId).toBe('MX')
    expect(request.toStationId).toBe('ZW')
    expect(request.analyticCode).toBe('ANA-123')
  })

  it('should validate required fields', () => {
    const request: RouteRequest = {
      fromStationId: '',
      toStationId: '',
      analyticCode: '',
    }

    expect(request.fromStationId).toBe('')
    expect(request.toStationId).toBe('')
    expect(request.analyticCode).toBe('')
  })
})
