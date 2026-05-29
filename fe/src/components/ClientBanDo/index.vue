<template>
  <div class="map-view-container">
    <!-- Left Sidebar for Search & List -->
    <div class="glass-sidebar shadow-lg">
      <div class="sidebar-header d-flex justify-content-between align-items-center mb-3">
        <div>
          <h4 class="sidebar-title mb-1">
            <i class="bx bx-map-alt text-orange"></i> Bản Đồ Số Dòng Tộc
          </h4>
          <p class="sidebar-subtitle mb-0">Định vị & Chỉ đường Mộ phần, Nhà thờ họ</p>
        </div>
        <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadMapData" :disabled="isLoading" title="Làm mới dữ liệu">
          <i class="bx bx-sync fs-5 text-orange" :class="{'bx-spin': isLoading}"></i>
        </button>
      </div>

      <!-- Quick Stats -->
      <div class="quick-stats-row mb-3">
        <div class="stat-card" @click="filterType = 'all'" :class="{ active: filterType === 'all' }">
          <span class="stat-num">{{ totalCount }}</span>
          <span class="stat-lbl">Tất cả</span>
        </div>
        <div class="stat-card grave-stat" @click="filterType = 'grave'" :class="{ active: filterType === 'grave' }">
          <span class="stat-num">{{ graves.length }}</span>
          <span class="stat-lbl">Mộ phần</span>
        </div>
        <div class="stat-card shrine-stat" @click="filterType = 'shrine'" :class="{ active: filterType === 'shrine' }">
          <span class="stat-num">{{ shrines.length }}</span>
          <span class="stat-lbl">Nhà thờ</span>
        </div>
      </div>

      <!-- Search Input -->
      <div class="search-box mb-3">
        <div class="input-group">
          <span class="input-group-text"><i class="bx bx-search"></i></span>
          <input 
            type="text" 
            class="form-control" 
            placeholder="Tìm kiếm vị trí..." 
            v-model="searchQuery"
          />
          <button v-if="searchQuery" class="btn btn-clear-search" @click="searchQuery = ''">
            <i class="bx bx-x"></i>
          </button>
        </div>
      </div>

      <!-- List of locations -->
      <div class="location-list flex-grow-1">
        <div v-if="isLoading" class="d-flex flex-column align-items-center justify-content-center py-5">
          <div class="spinner-border text-orange" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <span class="mt-3 text-muted">Đang tải bản đồ dòng tộc...</span>
        </div>

        <div v-else-if="filteredItems.length === 0" class="text-center py-5 text-muted">
          <i class="bx bx-map-pin fs-1 opacity-25 mb-2"></i>
          <p class="mb-0">Không tìm thấy vị trí nào khớp</p>
        </div>

        <div v-else class="list-wrapper">
          <div 
            v-for="item in filteredItems" 
            :key="item.uniqueId" 
            class="location-card"
            :class="{ 
              active: selectedItem && selectedItem.uniqueId === item.uniqueId,
              'no-coords': !item.hasCoords 
            }"
            @click="focusLocation(item)"
          >
            <div class="d-flex gap-3">
              <div class="card-icon-box" :class="item.type">
                <i :class="item.type === 'grave' ? 'bx bx-shield' : 'bx bx-home-alt-2'"></i>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                  <span class="badge-type" :class="item.type">
                    {{ item.type === 'grave' ? 'Mộ Cổ' : 'Nhà Thờ Tổ' }}
                  </span>
                  <span v-if="!item.hasCoords" class="badge bg-warning-subtle text-warning border-0 font-sm">
                    Chưa ghim
                  </span>
                </div>
                <h6 class="item-title mt-1 mb-1">{{ item.name }}</h6>
                <p class="item-desc text-muted mb-1 text-truncate-2">{{ item.address || 'Chưa cập nhật địa chỉ' }}</p>
                
                <div v-if="item.hasCoords" class="d-flex justify-content-between align-items-center mt-2">
                  <span class="coords-text">
                    <i class="bx bx-target-lock"></i> {{ item.lat.toFixed(5) }}, {{ item.lng.toFixed(5) }}
                  </span>
                  <button class="btn btn-sm btn-navigate-inline" @click.stop="startInAppRouting(item)">
                    <i class="bx bx-navigation"></i> Dẫn đường
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Back Button -->
      <div class="sidebar-footer border-top pt-3">
        <router-link to="/gia-pha" class="btn btn-back-gp w-100">
          <i class="bx bx-arrow-back"></i> Quay lại Gia Phả
        </router-link>
      </div>
    </div>

    <!-- Map Canvas -->
    <div id="leaflet-clan-map" class="map-canvas"></div>

    <!-- Floating Routing Stats Card -->
    <div v-if="routeStats" class="routing-stats-overlay shadow-lg animate-fade-in">
      <div class="d-flex align-items-center gap-3">
        <div class="route-icon-box">
          <i class="bx bx-navigation"></i>
        </div>
        <div class="flex-grow-1">
          <h6 class="stats-title mb-0">Thông tin dẫn đường</h6>
          <div class="stats-values d-flex gap-3 mt-1">
            <span class="stat-item"><i class="bx bx-transfer-alt"></i> {{ routeStats.distance }}</span>
            <span class="stat-item"><i class="bx bx-time"></i> {{ routeStats.duration }}</span>
          </div>
        </div>
        <button class="btn btn-close-route btn-sm ms-2" @click="clearRoute" title="Xóa đường đi">
          <i class="bx bx-x"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'ClientBanDo',
  data() {
    return {
      graves: [],
      shrines: [],
      searchQuery: '',
      filterType: 'all', // all, grave, shrine
      selectedItem: null,
      map: null,
      markers: {}, // Keyed by uniqueId
      isLoading: true,
      leafletLoaded: false,
      routingControl: null,
      routeStats: null
    }
  },
  computed: {
    totalCount() {
      return this.graves.length + this.shrines.length;
    },
    unifiedItems() {
      const all = [];
      this.graves.forEach(g => {
        all.push({
          uniqueId: `grave-${g.id}`,
          id: g.id,
          type: 'grave',
          name: g.ten_mo,
          address: g.dia_chi,
          lat: g.vi_do ? parseFloat(g.vi_do) : null,
          lng: g.kinh_do ? parseFloat(g.kinh_do) : null,
          hasCoords: !!(g.vi_do && g.kinh_do),
          photo: g.hinh_anh,
          notes: g.ghi_chu || 'Mộ cổ kính của dòng tộc.'
        });
      });
      this.shrines.forEach(s => {
        all.push({
          uniqueId: `shrine-${s.id}`,
          id: s.id,
          type: 'shrine',
          name: s.ten_nha_tho,
          address: s.dia_chi,
          lat: s.vi_do ? parseFloat(s.vi_do) : null,
          lng: s.kinh_do ? parseFloat(s.kinh_do) : null,
          hasCoords: !!(s.vi_do && s.kinh_do),
          photo: s.hinh_anh,
          notes: s.mo_ta || 'Nhà thờ họ tôn kính.'
        });
      });
      return all;
    },
    filteredItems() {
      return this.unifiedItems.filter(item => {
        const matchesType = this.filterType === 'all' || this.filterType === item.type;
        const matchesQuery = !this.searchQuery || 
          item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (item.address && item.address.toLowerCase().includes(this.searchQuery.toLowerCase()));
        return matchesType && matchesQuery;
      });
    }
  },
  mounted() {
    this.loadLeafletCDN(() => {
      this.leafletLoaded = true;
      this.initMap();
      this.loadMapData();
    });
    window.addEventListener('trigger-route', this.handleRouteTrigger);
  },
  beforeUnmount() {
    window.removeEventListener('trigger-route', this.handleRouteTrigger);
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadLeafletCDN(callback) {
      if (window.L && window.L.Routing) {
        callback();
        return;
      }
      
      const loadRouting = () => {
        const link2 = document.createElement('link');
        link2.rel = 'stylesheet';
        link2.href = 'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css';
        document.head.appendChild(link2);
        
        const script2 = document.createElement('script');
        script2.src = 'https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js';
        script2.onload = callback;
        document.head.appendChild(script2);
      };

      if (window.L) {
        loadRouting();
        return;
      }

      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);

      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      script.onload = loadRouting;
      document.head.appendChild(script);
    },
    initMap() {
      if (!this.leafletLoaded || this.map) return;

      // Default focal center is Vietnam center
      this.map = window.L.map('leaflet-clan-map', {
        zoomControl: false // Move zoom control to bottom right later
      }).setView([16.047079, 108.206230], 6);

      // Add elegant zoom control at bottom right
      window.L.control.zoom({ position: 'bottomright' }).addTo(this.map);

      // Configure OpenMap.vn layer with Fallback to OSM on load failure
      const openMapKey = 'DNmLXlnYjfHFnvaThBU1FXYrXAGhfpgD';
      const openMapUrl = `https://api.openmap.vn/styles/osm-bright/{z}/{x}/{y}.png?apikey=${openMapKey}`;
      const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

      const tileLayer = window.L.tileLayer(openMapUrl, {
        attribution: '&copy; <a href="https://openmap.vn" target="_blank">OpenMap.vn</a> contributors',
        maxZoom: 19
      });

      // Simple, beautiful error handler fallback
      tileLayer.on('tileerror', () => {
        console.warn("OpenMap.vn tile server loading error. Falling back to OpenStreetMap.");
        tileLayer.setUrl(osmUrl);
      });

      tileLayer.addTo(this.map);
    },
    loadMapData() {
      this.isLoading = true;
      const headers = this.getHeaders();
      
      const reqGraves = axios.get('http://127.0.0.1:8000/api/mo-phan/get-data', headers);
      const reqShrines = axios.get('http://127.0.0.1:8000/api/nha-tho-ho/get-data', headers);

      Promise.all([reqGraves, reqShrines])
        .then(([resG, resS]) => {
          if (resG.data.status) this.graves = resG.data.data;
          if (resS.data.status) this.shrines = resS.data.data;
          
          this.$nextTick(() => {
            this.plotMarkers();
          });
        })
        .catch(err => {
          console.error("Lỗi khi tải dữ liệu bản đồ:", err);
          toastr.error("Không thể tải thông tin định vị, vui lòng kiểm tra lại quyền đăng nhập.");
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    plotMarkers() {
      if (!this.map || !this.leafletLoaded) return;

      // Clear existing markers
      Object.values(this.markers).forEach(m => this.map.removeLayer(m));
      this.markers = {};

      const bounds = [];

      this.unifiedItems.forEach(item => {
        if (!item.hasCoords) return;

        // Custom high-end markers using Leaflet's divIcon
        const color = item.type === 'grave' ? '#10b981' : '#f43f5e';
        const iconHtml = `
          <div class="custom-map-pin" style="background-color: ${color}">
            <i class="bx ${item.type === 'grave' ? 'bx-shield' : 'bx-home-alt-2'}"></i>
            <div class="pin-pulse" style="background-color: ${color}"></div>
          </div>
        `;

        const customIcon = window.L.divIcon({
          html: iconHtml,
          className: 'custom-div-icon',
          iconSize: [36, 36],
          iconAnchor: [18, 36],
          popupAnchor: [0, -32]
        });

        // Construct elegant popup
        const defaultPhoto = item.type === 'grave'
          ? 'https://images.unsplash.com/photo-1595126731003-754972d54756?auto=format&fit=crop&q=80&w=400'
          : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&q=80&w=400';

        const popupContent = `
          <div class="map-popup-card">
            <div class="popup-image-container">
              <img src="${item.photo || defaultPhoto}" alt="${item.name}" onerror="this.src='${defaultPhoto}'" />
              <span class="popup-badge ${item.type}">${item.type === 'grave' ? 'Mộ cổ' : 'Nhà thờ họ'}</span>
            </div>
            <div class="popup-body">
              <h6 class="popup-title">${item.name}</h6>
              <p class="popup-address"><i class="bx bx-map"></i> ${item.address || 'Chưa cập nhật địa chỉ'}</p>
              <p class="popup-notes">${item.notes}</p>
              <hr class="my-2" />
              <div class="d-flex justify-content-between align-items-center">
                <span class="popup-coords">${item.lat.toFixed(5)}, ${item.lng.toFixed(5)}</span>
                <a href="javascript:void(0)" 
                   onclick="window.dispatchEvent(new CustomEvent('trigger-route', {detail: {lat: ${item.lat}, lng: ${item.lng}}}))" 
                   class="btn btn-sm btn-popup-navigate">
                  <i class="bx bx-navigation"></i> Dẫn đường
                </a>
              </div>
            </div>
          </div>
        `;

        const marker = window.L.marker([item.lat, item.lng], { icon: customIcon })
          .bindPopup(popupContent, { maxWidth: 280, minWidth: 260, className: 'premium-leaflet-popup' });

        // Highlight card when marker popup is opened
        marker.on('popupopen', () => {
          this.selectedItem = item;
          // Smooth scroll inside sidebar to the selected card
          this.$nextTick(() => {
            const cardEl = document.querySelector(`.location-card.active`);
            if (cardEl) {
              cardEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
          });
        });

        marker.addTo(this.map);
        this.markers[item.uniqueId] = marker;
        bounds.push([item.lat, item.lng]);
      });

      // Fit map view bounds if markers exist
      if (bounds.length > 0) {
        this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 18 });
      }
    },
    focusLocation(item) {
      if (!this.map || !this.leafletLoaded) return;
      this.selectedItem = item;

      if (!item.hasCoords) {
        toastr.info("Vị trí này chưa được ghim tọa độ Kinh/Vĩ.");
        return;
      }

      // Center map on coordinates with zoom level 18 to see street/house numbers
      this.map.flyTo([item.lat, item.lng], 18, {
        animate: true,
        duration: 1.2
      });

      // Trigger popup opening
      const marker = this.markers[item.uniqueId];
      if (marker) {
        setTimeout(() => {
          marker.openPopup();
        }, 600);
      }
    },
    handleRouteTrigger(e) {
      this.startInAppRouting(e.detail);
    },
    startInAppRouting(item) {
      if (!item.lat || !item.lng) return;
      if (!this.map || !window.L.Routing) {
          toastr.error('Hệ thống chỉ đường chưa sẵn sàng.');
          return;
      }
      
      const destination = [item.lat, item.lng];

      if ("geolocation" in navigator) {
        toastr.info("Đang lấy vị trí của bạn...");
        navigator.geolocation.getCurrentPosition((position) => {
          const userLat = position.coords.latitude;
          const userLng = position.coords.longitude;
          
          this.drawRoute([userLat, userLng], destination);
        }, (error) => {
          console.error("Lỗi geolocation: ", error);
          toastr.warning("Bạn đã từ chối vị trí. Đang dùng vị trí mặc định (bạn có thể kéo điểm bắt đầu).");
          // Fallback to map center
          const center = this.map.getCenter();
          this.drawRoute([center.lat, center.lng], destination, true);
        }, { enableHighAccuracy: true });
      } else {
        toastr.error("Trình duyệt không hỗ trợ định vị.");
        const center = this.map.getCenter();
        this.drawRoute([center.lat, center.lng], destination, true);
      }
    },
    drawRoute(startCoords, endCoords, isFallback = false) {
      if (this.routingControl) {
        this.map.removeControl(this.routingControl);
      }
      this.routeStats = null;
      
      this.routingControl = window.L.Routing.control({
        waypoints: [
          window.L.latLng(startCoords[0], startCoords[1]),
          window.L.latLng(endCoords[0], endCoords[1])
        ],
        routeWhileDragging: isFallback,
        addWaypoints: false,
        showAlternatives: false,
        show: false, // Hides the bulky text instruction panel to prevent overlap with the top navbar
        fitSelectedRoutes: true,
        lineOptions: {
            styles: [{color: '#f97316', opacity: 0.8, weight: 6}]
        },
        createMarker: function(i, waypoint, n) {
            if (i === 0) {
                return window.L.marker(waypoint.latLng, {
                    draggable: isFallback, // Allow dragging if it's a fallback location
                    icon: window.L.divIcon({
                        className: 'custom-div-icon',
                        html: `<div style="background:#3b82f6; width:18px; height:18px; border-radius:50%; border:3px solid white; box-shadow:0 0 5px rgba(0,0,0,0.5);"></div>`,
                        iconSize: [18,18],
                        iconAnchor: [9,9]
                    })
                }).bindPopup(isFallback ? "Kéo chấm xanh này đến vị trí của bạn" : "Vị trí của bạn").openPopup();
            }
            return null;
        }
      }).addTo(this.map);

      // Listen for route calculation to extract distance & duration (exact numbers on the road)
      this.routingControl.on('routesfound', (e) => {
        const routes = e.routes;
        if (routes && routes.length > 0) {
          const route = routes[0];
          const distance = route.summary.totalDistance; // meters
          const time = route.summary.totalTime; // seconds
          
          const formattedDistance = distance >= 1000 
            ? (distance / 1000).toFixed(1) + ' km' 
            : Math.round(distance) + ' m';
            
          const minutes = Math.round(time / 60);
          let formattedDuration = '';
          if (minutes >= 60) {
            const hours = Math.floor(minutes / 60);
            const remainingMins = minutes % 60;
            formattedDuration = `${hours} giờ ${remainingMins} phút`;
          } else {
            formattedDuration = `${minutes} phút`;
          }
          
          this.routeStats = {
            distance: formattedDistance,
            duration: formattedDuration
          };
        }
      });
    },
    clearRoute() {
      if (this.routingControl) {
        this.map.removeControl(this.routingControl);
        this.routingControl = null;
      }
      this.routeStats = null;
    }
  },
  watch: {
    // Re-plot when filters change
    filteredItems() {
      this.plotMarkers();
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.map-view-container {
  display: flex;
  height: calc(100vh - 140px);
  width: 96%;
  max-width: 1600px;
  margin: 100px auto 40px auto;
  position: relative;
  font-family: 'Inter', sans-serif;
  overflow: hidden;
  border-radius: 24px;
  box-shadow: 0 15px 40px rgba(212, 175, 55, 0.15), 0 0 0 2px rgba(212, 175, 55, 0.2);
  background: #faf9f6;
}

/* SIDEBAR GLASSMORPHISM STYLES */
.glass-sidebar {
  position: absolute;
  top: 20px;
  left: 20px;
  bottom: 20px;
  width: 380px;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(15px);
  -webkit-backdrop-filter: blur(15px);
  border: 1px solid rgba(255, 255, 255, 0.35);
  border-radius: 20px;
  padding: 24px;
  display: flex;
  flex-direction: column;
  color: #1e293b;
  transition: all 0.3s ease;
}

[data-theme="dark"] .glass-sidebar {
  background: rgba(15, 23, 42, 0.85);
  border-color: rgba(255, 255, 255, 0.08);
  color: #f8fafc;
}

.sidebar-header {
  margin-bottom: 20px;
}

.sidebar-title {
  font-weight: 700;
  font-size: 19px;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.text-orange {
  color: #f97316;
}

.sidebar-subtitle {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 0;
}

[data-theme="dark"] .sidebar-subtitle {
  color: #94a3b8;
}

/* STATS ROW */
.quick-stats-row {
  display: flex;
  gap: 8px;
}

.stat-card {
  flex: 1;
  background: rgba(255, 255, 255, 0.6);
  border: 1px solid rgba(0, 0, 0, 0.05);
  border-radius: 12px;
  padding: 10px;
  text-align: center;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  flex-direction: column;
}

[data-theme="dark"] .stat-card {
  background: rgba(255, 255, 255, 0.03);
  border-color: rgba(255, 255, 255, 0.05);
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.stat-card.active {
  background: #f97316;
  border-color: #ea580c;
  color: #fff;
}

.stat-card.active .stat-lbl {
  color: rgba(255, 255, 255, 0.8);
}

.stat-card.grave-stat.active {
  background: #10b981;
  border-color: #059669;
}

.stat-card.shrine-stat.active {
  background: #f43f5e;
  border-color: #e11d48;
}

.stat-num {
  font-size: 16px;
  font-weight: 700;
}

.stat-lbl {
  font-size: 10px;
  color: #64748b;
  font-weight: 500;
  text-transform: uppercase;
  margin-top: 2px;
}

[data-theme="dark"] .stat-lbl {
  color: #94a3b8;
}

/* SEARCH */
.search-box .input-group {
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 12px;
  overflow: hidden;
  transition: all 0.25s;
}

[data-theme="dark"] .search-box .input-group {
  background: rgba(0,0,0,0.2);
  border-color: rgba(255, 255, 255, 0.1);
}

.search-box .input-group:focus-within {
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.15);
}

.search-box .input-group-text {
  background: transparent;
  border: none;
  color: #64748b;
  padding-left: 14px;
}

.search-box .form-control {
  background: transparent;
  border: none;
  font-size: 14px;
  padding: 10px 10px 10px 4px;
  color: inherit;
  box-shadow: none !important;
}

.search-box .form-control::placeholder {
  color: #94a3b8;
}

.btn-clear-search {
  background: transparent;
  border: none;
  color: #94a3b8;
  padding: 0 10px;
}

.btn-clear-search:hover {
  color: #f43f5e;
}

/* LOCATION LIST */
.location-list {
  overflow-y: auto;
  margin-right: -8px;
  padding-right: 8px;
}

.location-list::-webkit-scrollbar {
  width: 4px;
}

.location-list::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.1);
  border-radius: 10px;
}

[data-theme="dark"] .location-list::-webkit-scrollbar-thumb {
  background: rgba(255,255,255,0.1);
}

.list-wrapper {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.location-card {
  background: rgba(255, 255, 255, 0.5);
  border: 1px solid rgba(0, 0, 0, 0.05);
  border-radius: 14px;
  padding: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
}

[data-theme="dark"] .location-card {
  background: rgba(255, 255, 255, 0.02);
  border-color: rgba(255, 255, 255, 0.04);
}

.location-card:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.8);
  border-color: rgba(249, 115, 22, 0.2);
}

[data-theme="dark"] .location-card:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(249, 115, 22, 0.3);
}

