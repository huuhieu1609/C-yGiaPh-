<template>
  <div class="map-manager-container">
    <!-- Left Panel: Sidebar containing Statistics, Lists, Create Form, and Edit Form -->
    <div class="sidebar-panel d-flex flex-column shadow-lg">
      
      <!-- Panel Header with Add Button -->
      <div class="panel-header d-flex justify-content-between align-items-center mb-2">
        <div>
          <h4 class="fw-bold text-orange mb-0">
            <i class="bx bx-map-pin"></i> Số Hóa Bản Đồ
          </h4>
          <p class="text-muted font-sm mb-0">Quản lý định vị Mộ phần & Nhà thờ</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="loadAllData" :disabled="isLoading" title="Làm mới dữ liệu">
            <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
          </button>
          <button v-if="!isCreating" class="btn btn-sm btn-create-new d-flex align-items-center gap-1" @click="startCreating">
            <i class="bx bx-plus-circle"></i> Thêm mới
          </button>
        </div>
      </div>

      <!-- Quick Statistics Cards (hidden when creating) -->
      <div v-if="!isCreating" class="row g-2 mb-3">
        <div class="col-6">
          <div class="stat-box status-pinned text-center">
            <span class="stat-count text-success">{{ stats.pinned }}</span>
            <span class="stat-title text-muted">Đã định vị</span>
          </div>
        </div>
        <div class="col-6">
          <div class="stat-box status-unpinned text-center">
            <span class="stat-count text-danger">{{ stats.unpinned }}</span>
            <span class="stat-title text-muted">Chưa định vị</span>
          </div>
        </div>
      </div>

      <!-- Tabs between MoPhan and NhaThoHo (hidden when creating) -->
      <ul v-if="!isCreating" class="nav nav-pills custom-pills mb-3" id="manager-tab" role="tablist">
        <li class="nav-item flex-grow-1" role="presentation">
          <button 
            class="nav-link w-100 active" 
            id="grave-tab" 
            data-bs-toggle="pill" 
            data-bs-target="#grave-pane" 
            type="button" 
            role="tab" 
            @click="onTabChange('grave')"
          >
            <i class="bx bx-shield"></i> Mộ Phần ({{ graves.length }})
          </button>
        </li>
        <li class="nav-item flex-grow-1" role="presentation">
          <button 
            class="nav-link w-100" 
            id="shrine-tab" 
            data-bs-toggle="pill" 
            data-bs-target="#shrine-pane" 
            type="button" 
            role="tab" 
            @click="onTabChange('shrine')"
          >
            <i class="bx bx-home-alt-2"></i> Nhà Thờ ({{ shrines.length }})
          </button>
        </li>
      </ul>

      <!-- Main Search (hidden when creating) -->
      <div v-if="!isCreating" class="search-box mb-3">
        <div class="input-group">
          <span class="input-group-text"><i class="bx bx-search"></i></span>
          <input 
            type="text" 
            class="form-control" 
            placeholder="Tìm theo tên..." 
            v-model="searchQuery"
          />
        </div>
      </div>

      <!-- Create New Form Panel -->
      <div v-if="isCreating" class="create-form-wrapper flex-grow-1 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold text-dark mb-0">
            <i class="bx bx-plus text-orange"></i> Tạo {{ activeTab === 'grave' ? 'Mộ Phần' : 'Nhà Thờ' }} Mới
          </h5>
          <button class="btn btn-sm btn-outline-secondary py-1" @click="cancelCreating">Hủy</button>
        </div>

        <div class="form-scrollable flex-grow-1">
          <!-- Name Input -->
          <div class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">
              Tên {{ activeTab === 'grave' ? 'Mộ phần' : 'Nhà thờ' }} <span class="text-danger">*</span>
            </label>
            <input 
              type="text" 
              class="form-control border" 
              v-model="newForm.name" 
              :placeholder="activeTab === 'grave' ? 'VD: Mộ Cụ Thủy Tổ' : 'VD: Nhà Thờ Tổ Chi Nhánh Phía Bắc'"
              required
            />
          </div>

          <!-- Address Input -->
          <div class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">Địa chỉ</label>
            <input 
              type="text" 
              class="form-control border" 
              v-model="newForm.address" 
              placeholder="VD: Nghĩa trang Thạch Thất, Hà Nội"
            />
            <span class="text-muted font-xxs mt-1 d-block">
              <i class="bx bx-info-circle text-orange"></i> Click bản đồ để lấy vị trí, sau đó bạn có thể chỉnh sửa/tự nhập thêm số nhà chính xác.
            </span>
          </div>

          <!-- Image URL Input -->
          <div class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">Hình ảnh (Link URL)</label>
            <input 
              type="url" 
              class="form-control border" 
              v-model="newForm.hinh_anh" 
              placeholder="Nhập link hình ảnh hiển thị..."
            />
          </div>

          <!-- Description / Notes -->
          <div class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">Ghi chú / Mô tả</label>
            <textarea 
              class="form-control border" 
              rows="3" 
              v-model="newForm.notes" 
              placeholder="Ghi chú thêm về vị trí, lịch sử..."
            ></textarea>
          </div>

          <!-- Dropdown based on Tab Type -->
          <div v-if="activeTab === 'grave'" class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">Thành viên liên kết</label>
            <select class="form-select border" v-model="newForm.memberId">
              <option :value="null">-- Chọn thành viên đã khuất --</option>
              <option v-for="m in listMembers" :key="m.id" :value="m.id">
                {{ m.ho_ten }} ({{ m.gioi_tinh }} - Đời {{ m.doi_thu }})
              </option>
            </select>
          </div>

          <div v-else class="mb-3">
            <label class="form-label fw-bold font-xs text-muted mb-1">Chi nhánh quản lý <span class="text-danger">*</span></label>
            <select class="form-select border" v-model="newForm.branchId">
              <option :value="null">-- Chọn chi nhánh dòng tộc --</option>
              <option v-for="b in listBranches" :key="b.id" :value="b.id">
                {{ b.ten_chi }}
              </option>
            </select>
          </div>

          <!-- Coordinates (Read Only) -->
          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold font-xs text-muted mb-1">Vĩ độ (Lat)</label>
              <input type="text" class="form-control bg-light font-xs border" v-model="newForm.lat" readonly placeholder="Click bản đồ" />
            </div>
            <div class="col-6">
              <label class="form-label fw-bold font-xs text-muted mb-1">Kinh độ (Lng)</label>
              <input type="text" class="form-control bg-light font-xs border" v-model="newForm.lng" readonly placeholder="Click bản đồ" />
            </div>
            <div class="col-12 mt-1">
              <span class="text-muted font-xxs">
                <i class="bx bx-info-circle text-orange"></i> Click trực tiếp lên điểm mong muốn trên bản đồ để chọn tọa độ ghim.
              </span>
            </div>
          </div>
        </div>

        <div class="border-top pt-3 mt-auto">
          <button class="btn btn-action-orange w-100 fw-bold py-2" @click="saveNewItem" :disabled="isSaving">
            <i class="bx bx-check-circle"></i> {{ isSaving ? 'Đang tạo...' : 'Tạo Mới & Ghim Lên Bản Đồ' }}
          </button>
        </div>
      </div>

      <!-- Tab Content (Lists of existing items - hidden when creating) -->
      <div v-if="!isCreating" class="list-container flex-grow-1">
        <div v-if="isLoading" class="text-center py-5">
          <div class="spinner-border text-orange" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="text-muted mt-2">Đang tải danh sách dòng tộc...</p>
        </div>

        <div v-else class="tab-content" id="manager-tabContent">
          <!-- Graves List -->
          <div class="tab-pane fade show active" id="grave-pane" role="tabpanel">
            <div v-if="filteredGraves.length === 0" class="text-center py-5 text-muted">
              <i class="bx bx-info-circle fs-2 mb-2"></i>
              <p>Không có mộ phần nào hợp lệ</p>
            </div>
            <div v-else class="list-wrapper">
              <div 
                v-for="item in filteredGraves" 
                :key="item.id" 
                class="item-row"
                :class="{ 
                  active: editingItem && editingItem.type === 'grave' && editingItem.id === item.id,
                  'has-pos': item.vi_do && item.kinh_do
                }"
                @click="selectItemForEditing(item, 'grave')"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1 me-2 overflow-hidden">
                    <h6 class="mb-1 item-name text-truncate">{{ item.ten_mo }}</h6>
                    <p class="mb-0 text-muted font-xs text-truncate">{{ item.dia_chi || 'Chưa cập nhật địa chỉ' }}</p>
                  </div>
                  <span class="badge flex-shrink-0" :class="item.vi_do && item.kinh_do ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                    {{ item.vi_do && item.kinh_do ? 'Đã ghim' : 'Chưa ghim' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Shrines List -->
          <div class="tab-pane fade" id="shrine-pane" role="tabpanel">
            <div v-if="filteredShrines.length === 0" class="text-center py-5 text-muted">
              <i class="bx bx-info-circle fs-2 mb-2"></i>
              <p>Không có nhà thờ nào hợp lệ</p>
            </div>
            <div v-else class="list-wrapper">
              <div 
                v-for="item in filteredShrines" 
                :key="item.id" 
                class="item-row"
                :class="{ 
                  active: editingItem && editingItem.type === 'shrine' && editingItem.id === item.id,
                  'has-pos': item.vi_do && item.kinh_do
                }"
                @click="selectItemForEditing(item, 'shrine')"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <div class="flex-grow-1 me-2 overflow-hidden">
                    <h6 class="mb-1 item-name text-truncate">{{ item.ten_nha_tho }}</h6>
                    <p class="mb-0 text-muted font-xs text-truncate">{{ item.dia_chi || 'Chưa cập nhật địa chỉ' }}</p>
                  </div>
                  <span class="badge flex-shrink-0" :class="item.vi_do && item.kinh_do ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'">
                    {{ item.vi_do && item.kinh_do ? 'Đã ghim' : 'Chưa ghim' }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bottom Panel: Edit existing item Form Panel (hidden when creating) -->
      <div class="edit-form-panel border-top pt-3 mt-2" v-if="editingItem && !isCreating">
        <h6 class="fw-bold mb-2 text-dark d-flex align-items-center justify-content-between">
          <span><i class="bx bx-edit text-orange"></i> Cập Nhật Thông Tin</span>
          <button class="btn btn-close-edit btn-sm" @click="cancelEditing"><i class="bx bx-x"></i></button>
        </h6>

        <div class="form-scrollable" style="max-height: 250px; overflow-y: auto;">
          <!-- Name Edit -->
          <div class="mb-2">
            <label class="form-label font-xs text-muted mb-1">Tên</label>
            <input type="text" class="form-control form-control-sm border" v-model="editingItem.name" />
          </div>

          <!-- Address Edit -->
          <div class="mb-2">
            <label class="form-label font-xs text-muted mb-1">Địa chỉ</label>
            <input type="text" class="form-control form-control-sm border" v-model="editingItem.address" />
            <span class="text-muted font-xxs mt-1 d-block">
              <i class="bx bx-info-circle text-orange"></i> Click bản đồ để lấy vị trí, sau đó bạn có thể chỉnh sửa/tự nhập thêm số nhà chính xác.
            </span>
          </div>

          <!-- Image URL Edit -->
          <div class="mb-2">
            <label class="form-label font-xs text-muted mb-1">Hình ảnh (Link URL)</label>
            <input type="url" class="form-control form-control-sm border" v-model="editingItem.hinh_anh" placeholder="Nhập link hình ảnh..." />
          </div>

          <!-- Notes/Description Edit -->
          <div class="mb-2">
            <label class="form-label font-xs text-muted mb-1">Ghi chú / Mô tả</label>
            <textarea class="form-control form-control-sm border" rows="2" v-model="editingItem.notes"></textarea>
          </div>

          <!-- Coordinates Inputs -->
          <div class="row g-2 mb-2">
            <div class="col-6">
              <label class="form-label font-xs text-muted mb-1">Vĩ độ (Lat)</label>
              <input 
                type="number" 
                step="any" 
                class="form-control form-control-sm border" 
                v-model.number="editingItem.lat" 
                @input="updateMarkerPosition"
              />
            </div>
            <div class="col-6">
              <label class="form-label font-xs text-muted mb-1">Kinh độ (Lng)</label>
              <input 
                type="number" 
                step="any" 
                class="form-control form-control-sm border" 
                v-model.number="editingItem.lng" 
                @input="updateMarkerPosition"
              />
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-2">
          <button class="btn btn-sm btn-action-orange flex-grow-1 fw-semibold py-2" @click="saveCoordinates" :disabled="isSaving">
            <i class="bx bx-save"></i> {{ isSaving ? 'Đang lưu...' : 'Lưu Thay Đổi' }}
          </button>
          <button v-if="editingItem.lat && editingItem.lng" class="btn btn-sm btn-outline-warning" @click="removeCoordinates" title="Xóa ghim">
            <i class="bx bx-pin"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" @click="deleteItem" title="Xóa vĩnh viễn">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Map Canvas on the Right -->
    <div id="leaflet-manager-map" class="map-canvas"></div>

    <!-- Helper banner floating on top of Map when editing/creating -->
    <div class="map-floating-overlay shadow-lg" v-if="editingItem || isCreating">
      <div class="d-flex align-items-center gap-2 text-white">
        <i class="bx bx-info-circle fs-5 animate-pulse"></i>
        <div>
          <h6 class="mb-0 fw-bold">Chế độ định vị trực quan</h6>
          <p class="mb-0 font-xxs opacity-90">Click bất kỳ điểm nào trên bản đồ hoặc Kéo thả marker màu cam để chọn vị trí chính xác.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'QuanLyBanDo',
  data() {
    return {
      graves: [],
      shrines: [],
      listMembers: [], // for Grave creation linking
      listBranches: [], // for Shrine branch linking
      searchQuery: '',
      activeTab: 'grave', // grave, shrine
      editingItem: null, // item structure: { id, type, name, lat, lng, address, notes }
      isCreating: false,
      newForm: {
        name: '',
        address: '',
        notes: '',
        memberId: null,
        branchId: null,
        lat: null,
        lng: null,
        hinh_anh: ''
      },
      map: null,
      activePinMarker: null,
      staticMarkers: {}, // Keyed by item type-id
      isLoading: true,
      isSaving: false,
      leafletLoaded: false
    }
  },
  computed: {
    stats() {
      let pinned = 0;
      let unpinned = 0;
      this.graves.forEach(g => {
        if (g.vi_do && g.kinh_do) pinned++;
        else unpinned++;
      });
      this.shrines.forEach(s => {
        if (s.vi_do && s.kinh_do) pinned++;
        else unpinned++;
      });
      return { pinned, unpinned };
    },
    filteredGraves() {
      return this.graves.filter(g => 
        !this.searchQuery || g.ten_mo.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    },
    filteredShrines() {
      return this.shrines.filter(s => 
        !this.searchQuery || s.ten_nha_tho.toLowerCase().includes(this.searchQuery.toLowerCase())
      );
    }
  },
  mounted() {
    this.loadLeafletCDN(() => {
      this.leafletLoaded = true;
      this.initMap();
      this.loadAllData();
      this.loadMetaLists();
    });
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadLeafletCDN(callback) {
      if (window.L) {
        callback();
        return;
      }
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      link.crossOrigin = '';
      document.head.appendChild(link);

      const script = document.createElement('script');
      script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
      script.crossOrigin = '';
      script.onload = () => {
        callback();
      };
      document.head.appendChild(script);
    },
    initMap() {
      if (!this.leafletLoaded || this.map) return;

      this.map = window.L.map('leaflet-manager-map').setView([16.047079, 108.206230], 6);

      const openMapKey = 'DNmLXlnYjfHFnvaThBU1FXYrXAGhfpgD';
      const openMapUrl = `https://api.openmap.vn/styles/osm-bright/{z}/{x}/{y}.png?apikey=${openMapKey}`;
      const osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';

      const tileLayer = window.L.tileLayer(openMapUrl, {
        attribution: '&copy; <a href="https://openmap.vn" target="_blank">OpenMap.vn</a> contributors',
        maxZoom: 19
      });

      tileLayer.on('tileerror', () => {
        console.warn("OpenMap.vn tile server loading error. Falling back to OpenStreetMap.");
        tileLayer.setUrl(osmUrl);
      });

      tileLayer.addTo(this.map);

      // Click event for coordinates capture
      this.map.on('click', (e) => {
        const { lat, lng } = e.latlng;
        const latVal = parseFloat(lat.toFixed(6));
        const lngVal = parseFloat(lng.toFixed(6));

        if (this.isCreating) {
          this.newForm.lat = latVal;
          this.newForm.lng = lngVal;
          this.updateCreationMarker();
          this.reverseGeocode(latVal, lngVal, 'new');
        } else if (this.editingItem) {
          this.editingItem.lat = latVal;
          this.editingItem.lng = lngVal;
          this.updateMarkerPosition();
          this.reverseGeocode(latVal, lngVal, 'edit');
        }
      });
    },
    loadAllData() {
      this.isLoading = true;
      const headers = this.getHeaders();

      const reqGraves = axios.get('http://127.0.0.1:8000/api/mo-phan/get-data', headers);
      const reqShrines = axios.get('http://127.0.0.1:8000/api/nha-tho-ho/get-data', headers);

      Promise.all([reqGraves, reqShrines])
        .then(([resG, resS]) => {
          if (resG.data.status) this.graves = resG.data.data;
          if (resS.data.status) this.shrines = resS.data.data;

          this.$nextTick(() => {
            this.plotStaticMarkers();
          });
        })
        .catch(err => {
          console.error("Lỗi khi tải dữ liệu bản đồ:", err);
          toastr.error("Không thể tải dữ liệu, vui lòng kiểm tra kết nối mạng.");
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    loadMetaLists() {
      const headers = this.getHeaders();
      // Load branch members
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data', headers)
        .then(res => {
          if (res.data.status) {
            // Filter deceased members or load all
            this.listMembers = res.data.data;
          }
        });

      // Load partner branches
      axios.get('http://127.0.0.1:8000/api/doi-tac/lay-chi-nhanh', headers)
        .then(res => {
          if (res.data.data) {
            this.listBranches = res.data.data;
            if (this.listBranches.length > 0) {
              this.newForm.branchId = this.listBranches[0].id;
            }
          }
        });
    },
    plotStaticMarkers() {
      if (!this.map || !this.leafletLoaded) return;

      Object.values(this.staticMarkers).forEach(m => this.map.removeLayer(m));
      this.staticMarkers = {};

      const bounds = [];

      this.graves.forEach(g => {
        if (!g.vi_do || !g.kinh_do) return;
        const markerKey = `grave-${g.id}`;
        
        const pinHtml = `<div class="custom-static-pin" style="background-color: #10b981"><i class="bx bx-shield"></i></div>`;
        const icon = window.L.divIcon({
          html: pinHtml,
          className: 'custom-div-icon',
          iconSize: [28, 28],
          iconAnchor: [14, 28]
        });

        const marker = window.L.marker([g.vi_do, g.kinh_do], { icon })
          .bindTooltip(`Mộ phần: ${g.ten_mo}`, { permanent: false, direction: 'top' });
        
        marker.addTo(this.map);
        this.staticMarkers[markerKey] = marker;
        bounds.push([g.vi_do, g.kinh_do]);
      });

      this.shrines.forEach(s => {
        if (!s.vi_do || !s.kinh_do) return;
        const markerKey = `shrine-${s.id}`;

        const pinHtml = `<div class="custom-static-pin" style="background-color: #f43f5e"><i class="bx bx-home-alt-2"></i></div>`;
        const icon = window.L.divIcon({
          html: pinHtml,
          className: 'custom-div-icon',
          iconSize: [28, 28],
          iconAnchor: [14, 28]
        });

        const marker = window.L.marker([s.vi_do, s.kinh_do], { icon })
          .bindTooltip(`Nhà thờ: ${s.ten_nha_tho}`, { permanent: false, direction: 'top' });

        marker.addTo(this.map);
        this.staticMarkers[markerKey] = marker;
        bounds.push([s.vi_do, s.kinh_do]);
      });

      if (bounds.length > 0 && !this.editingItem && !this.isCreating) {
        this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 18 });
      }
    },
    onTabChange(tabName) {
      this.activeTab = tabName;
      this.cancelEditing();
    },
    startCreating() {
      this.cancelEditing();
      this.isCreating = true;
      this.newForm = {
        name: '',
        address: '',
        notes: '',
        memberId: this.listMembers.length > 0 ? this.listMembers[0].id : null,
        branchId: this.listBranches.length > 0 ? this.listBranches[0].id : null,
        lat: null,
        lng: null,
        hinh_anh: ''
      };
    },
    cancelCreating() {
      this.isCreating = false;
      if (this.activePinMarker) {
        this.map.removeLayer(this.activePinMarker);
        this.activePinMarker = null;
      }
      this.plotStaticMarkers();
    },
    updateCreationMarker() {
      if (!this.map || !this.leafletLoaded || !this.newForm.lat || !this.newForm.lng) return;

      if (!this.activePinMarker) {
        const pinHtml = `<div class="custom-active-pin"><i class="bx bx-map-pin"></i><div class="active-pulse"></div></div>`;
        const icon = window.L.divIcon({
          html: pinHtml,
          className: 'custom-div-icon',
          iconSize: [38, 38],
          iconAnchor: [19, 38]
        });

        this.activePinMarker = window.L.marker([this.newForm.lat, this.newForm.lng], {
          icon: icon,
          draggable: true
        }).addTo(this.map);

        this.activePinMarker.on('dragend', (e) => {
          const pos = e.target.getLatLng();
          const latVal = parseFloat(pos.lat.toFixed(6));
          const lngVal = parseFloat(pos.lng.toFixed(6));
          this.newForm.lat = latVal;
          this.newForm.lng = lngVal;
          this.reverseGeocode(latVal, lngVal, 'new');
        });
      } else {
        this.activePinMarker.setLatLng([this.newForm.lat, this.newForm.lng]);
      }
    },
    saveNewItem() {
      if (!this.newForm.name) {
        toastr.warning(`Vui lòng nhập Tên ${this.activeTab === 'grave' ? 'mộ phần' : 'nhà thờ'}.`);
        return;
      }

      this.isSaving = true;
      const apiPrefix = this.activeTab === 'grave' ? 'mo-phan' : 'nha-tho-ho';
      
      const payload = this.activeTab === 'grave' ? {
        ten_mo: this.newForm.name,
        dia_chi: this.newForm.address,
        ghi_chu: this.newForm.notes,
        thanh_vien_id: this.newForm.memberId,
        vi_do: this.newForm.lat,
        kinh_do: this.newForm.lng,
        hinh_anh: this.newForm.hinh_anh
      } : {
        ten_nha_tho: this.newForm.name,
        dia_chi: this.newForm.address,
        mo_ta: this.newForm.notes,
        chi_nhanh_id: this.newForm.branchId,
        vi_do: this.newForm.lat,
        kinh_do: this.newForm.lng,
        hinh_anh: this.newForm.hinh_anh
      };

      axios.post(`http://127.0.0.1:8000/api/${apiPrefix}/create`, payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(`Đã thêm mới thành công "${this.newForm.name}"!`);
            this.loadAllData();
            this.cancelCreating();
          } else {
            toastr.error("Có lỗi xảy ra: " + res.data.message);
          }
        })
        .catch(err => {
          console.error("Lỗi khi thêm mới:", err);
          toastr.error("Lỗi kết nối máy chủ, không thể lưu.");
        })
        .finally(() => {
          this.isSaving = false;
        });
    },
    selectItemForEditing(item, type) {
      const lat = item.vi_do ? parseFloat(item.vi_do) : null;
      const lng = item.kinh_do ? parseFloat(item.kinh_do) : null;

      this.editingItem = {
        id: item.id,
        type: type,
        name: type === 'grave' ? item.ten_mo : item.ten_nha_tho,
        address: item.dia_chi,
        notes: type === 'grave' ? item.ghi_chu : item.mo_ta,
        lat: lat,
        lng: lng,
        hinh_anh: item.hinh_anh || '',
        originalItem: item
      };

      if (lat && lng) {
        this.map.flyTo([lat, lng], 18, { animate: true });
      }

      this.$nextTick(() => {
        this.updateMarkerPosition();
      });
    },
    updateMarkerPosition() {
      if (!this.map || !this.leafletLoaded || !this.editingItem) return;

      const lat = this.editingItem.lat;
      const lng = this.editingItem.lng;

      if (!lat || !lng) {
        if (this.activePinMarker) {
          this.map.removeLayer(this.activePinMarker);
          this.activePinMarker = null;
        }
        return;
      }

      if (!this.activePinMarker) {
        const pinHtml = `<div class="custom-active-pin"><i class="bx bx-map-pin"></i><div class="active-pulse"></div></div>`;
        const icon = window.L.divIcon({
          html: pinHtml,
          className: 'custom-div-icon',
          iconSize: [38, 38],
          iconAnchor: [19, 38]
        });

        this.activePinMarker = window.L.marker([lat, lng], {
          icon: icon,
          draggable: true
        }).addTo(this.map);

        this.activePinMarker.on('dragend', (event) => {
          const position = event.target.getLatLng();
          const latVal = parseFloat(position.lat.toFixed(6));
          const lngVal = parseFloat(position.lng.toFixed(6));
          this.editingItem.lat = latVal;
          this.editingItem.lng = lngVal;
          this.reverseGeocode(latVal, lngVal, 'edit');
        });
      } else {
        this.activePinMarker.setLatLng([lat, lng]);
      }
    },
    cancelEditing() {
      this.editingItem = null;
      if (this.activePinMarker) {
        this.map.removeLayer(this.activePinMarker);
        this.activePinMarker = null;
      }
      this.plotStaticMarkers();
    },
    saveCoordinates() {
      if (!this.editingItem) return;
      if (!this.editingItem.name) {
        toastr.warning("Tên không được để trống!");
        return;
      }

      this.isSaving = true;
      const apiPrefix = this.editingItem.type === 'grave' ? 'mo-phan' : 'nha-tho-ho';
      
      const payload = this.editingItem.type === 'grave' ? {
        id: this.editingItem.id,
        ten_mo: this.editingItem.name,
        dia_chi: this.editingItem.address,
        ghi_chu: this.editingItem.notes,
        vi_do: this.editingItem.lat,
        kinh_do: this.editingItem.lng,
        hinh_anh: this.editingItem.hinh_anh
      } : {
        id: this.editingItem.id,
        ten_nha_tho: this.editingItem.name,
        dia_chi: this.editingItem.address,
        mo_ta: this.editingItem.notes,
        vi_do: this.editingItem.lat,
        kinh_do: this.editingItem.lng,
        hinh_anh: this.editingItem.hinh_anh
      };

      axios.post(`http://127.0.0.1:8000/api/${apiPrefix}/update`, payload, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(`Đã cập nhật thông tin thành công cho "${this.editingItem.name}"`);
            this.loadAllData();
            this.cancelEditing();
          } else {
            toastr.error("Có lỗi xảy ra: " + res.data.message);
          }
        })
        .catch(err => {
          console.error("Lỗi cập nhật:", err);
          toastr.error("Lỗi kết nối máy chủ, không thể lưu.");
        })
        .finally(() => {
          this.isSaving = false;
        });
    },
    removeCoordinates() {
      if (!this.editingItem) return;
      this.editingItem.lat = null;
      this.editingItem.lng = null;
      this.updateMarkerPosition();
      toastr.info("Đã tháo ghim, nhấn Lưu Thay Đổi để xác nhận.");
    },
    deleteItem() {
      if (!this.editingItem) return;
      
      if (!confirm(`Bạn có chắc chắn muốn XÓA VĨNH VIỄN "${this.editingItem.name}" khỏi hệ thống?`)) {
        return;
      }

      this.isSaving = true;
      const apiPrefix = this.editingItem.type === 'grave' ? 'mo-phan' : 'nha-tho-ho';

      axios.post(`http://127.0.0.1:8000/api/${apiPrefix}/delete`, { id: this.editingItem.id }, this.getHeaders())
        .then(res => {
          if (res.data.status) {
            toastr.success(`Đã xóa vĩnh viễn địa điểm thành công!`);
            this.loadAllData();
            this.cancelEditing();
          } else {
            toastr.error("Lỗi khi xóa: " + res.data.message);
          }
        })
        .catch(err => {
          console.error("Lỗi khi xóa:", err);
          toastr.error("Lỗi kết nối máy chủ.");
        })
        .finally(() => {
          this.isSaving = false;
        });
    },
    reverseGeocode(lat, lng, target) {
      if (!lat || !lng) return;
      
      const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&email=huuhieu1609@gmail.com`;
      
      axios.get(url, {
        headers: {
          'Accept-Language': 'vi,en;q=0.9',
          'User-Agent': 'CayGiaPhaMapApp/1.0'
        }
      })
        .then(res => {
          if (res.data && res.data.display_name) {
            let displayAddress = res.data.display_name;
            
            // Clean up Nominatim response to be neat and standard for VN address hierarchy
            const addr = res.data.address;
            if (addr) {
              const houseNumber = addr.house_number || addr.building || '';
              const road = addr.road || '';
              const ward = addr.suburb || addr.village || addr.hamlet || addr.commune || '';
              
              const districtCandidates = [addr.city_district, addr.district, addr.town, addr.county];
              let district = '';
              for (const cand of districtCandidates) {
                if (cand && cand !== ward && !cand.toLowerCase().includes('thành phố') && !cand.toLowerCase().includes('tỉnh')) {
                  district = cand;
                  break;
                }
              }
              
              const province = addr.city || addr.province || addr.state || '';
              
              const parts = [];
              const streetAddress = [houseNumber, road].filter(p => !!p).join(' ');
              if (streetAddress) parts.push(streetAddress);
              if (ward) parts.push(ward);
              if (district && district !== ward) parts.push(district);
              if (province && province !== district && province !== ward) parts.push(province);
              
              if (parts.length > 0) {
                displayAddress = parts.join(', ');
              }
            }

            if (target === 'new') {
              this.newForm.address = displayAddress;
            } else if (target === 'edit' && this.editingItem) {
              this.editingItem.address = displayAddress;
            }
          }
        })
        .catch(err => {
          console.warn("Lỗi khi giải ngược địa chỉ bằng Nominatim:", err);
        });
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.map-manager-container {
  display: flex;
  height: calc(100vh - 30px);
  width: 100%;
  font-family: 'Inter', sans-serif;
  overflow: hidden;
  border-radius: 20px;
  background-color: var(--card-bg, #ffffff);
  border: 1px solid var(--border-color, rgba(0,0,0,0.05));
  position: relative;
}

/* SIDEBAR PANEL */
.sidebar-panel {
  width: 380px;
  background: var(--card-bg, #ffffff);
  border-right: 1px solid var(--border-color, rgba(0,0,0,0.05));
  padding: 24px;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  z-index: 10;
}

[data-theme="dark"] .sidebar-panel {
  background: #111c44;
  border-right-color: rgba(255,255,255,0.05);
}

.panel-header {
  margin-bottom: 8px;
}

.btn-create-new {
  background: rgba(249, 115, 22, 0.1);
  color: #f97316;
  border: 1px solid rgba(249, 115, 22, 0.2);
  border-radius: 20px;
  font-weight: 600;
  padding: 6px 14px;
  transition: all 0.2s;
}

.btn-create-new:hover {
  background: #f97316;
  color: #fff;
  border-color: transparent;
  transform: translateY(-1px);
}

.text-orange {
  color: #f97316;
}

.font-sm {
  font-size: 12.5px;
}

.font-xs {
  font-size: 11.5px;
}

.font-xxs {
  font-size: 10.5px;
}

/* QUICK STATS */
.stat-box {
  background: var(--input-bg, #f8fafc);
  border: 1px solid var(--border-color, rgba(0,0,0,0.03));
  border-radius: 12px;
  padding: 10px;
}

[data-theme="dark"] .stat-box {
  background: rgba(255,255,255,0.02);
  border-color: rgba(255,255,255,0.04);
}

.stat-count {
  display: block;
  font-size: 20px;
  font-weight: 700;
  line-height: 1.2;
}

.stat-title {
  font-size: 10px;
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.5px;
}

/* CUSTOM PILLS */
.custom-pills {
  background: var(--input-bg, #f1f5f9);
  padding: 4px;
  border-radius: 12px;
}

[data-theme="dark"] .custom-pills {
  background: rgba(0,0,0,0.15);
}

.custom-pills .nav-link {
  border-radius: 8px !important;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-sub) !important;
  padding: 8px 12px;
  border: none !important;
}

.custom-pills .nav-link.active {
  background: var(--card-bg, #ffffff) !important;
  color: #f97316 !important;
  box-shadow: 0 4px 10px rgba(0,0,0,0.04) !important;
}

[data-theme="dark"] .custom-pills .nav-link.active {
  background: #1e293b !important;
}

/* SEARCH INPUT */
.search-box .form-control {
  background: var(--input-bg, #f1f5f9) !important;
  border: 1px solid var(--border-color, rgba(0,0,0,0.03)) !important;
  font-size: 13.5px;
  padding: 9px 12px;
  border-radius: 0 10px 10px 0;
  color: var(--text-main);
}

.search-box .input-group-text {
  background: var(--input-bg, #f1f5f9);
  border: 1px solid var(--border-color, rgba(0,0,0,0.03));
  border-radius: 10px 0 0 10px;
  color: var(--text-sub);
}

/* CREATE FORM */
.create-form-wrapper {
  animation: slideInLeft 0.25s ease-out;
}

@keyframes slideInLeft {
  from { transform: translateX(-15px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.form-scrollable {
  overflow-y: auto;
  padding-right: 4px;
  margin-right: -4px;
}

.form-scrollable::-webkit-scrollbar {
  width: 4px;
}

.form-scrollable::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.05);
  border-radius: 10px;
}

.form-control, .form-select {
  border-radius: 10px;
  font-size: 13.5px;
  padding: 10px 14px;
  color: var(--text-main);
  background-color: var(--card-bg);
}

.form-control:focus, .form-select:focus {
  border-color: #f97316 !important;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1) !important;
}

/* LIST CONTENT */
.list-container {
  overflow-y: auto;
  margin-right: -8px;
  padding-right: 8px;
}

.list-container::-webkit-scrollbar {
  width: 4px;
}

.list-container::-webkit-scrollbar-thumb {
  background: rgba(0,0,0,0.08);
  border-radius: 10px;
}

.list-wrapper {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.item-row {
  background: var(--card-bg);
  border: 1px solid var(--border-color, rgba(0,0,0,0.05));
  border-radius: 12px;
  padding: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.item-row:hover {
  border-color: rgba(249, 115, 22, 0.3);
  transform: translateX(2px);
}

.item-row.active {
  border-color: #f97316;
  background: rgba(249, 115, 22, 0.05);
}

.item-row.has-pos {
  border-left: 3px solid #10b981;
}

.item-name {
  font-size: 13.5px;
  font-weight: 600;
  color: var(--text-main);
}

.badge {
  font-size: 9.5px;
  font-weight: 700;
  padding: 4px 8px;
  border-radius: 20px;
}

/* EDIT PANEL */
.edit-form-panel {
  background: var(--input-bg, #f8fafc);
  border-radius: 14px;
  padding: 16px;
  border: 1px solid var(--border-color, rgba(0,0,0,0.03));
}

[data-theme="dark"] .edit-form-panel {
  background: rgba(0,0,0,0.15);
}

.btn-close-edit {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 18px;
}

.btn-close-edit:hover {
  color: #f43f5e;
}

.form-control-sm {
  border-radius: 8px;
  font-size: 12.5px;
}

.btn-action-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important;
  color: #ffffff !important; 
  border: none !important; 
  border-radius: 10px !important;
  box-shadow: 0 4px 10px rgba(244, 63, 94, 0.15) !important;
}

.btn-action-orange:hover {
  transform: translateY(-0.5px);
  box-shadow: 0 6px 15px rgba(244, 63, 94, 0.25) !important;
}

/* RIGHT MAP CANVAS */
.map-canvas {
  flex-grow: 1;
  height: 100%;
  z-index: 1;
}

/* MAP OVERLAYS */
.map-floating-overlay {
  position: absolute;
  top: 20px;
  right: 20px;
  z-index: 999;
  background: rgba(249, 115, 22, 0.9);
  backdrop-filter: blur(10px);
  border-radius: 12px;
  padding: 12px 18px;
  max-width: 320px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.animate-pulse {
  animation: pulseIcon 1.5s infinite;
}

@keyframes pulseIcon {
  0% { transform: scale(1); }
  50% { transform: scale(1.15); }
  100% { transform: scale(1); }
}

/* CUSTOM MARKERS */
.custom-static-pin {
  width: 28px;
  height: 28px;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #fff;
  box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.custom-static-pin i {
  transform: rotate(45deg);
  color: #fff;
  font-size: 13px;
}

.custom-active-pin {
  width: 38px;
  height: 38px;
  background: #f97316;
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2.5px solid #fff;
  box-shadow: 0 4px 10px rgba(0,0,0,0.25);
  animation: bounceActivePin 0.6s ease-out;
}

.custom-active-pin i {
  transform: rotate(45deg);
  color: #fff;
  font-size: 20px;
}

.active-pulse {
  position: absolute;
  width: 100%;
  height: 100%;
  border-radius: 50%;
  background: rgba(249, 115, 22, 0.4);
  animation: activePulseRing 1.5s infinite ease-out;
  z-index: -1;
}

@keyframes bounceActivePin {
  0% { transform: translateY(-20px) rotate(-45deg); opacity: 0; }
  60% { transform: translateY(4px) rotate(-45deg); }
  100% { transform: translateY(0) rotate(-45deg); opacity: 1; }
}

@keyframes activePulseRing {
  0% { transform: rotate(45deg) scale(1); opacity: 0.8; }
  100% { transform: rotate(45deg) scale(2); opacity: 0; }
}

.btn-refresh-premium {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  width: 32px;
  height: 32px;
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
</style>
