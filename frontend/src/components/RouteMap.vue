<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import 'leaflet/dist/leaflet.css'
import L from 'leaflet'

const props = defineProps<{
  path: string[]
}>()

const mapContainer = ref<HTMLElement | null>(null)
let map: L.Map | null = null

// Coordonnées approximatives des stations MOB (Montreux-Oberland-Bernois)
const stationCoords: Record<string, [number, number]> = {
  'MX': [46.4312, 6.9107],      // Montreux
  'CGE': [46.4350, 6.9150],     // Montreux-Collège
  'VUAR': [46.4400, 6.9200],    // Vuarennes
  'CAUX': [46.4350, 6.9350],    // Caux
  'AVA': [46.4450, 6.9500],     // Les Avants
  'SOMB': [46.4550, 6.9600],    // Sonzier
  'CABY': [46.4600, 6.9700],    // Chamby
  'JMAN': [46.4650, 6.9800],    // Jaman
  'ALLI': [46.4700, 6.9900],    // Allières
  'MONT': [46.4800, 7.0000],    // Montbovon
  'ROSE': [46.5000, 7.0500],    // Rossinière
  'CHDO': [46.5200, 7.1000],    // Château-d'Oex
  'ROGE': [46.5400, 7.1500],    // Rougemont
  'SAAS': [46.5600, 7.2000],    // Saanen
  'GSTA': [46.4750, 7.2870],    // Gstaad
  'SCHZ': [46.5000, 7.3500],    // Schönried
  'SAAG': [46.5200, 7.4000],    // Saanenmöser
  'ZW': [46.5567, 7.3733],      // Zweisimmen
  'BLON': [46.4600, 6.9000],    // Blonay
  'PLCH': [46.4700, 6.8900],    // Planchamp
  'CHBR': [46.4850, 6.8800],    // Chexbres
  'VEVX': [46.4600, 6.8400],    // Vevey
}

// Générer des coordonnées interpolées pour les stations non mappées
const getStationCoord = (station: string, index: number, total: number): [number, number] => {
  if (stationCoords[station]) {
    return stationCoords[station]
  }
  // Interpolation entre Montreux et Zweisimmen pour les stations inconnues
  const startLat = 46.4312
  const startLng = 6.9107
  const endLat = 46.5567
  const endLng = 7.3733
  const ratio = total > 1 ? index / (total - 1) : 0
  return [
    startLat + (endLat - startLat) * ratio + (Math.random() - 0.5) * 0.01,
    startLng + (endLng - startLng) * ratio + (Math.random() - 0.5) * 0.01
  ]
}

const initMap = () => {
  if (!mapContainer.value || map) return

  map = L.map(mapContainer.value).setView([46.48, 7.1], 10)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map)

  updateRoute()
}

const updateRoute = () => {
  if (!map || props.path.length === 0) return

  // Clear existing layers
  map.eachLayer((layer) => {
    if (layer instanceof L.Marker || layer instanceof L.Polyline) {
      map!.removeLayer(layer)
    }
  })

  const coords = props.path.map((station, index) =>
    getStationCoord(station, index, props.path.length)
  )

  // Add markers
  coords.forEach((coord, index) => {
    const isFirst = index === 0
    const isLast = index === coords.length - 1

    const icon = L.divIcon({
      className: 'custom-marker',
      html: `<div class="marker-pin ${isFirst ? 'start' : isLast ? 'end' : 'middle'}">
        <span>${index + 1}</span>
      </div>`,
      iconSize: [30, 30],
      iconAnchor: [15, 30]
    })

    L.marker(coord, { icon })
      .addTo(map!)
      .bindPopup(`<strong>${props.path[index]}</strong><br>Station ${index + 1}/${props.path.length}`)
  })

  // Draw polyline
  L.polyline(coords, {
    color: '#1976d2',
    weight: 4,
    opacity: 0.8
  }).addTo(map)

  // Fit bounds
  if (coords.length > 0) {
    map.fitBounds(coords as L.LatLngBoundsExpression, { padding: [30, 30] })
  }
}

watch(() => props.path, () => {
  if (map) {
    updateRoute()
  }
}, { deep: true })

onMounted(() => {
  setTimeout(initMap, 100)
})
</script>

<template>
  <div ref="mapContainer" class="route-map"></div>
</template>

<style>
.route-map {
  height: 250px;
  width: 100%;
  border-radius: 8px;
  z-index: 0;
}

.custom-marker .marker-pin {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: bold;
  color: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.custom-marker .marker-pin.start {
  background: #4caf50;
}

.custom-marker .marker-pin.end {
  background: #f44336;
}

.custom-marker .marker-pin.middle {
  background: #1976d2;
}
</style>