.location-card.active {
  background: rgba(249, 115, 22, 0.06);
  border-color: #f97316;
  box-shadow: 0 4px 15px rgba(249, 115, 22, 0.08);
}

[data-theme="dark"] .location-card.active {
  background: rgba(249, 115, 22, 0.12);
  border-color: #f97316;
}

.location-card.no-coords {
  opacity: 0.75;
}

.card-icon-box {
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
  flex-shrink: 0;
}

.card-icon-box.grave {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.card-icon-box.shrine {
  background: rgba(244, 63, 94, 0.1);
  color: #f43f5e;
}

.badge-type {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.badge-type.grave {
  color: #10b981;
}

.badge-type.shrine {
  color: #f43f5e;
}

.item-title {
  font-size: 14px;
  font-weight: 700;
  color: inherit;
}

.item-desc {
  font-size: 12px;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.coords-text {
  font-size: 11px;
  color: #64748b;
  font-weight: 500;
}

[data-theme="dark"] .coords-text {
  color: #94a3b8;
}

.btn-navigate-inline {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%);
  color: #fff;
  border: none;
  font-weight: 600;
  font-size: 11.5px;
  padding: 5px 12px;
  border-radius: 20px;
  box-shadow: 0 4px 10px rgba(244, 63, 94, 0.2);
  transition: all 0.2s;
}

.btn-navigate-inline:hover {
  transform: scale(1.04);
  color: #fff;
  box-shadow: 0 6px 15px rgba(244, 63, 94, 0.3);
}

.font-sm {
  font-size: 10px !important;
}

/* FOOTER */
.btn-back-gp {
  background: var(--input-bg, #f1f5f9);
  border: 1px solid rgba(0,0,0,0.05);
  color: var(--text-main);
  font-weight: 600;
  padding: 10px;
  border-radius: 12px;
  font-size: 13.5px;
  transition: all 0.25s;
}

.btn-back-gp:hover {
  background: #f97316;
  color: #fff;
  border-color: transparent;
}

/* MAP CANVAS */
.map-canvas {
  height: 100%;
  width: 100%;
  z-index: 1;
}

/* MAP MARKERS AND POPUPS */
.custom-map-pin {
  width: 36px;
  height: 36px;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  border: 2.5px solid #ffffff;
}

.custom-map-pin i {
  transform: rotate(45deg);
  color: #ffffff;
  font-size: 18px;
}

.pin-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  animation: markerPulse 1.8s infinite ease-in-out;
  opacity: 0;
  z-index: -1;
}

@keyframes markerPulse {
  0% {
    transform: rotate(45deg) scale(1);
    opacity: 0.5;
  }
  100% {
    transform: rotate(45deg) scale(2.2);
    opacity: 0;
  }
}
</style>

<!-- Non-scoped styling to override leaflet style overrides -->
<style>
.premium-leaflet-popup .leaflet-popup-content-wrapper {
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.15);
  padding: 0;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.4);
}

[data-theme="dark"] .premium-leaflet-popup .leaflet-popup-content-wrapper {
  background: rgba(15, 23, 42, 0.95);
  border-color: rgba(255,255,255,0.1);
  color: #f8fafc;
}

.premium-leaflet-popup .leaflet-popup-content {
  margin: 0;
  line-height: 1.5;
}

.premium-leaflet-popup .leaflet-popup-tip {
  background: rgba(255, 255, 255, 0.95);
}

[data-theme="dark"] .premium-leaflet-popup .leaflet-popup-tip {
  background: rgba(15, 23, 42, 0.95);
}

.map-popup-card {
  width: 100%;
}

.popup-image-container {
  height: 130px;
  position: relative;
  overflow: hidden;
  width: 100%;
}

.popup-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.popup-badge {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  padding: 4px 8px;
  border-radius: 20px;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.popup-badge.grave {
  background: #10b981;
}

.popup-badge.shrine {
  background: #f43f5e;
}

.popup-body {
  padding: 14px;
}

.popup-title {
  font-weight: 700;
  font-size: 14.5px;
  margin-bottom: 6px;
  color: #1e293b;
}

[data-theme="dark"] .popup-title {
  color: #ffffff;
}

.popup-address {
  font-size: 11.5px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 4px;
  margin-bottom: 8px;
}

[data-theme="dark"] .popup-address {
  color: #cbd5e1;
}

.popup-notes {
  font-size: 11px;
  color: #475569;
  margin-bottom: 0;
}

[data-theme="dark"] .popup-notes {
  color: #cbd5e1;
}

.popup-coords {
  font-size: 10.5px;
  color: #94a3b8;
  font-family: monospace;
}

.btn-popup-navigate {
  background: linear-gradient(135deg, #f97316 0%, #f43f5e 100%);
  color: #fff !important;
  font-weight: 600;
  font-size: 11px;
  padding: 5px 12px;
  border: none;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

.btn-popup-navigate:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(249, 115, 22, 0.2);
}

.btn-refresh-premium {
  background: var(--input-bg, #f1f5f9) !important;
  border: 1px solid var(--border-color, #cbd5e1) !important;
  width: 36px;
  height: 36px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-refresh-premium:hover {
  transform: rotate(30deg) scale(1.05);
  border-color: #f97316 !important;
  box-shadow: 0 4px 12px rgba(249, 115, 22, 0.15);
}
.btn-refresh-premium:active {
  transform: scale(0.95);
}

/* Floating Routing Stats Card Styles */
.routing-stats-overlay {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 16px;
  padding: 14px 18px;
  color: #1e293b;
  width: 300px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
  transition: all 0.3s ease;
  animation: slideInDown 0.3s ease-out;
}

[data-theme="dark"] .routing-stats-overlay {
  background: rgba(15, 23, 42, 0.9);
  border-color: rgba(255, 255, 255, 0.08);
  color: #f8fafc;
}

.route-icon-box {
  width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #f97316 0%, #f43f5e 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #fff;
  font-size: 20px;
}

.stats-title {
  font-weight: 700;
  font-size: 13.5px;
}

.stats-values {
  font-size: 12px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 4px;
  font-weight: 600;
}

.btn-close-route {
  background: rgba(0, 0, 0, 0.05);
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #64748b;
  transition: all 0.2s;
}

[data-theme="dark"] .btn-close-route {
  background: rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
}

.btn-close-route:hover {
  background: #f43f5e;
  color: #fff;
}

@keyframes slideInDown {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}
</style>
