<template>
  <div class="container-fluid px-0">
    <div class="row g-4">
      <div class="col-md-7">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16">
          <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="fw-bold mb-0 theme-text-main">
              <i class="bx bx-calendar-event me-2 text-warning"></i>Quản Lý Sự Kiện Dòng Họ
            </h5>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <!-- Select Tree/Branch Filter -->
              <div style="width: 170px;">
                <select class="form-select premium-select fw-bold py-1 px-3 shadow-none border-2" v-model="selectedChiNhanhId">
                  <option value="all">-- Tất cả các cây --</option>
                  <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                </select>
              </div>
              <button class="btn btn-refresh-premium rounded-circle d-flex align-items-center justify-content-center" @click="refreshData" :disabled="isLoading" title="Làm mới dữ liệu">
                <i class="bx bx-sync fs-5 text-warning" :class="{'bx-spin': isLoading}"></i>
              </button>
              <button class="btn btn-sm btn-gradient-orange radius-30 px-3 fw-bold shadow-sm" @click="openAddModal">
                <i class="bx bx-plus-circle me-1"></i> Tạo Sự Kiện Mới
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive table-container-premium mx-4 mb-4">
              <table class="table align-middle mb-0 text-nowrap table-hover">
                <thead>
                  <tr class="text-secondary text-uppercase small">
                    <th class="ps-4">Sự kiện</th>
                    <th>Loại</th>
                    <th>Thời gian</th>
                    <th>Địa điểm</th>
                    <th class="pe-4 text-end">Hành động</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="filteredEvents.length === 0">
                    <td colspan="5" class="text-center py-5 text-muted bg-transparent fw-medium">
                      <i class="bx bx-calendar d-block display-4 opacity-25 mb-2 text-warning"></i>
                      Không tìm thấy sự kiện nào cho cây gia phả này.
                    </td>
                  </tr>
                  <tr v-for="ev in filteredEvents" :key="ev.id" class="table-row-premium" :class="{'row-active': selectedEventId === ev.id}" @click="selectEvent(ev)">
                    <td class="ps-4 bg-transparent">
                      <div>
                        <strong class="theme-text-main d-block font-bold mb-1">{{ ev.tieu_de }}</strong>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                          <small class="text-secondary text-truncate d-inline-block" style="max-width: 140px;">{{ ev.noi_dung }}</small>
                          <span class="badge bg-light text-dark font-9 py-0.5 px-2" style="border: 1px solid rgba(0,0,0,0.1); border-radius: 4px !important;" v-if="ev.chi_nhanh_id">
                            <i class="bx bx-git-branch text-warning"></i> {{ getEventBranchName(ev.chi_nhanh_id) }}
                          </span>
                          <span class="badge bg-light text-secondary font-9 py-0.5 px-2" style="border: 1px solid rgba(0,0,0,0.1); border-radius: 4px !important;" v-else>
                            Toàn họ
                          </span>
                        </div>
                      </div>
                    </td>
                    <td class="bg-transparent">
                      <span class="badge badge-premium-type" :class="evTypeBadgeClass(ev.loai)">
                        {{ ev.loai }}
                      </span>
                    </td>
                    <td class="bg-transparent text-secondary font-medium small">{{ fmtDateTime(ev.ngay_to_chuc) }}</td>
                    <td class="bg-transparent theme-text-main font-medium small">{{ ev.dia_diem || 'Không rõ' }}</td>
                    <td class="pe-4 text-end bg-transparent" @click.stopPropagation>
                      <div class="d-flex justify-content-end gap-2">
                        <button class="btn btn-sm btn-action-edit" @click="openEditModal(ev)" title="Sửa">
                          <i class="bx bx-edit-alt"></i>
                        </button>
                        <button class="btn btn-sm btn-action-delete" @click="handleDelete(ev.id)" title="Xóa">
                          <i class="bx bx-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-5">
        <div class="card genealogy-main-card border-0 shadow-sm radius-16 h-100">
          <div class="card-header bg-transparent border-0 py-4 px-4">
            <h5 class="fw-bold mb-0 theme-text-main">
              <i class="bx bx-check-shield me-2 text-warning"></i>Danh Sách Đăng Ký / Điểm Danh
            </h5>
          </div>
          <div class="card-body p-4 pt-0">
            <div v-if="!selectedEventId" class="text-center py-5 text-muted">
              <i class="bx bx-pointer d-block display-4 opacity-25 mb-2 text-warning"></i>
              Chọn một sự kiện bên hành lang trái để xem danh sách đăng ký.
            </div>
            <div v-else>
              <div class="event-summary mb-4 p-3 rounded-4 bg-adaptive-input border">
                <h6 class="fw-bold theme-text-main mb-2 font-bold">{{ selectedEventName }}</h6>
                <span class="badge bg-orange-premium">Tổng: {{ participants.length }} thành viên</span>
              </div>

              <div class="mb-3 d-flex gap-2">
                <input type="text" class="form-control premium-input" v-model="searchQuery" placeholder="Tìm thành viên...">
                <select class="form-select premium-select w-auto" v-model="filterDoi">
                  <option value="">Mọi đời</option>
                  <option v-for="d in maxDoi" :key="d" :value="d">Đời thứ {{ d }}</option>
                </select>
              </div>

              <div class="participants-list-wrapper border-adaptive p-2" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm align-middle mb-0">
                  <thead>
                    <tr class="small text-secondary">
                      <th class="ps-2">Thành viên</th>
                      <th>Giới tính</th>
                      <th class="text-center">Đời</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="filteredParticipants.length === 0">
                      <td colspan="3" class="text-center py-4 text-muted bg-transparent small fw-medium">
                        Chưa có thành viên nào đăng ký tham dự.
                      </td>
                    </tr>
                    <tr v-for="p in filteredParticipants" :key="p.id" class="table-row-premium">
                      <td class="bg-transparent ps-2">
                        <div class="d-flex align-items-center gap-2">
                          <img :src="p.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(p.ho_ten)}&background=f97316&color=fff&size=30`" class="rounded-circle border border-2 border-white shadow-sm" width="26" height="26" />
                          <strong class="theme-text-main small font-bold">{{ p.ho_ten }}</strong>
                        </div>
                      </td>
                      <td class="bg-transparent"><span class="small text-secondary font-medium">{{ p.gioi_tinh }}</span></td>
                      <td class="bg-transparent text-center"><span class="badge badge-generation font-monospace">Đời {{ p.doi_thu }}</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- MODAL KHỞI TẠO / CẬP NHẬT SỰ KIỆN PREMIUM -->
    <div class="modal fade" id="eventCrudModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-modal border-0 shadow-lg bg-adaptive-card overflow-hidden">
          
          <!-- Header Modal cực đỉnh với dải màu Gradient chuyển động -->
          <div class="modal-header border-0 premium-modal-header text-white p-4 position-relative">
            <div class="header-overlay"></div>
            <div class="position-relative z-index-2 d-flex align-items-center gap-3 w-100">
              <div class="header-icon-box shadow-sm">
                <i class="bx bx-calendar-event fs-3 text-warning"></i>
              </div>
              <div class="flex-grow-1">
                <h5 class="modal-title fw-bold text-white mb-1">
                  {{ isEditing ? 'Cập Nhật Thiết Lập Sự Kiện' : 'Khởi Tạo Sự Kiện Dòng Họ Mới' }}
                </h5>
                <p class="text-white-50 small mb-0">Quản lý hoạt động cúng tế, họp mặt và ngày kỷ niệm dòng họ.</p>
              </div>
              <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>

          <!-- Body Modal với bố cục 2 cột (hoặc grouped) -->
          <div class="modal-body p-4 text-start bg-adaptive-card">
            <div class="row g-4">
              
              <!-- Cột trái: Thông tin cơ bản -->
              <div class="col-lg-6">
                <div class="form-section-card p-3 rounded-4 border-adaptive mb-3">
                  <h6 class="fw-bold mb-3 theme-text-main d-flex align-items-center">
                    <i class="bx bx-info-circle text-orange-premium me-2"></i>Thông Tin Cơ Bản
                  </h6>
                  
                  <div class="mb-3">
                    <label class="form-label-premium">
                      <i class="bx bx-edit-alt me-1"></i>Tiêu đề sự kiện <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control premium-input-glow" v-model="form.tieu_de" placeholder="Ví dụ: Giỗ Tổ Cuối Năm 2026">
                  </div>
                  
                  <div class="row g-2">
                    <div class="col-md-6">
                      <label class="form-label-premium">
                        <i class="bx bx-grid-alt me-1"></i>Phân loại danh mục
                      </label>
                      <select class="form-select premium-select-glow" v-model="form.loai">
                        <option value="Giỗ tổ">Giỗ tổ</option>
                        <option value="Họp họ">Họp họ</option>
                        <option value="Cưới hỏi">Cưới hỏi</option>
                        <option value="Tang lễ">Tang lễ</option>
                      </select>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label-premium">
                        <i class="bx bx-time me-1"></i>Thời gian tổ chức <span class="text-danger" v-if="!form.is_lunar">*</span>
                      </label>
                      <input type="datetime-local" class="form-control premium-input-glow" v-model="form.ngay_to_chuc" :disabled="form.is_lunar">
                    </div>
                  </div>

                  <div class="mt-3">
                    <div class="form-check form-switch mb-2">
                      <input class="form-check-input" type="checkbox" id="is_lunar_event" v-model="form.is_lunar" :true-value="true" :false-value="false">
                      <label class="form-check-label fw-bold text-secondary" for="is_lunar_event">
                        <i class="bx bx-moon me-1 text-warning"></i> Sự kiện Âm lịch
                      </label>
                    </div>
                  </div>

                  <div class="card bg-light border border-dashed p-3 radius-8 mt-2" v-if="form.is_lunar">
                    <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-1">
                      <i class="bx bx-calendar-event text-warning fs-5"></i> Ngày tổ chức Âm lịch
                    </h6>
                    <div class="row g-2">
                      <div class="col-md-4 col-6">
                        <label class="form-label small text-muted mb-1">Ngày AL <span class="text-danger">*</span></label>
                        <select class="form-select radius-8 border-2 shadow-none text-dark" v-model="form.ngay_al_ngay">
                          <option :value="null">--</option>
                          <option v-for="d in 30" :key="d" :value="d">{{ d }}</option>
                        </select>
                      </div>
                      <div class="col-md-4 col-6">
                        <label class="form-label small text-muted mb-1">Tháng AL <span class="text-danger">*</span></label>
                        <select class="form-select radius-8 border-2 shadow-none text-dark" v-model="form.ngay_al_thang">
                          <option :value="null">--</option>
                          <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
                        </select>
                      </div>
                      <div class="col-md-4 col-12 d-flex align-items-end">
                        <div class="form-check mb-2 ms-2">
                          <input class="form-check-input" type="checkbox" id="nhuan_event" v-model="form.ngay_al_nhuan" :true-value="true" :false-value="false">
                          <label class="form-check-label fw-semibold text-dark" for="nhuan_event">Tháng nhuận</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-section-card p-3 rounded-4 border-adaptive">
                  <h6 class="fw-bold mb-3 theme-text-main d-flex align-items-center">
                    <i class="bx bx-globe text-orange-premium me-2"></i>Phạm Vi & Liên Kết
                  </h6>
                  
                  <div>
                    <label class="form-label-premium">
                      <i class="bx bx-git-branch me-1"></i>Liên kết Chi Nhánh
                    </label>
                    <select class="form-select premium-select-glow" v-model="form.chi_nhanh_id">
                      <option :value="null">-- Sự kiện công cộng (Toàn hệ) --</option>
                      <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">{{ cn.ten_chi }}</option>
                    </select>
                    <small class="text-muted d-block mt-2">
                      <i class="bx bx-info-circle me-1"></i>Liên kết chi nhánh giúp lọc thành viên đăng ký và gửi thông báo chuẩn xác.
                    </small>
                  </div>
                </div>
              </div>

              <!-- Cột phải: Chi tiết và địa điểm -->
              <div class="col-lg-6">
                <div class="form-section-card p-3 rounded-4 border-adaptive h-100 d-flex flex-column">
                  <h6 class="fw-bold mb-3 theme-text-main d-flex align-items-center">
                    <i class="bx bx-detail text-orange-premium me-2"></i>Chi Tiết Sự Kiện
                  </h6>
                  
                  <div class="mb-3">
                    <label class="form-label-premium">
                      <i class="bx bx-map-pin me-1"></i>Địa điểm diễn ra
                    </label>
                    <input type="text" class="form-control premium-input-glow" v-model="form.dia_diem" placeholder="Ví dụ: Nhà thờ tổ hoặc Khách sạn Hoa Sen">
                  </div>
                  
                  <div class="mb-0 flex-grow-1 d-flex flex-column">
                    <label class="form-label-premium">
                      <i class="bx bx-message-detail me-1"></i>Nội dung chi tiết / Thông báo
                    </label>
                    <textarea class="form-control premium-textarea-glow flex-grow-1" rows="6" v-model="form.noi_dung" placeholder="Kế hoạch chi tiết, thời gian biểu, phân công công đức lộc lá..."></textarea>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- Footer Modal với phím bóng bẩy -->
          <div class="modal-footer border-0 p-4 bg-adaptive-card d-flex justify-content-end gap-3 position-relative z-index-2">
            <button class="btn btn-premium-cancel px-4 radius-30 fw-semibold" data-bs-dismiss="modal">
              <i class="bx bx-x me-1"></i> Hủy Bỏ
            </button>
            <button class="btn btn-gradient-orange text-white px-5 radius-30 fw-bold border-0 shadow-premium" @click="saveEvent">
              <i class="bx bx-check-double me-1 fs-5"></i> {{ isEditing ? 'Cập Nhật Ngay' : 'Tạo Sự Kiện' }}
            </button>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
  name: 'QuanLySuKien',
  data() {
    return {
      events: [],
      participants: [],
      selectedEventId: null,
      selectedEventName: '',
      selectedChiNhanhId: 'all',
      form: {
        id: null,
        tieu_de: '',
        noi_dung: '',
        ngay_to_chuc: '',
        dia_diem: '',
        loai: 'Giỗ tổ',
        is_lunar: false,
        ngay_al_ngay: null,
        ngay_al_thang: null,
        ngay_al_nam: null,
        ngay_al_nhuan: false
      },
      listChiNhanh: [],
      searchQuery: '',
      filterDoi: '',
      isEditing: false,
      modal: null,
      isLoading: false
    };
  },
  computed: {
    filteredEvents() {
      if (this.selectedChiNhanhId === 'all') {
        return this.events;
      }
      return this.events.filter(e => e.chi_nhanh_id == this.selectedChiNhanhId);
    },
    filteredParticipants() {
      let list = this.participants;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(p => p.ho_ten.toLowerCase().includes(q));
      }
      if (this.filterDoi) {
        list = list.filter(p => p.doi_thu == this.filterDoi);
      }
      return list;
    },
    maxDoi() {
      if (!this.participants.length) return 0;
      return Math.max(...this.participants.map(p => p.doi_thu));
    }
  },
  watch: {
    selectedChiNhanhId() {
      const list = this.filteredEvents;
      if (list.length > 0) {
        this.selectEvent(list[0]);
      } else {
        this.selectedEventId = null;
        this.selectedEventName = '';
        this.participants = [];
      }
    }
  },
  mounted() {
    this.loadEvents();
    this.loadChiNhanh();
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('eventCrudModal'));
    }
  },
  methods: {
    getHeaders() {
      return { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } };
    },
    loadEvents() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.events = res.data.data;
          toastr.info('Đã tải thành công ' + this.events.length + ' sự kiện.');
          if (this.events.length > 0 && !this.selectedEventId) {
            this.selectEvent(this.events[0]);
          }
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể lấy dữ liệu sự kiện.');
      })
      .finally(() => {
        this.isLoading = false;
      });
    },
    refreshData() {
      this.isLoading = true;
      Promise.all([
        axios.get('http://127.0.0.1:8000/api/su-kien/get-data', this.getHeaders()),
        axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
      ]).then(([resEvents, resBranch]) => {
        if (resEvents.data.status) {
          this.events = resEvents.data.data;
          if (this.events.length > 0) {
            const stillExists = this.events.find(x => x.id === this.selectedEventId);
            this.selectEvent(stillExists || this.events[0]);
          } else {
            this.selectedEventId = null;
            this.participants = [];
          }
        }
        if (resBranch.data.status) {
          this.listChiNhanh = resBranch.data.data;
        }
        toastr.success('Dữ liệu sự kiện đã được làm mới!');
      }).catch(() => {
        toastr.error('Lỗi khi tải lại dữ liệu sự kiện.');
      }).finally(() => {
        this.isLoading = false;
      });
    },
    loadChiNhanh() {
      axios.get('http://127.0.0.1:8000/api/chi-nhanh/get-data', this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.listChiNhanh = res.data.data;
          if (!this.form.chi_nhanh_id && this.listChiNhanh.length) {
            this.form.chi_nhanh_id = this.listChiNhanh[0].id;
          }
        } else {
          toastr.error(res.data.message || 'Không thể tải danh sách chi nhánh.');
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Lỗi kết nối khi tải danh sách chi nhánh.');
      });
    },
    selectEvent(ev) {
      this.selectedEventId = ev.id;
      this.selectedEventName = ev.tieu_de;
      this.loadParticipants(ev.id);
    },
    loadParticipants(suKienId) {
      axios.get(`http://127.0.0.1:8000/api/su-kien/get-participants/${suKienId}`, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          this.participants = res.data.data;
        }
      });
    },
    openAddModal() {
      this.isEditing = false;
      this.form = {
        id: null,
        tieu_de: '',
        noi_dung: '',
        ngay_to_chuc: '',
        dia_diem: '',
        loai: 'Giỗ tổ',
        chi_nhanh_id: (this.listChiNhanh[0] && this.listChiNhanh[0].id) || null,
        is_lunar: false,
        ngay_al_ngay: null,
        ngay_al_thang: null,
        ngay_al_nam: null,
        ngay_al_nhuan: false
      };
      this.modal.show();
    },
    openEditModal(ev) {
      this.isEditing = true;
      this.form = {
        id: ev.id,
        tieu_de: ev.tieu_de,
        noi_dung: ev.noi_dung || '',
        ngay_to_chuc: ev.ngay_to_chuc ? ev.ngay_to_chuc.substring(0, 16) : '',
        dia_diem: ev.dia_diem || '',
        loai: ev.loai,
        chi_nhanh_id: ev.chi_nhanh_id || null,
        is_lunar: ev.is_lunar ? true : false,
        ngay_al_ngay: ev.ngay_al_ngay || null,
        ngay_al_thang: ev.ngay_al_thang || null,
        ngay_al_nam: ev.ngay_al_nam || null,
        ngay_al_nhuan: ev.ngay_al_nhuan ? true : false
      };
      this.modal.show();
    },
    saveEvent() {
      if (!this.form.tieu_de) {
        toastr.warning('Vui lòng điền tiêu đề sự kiện!');
        return;
      }
      if (this.form.is_lunar) {
        if (!this.form.ngay_al_ngay || !this.form.ngay_al_thang) {
          toastr.warning('Vui lòng điền đầy đủ ngày/tháng Âm lịch!');
          return;
        }
      } else {
        if (!this.form.ngay_to_chuc) {
          toastr.warning('Vui lòng điền thời gian tổ chức sự kiện!');
          return;
        }
      }
      const url = this.isEditing 
        ? 'http://127.0.0.1:8000/api/su-kien/update'
        : 'http://127.0.0.1:8000/api/su-kien/create';

      axios.post(url, this.form, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          this.modal.hide();
          this.loadEvents();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Lỗi khi lưu sự kiện.');
      });
    },
    handleDelete(id) {
      if (!confirm('Bạn có chắc chắn muốn xóa sự kiện này không?')) return;
      axios.post('http://127.0.0.1:8000/api/su-kien/delete', { id }, this.getHeaders())
      .then(res => {
        if (res.data.status) {
          toastr.success(res.data.message);
          if (this.selectedEventId === id) {
            this.selectedEventId = null;
            this.selectedEventName = '';
            this.participants = [];
          }
          this.loadEvents();
        } else {
          toastr.error(res.data.message);
        }
      })
      .catch(err => {
        toastr.error(err.response?.data?.message || 'Không thể xóa sự kiện.');
      });
    },
    evTypeBadgeClass(loai) {
      if (loai === 'Giỗ tổ') return 'badge-gioto';
      if (loai === 'Họp họ') return 'badge-hopho';
      if (loai === 'Cưới hỏi') return 'badge-cuoihoi';
      return 'badge-tangle';
    },
    fmtDateTime(d) {
      if (!d) return '';
      const dt = new Date(d);
      return dt.toLocaleDateString('vi-VN') + ' ' + dt.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
    },
    getEventBranchName(branchId) {
      if (!this.listChiNhanh || !this.listChiNhanh.length) return 'Cây gia phả';
      const branch = this.listChiNhanh.find(c => c.id === branchId);
      return branch ? branch.ten_chi : 'Cây gia phả';
    }
  }
};
</script>

<style scoped>
/* ─── HỆ THỐNG PHÔNG NỀN VÀ KHUNG CARD THÍCH ỨNG CHUẨN ─── */
.genealogy-main-card {
  background: var(--card-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 16px !important;
}
.theme-text-main { color: var(--text-main) !important; }
.font-bold { font-weight: 700; }
.font-medium { font-weight: 500; }
.bg-adaptive-input { background: var(--input-bg) !important; border: 1px solid var(--border-color) !important; }
.bg-adaptive-card { background: var(--card-bg) !important; }
.border-adaptive { border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }

/* Thẻ Summary và Ô Tìm kiếm viên thuốc */
.event-summary {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  border-radius: 14px !important;
}

.premium-input, .premium-select {
  border-radius: 30px !important;
  border: 1px solid var(--border-color) !important;
  padding: 8px 16px !important;
  font-size: 14px;
  background-color: var(--input-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.2s ease;
}
.premium-input:focus, .premium-select:focus {
  border-color: #f97316 !important;
  background-color: var(--card-bg) !important;
}

/* KHUNG BẢNG PREMIUM ADAPTIVE */
.table-container-premium {
  border: 1px solid var(--border-color);
  border-radius: 12px; overflow: hidden;
}
.table thead th {
  background-color: var(--input-bg) !important;
  color: var(--text-sub) !important;
  font-weight: 600; font-size: 12px;
  border-bottom: 1px solid var(--border-color) !important;
  padding: 14px 16px !important;
}
.table-row-premium {
  border-bottom: 1px solid var(--border-color) !important;
  transition: background-color 0.2s ease;
  cursor: pointer;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }

/* Kích hoạt dòng sự kiện đang chọn */
.table-row-premium.row-active {
  background-color: rgba(249, 115, 22, 0.06) !important;
}
[data-theme="dark"] .table-row-premium.row-active {
  background-color: rgba(249, 115, 22, 0.12) !important;
}

/* ─── HỆ THỐNG BADGE PHÂN LOẠI MỜ PASTEL ─── */
.badge { padding: 6px 14px !important; font-size: 11px !important; font-weight: 700 !important; border-radius: 30px !important; }
.bg-orange-premium { background-color: #f97316 !important; color: white !important; }

/* Badge loại danh mục */
.badge-premium-type { border-radius: 6px !important; font-size: 11px !important; }
.badge-gioto { background-color: rgba(245, 158, 11, 0.08) !important; color: #f59e0b !important; border: 1px solid rgba(245, 158, 11, 0.15); }
.badge-hopho { background-color: rgba(59, 130, 246, 0.08) !important; color: #3b82f6 !important; border: 1px solid rgba(59, 130, 246, 0.15); }
.badge-cuoihoi { background-color: rgba(16, 185, 129, 0.08) !important; color: #10b981 !important; border: 1px solid rgba(16, 185, 129, 0.15); }
.badge-tangle { background-color: rgba(156, 163, 175, 0.08) !important; color: #9ca3af !important; border: 1px solid rgba(156, 163, 175, 0.15); }
.badge-generation { background-color: var(--input-bg) !important; color: var(--text-main) !important; border: 1px solid var(--border-color); }

/* NÚT TƯƠNG TÁC CHỨC NĂNG */
.btn-action-edit, .btn-action-delete {
  background: transparent !important; padding: 5px 10px !important; font-size: 14px; border-radius: 8px !important; transition: all 0.2s ease;
}
.btn-action-edit { border: 1px solid rgba(245, 158, 11, 0.3) !important; color: #f59e0b !important; }
.btn-action-edit:hover { background: #f59e0b !important; color: white !important; }

.btn-action-delete { border: 1px solid rgba(239, 68, 68, 0.3) !important; color: #ef4444 !important; }
.btn-action-delete:hover { background: #ef4444 !important; color: white !important; }

.btn-gradient-orange {
  background: linear-gradient(135deg, #f43f5e 0%, #f97316 100%) !important; color: white !important; border: none;
}
.radius-30 { border-radius: 30px !important; }
.radius-24 { border-radius: 24px !important; }

/* MODAL CONTAINER HỆ THỐNG */
.bg-dark-premium { background: linear-gradient(145deg, #1a1c2e 0%, #141625 100%) !important; border-bottom: 1px solid var(--border-color); }
.premium-textarea {
  border-radius: 14px !important; border: 1px solid var(--border-color) !important;
  padding: 12px 14px !important; font-size: 14px; background-color: var(--input-bg) !important; color: var(--text-main) !important; box-shadow: none !important;
}
.premium-textarea:focus { border-color: #f97316 !important; background-color: var(--card-bg) !important; }

/* ─── PREMIUM MODAL STYLING SYSTEM ─── */
.premium-modal {
  border-radius: 24px !important;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15) !important;
  border: 1px solid var(--border-color) !important;
}

.premium-modal-header {
  background: linear-gradient(135deg, #111827 0%, #1e293b 100%) !important;
  padding: 24px 28px !important;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}
[data-theme="dark"] .premium-modal-header {
  background: linear-gradient(135deg, #090d16 0%, #111827 100%) !important;
}

.header-overlay {
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 80% 20%, rgba(249, 115, 22, 0.08) 0%, transparent 60%);
  pointer-events: none;
}

.header-icon-box {
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.form-section-card {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01) !important;
  transition: border-color 0.3s;
}
.form-section-card:hover {
  border-color: rgba(249, 115, 22, 0.25) !important;
}
.table-row-premium:hover { background-color: var(--input-bg) !important; }

.form-label-premium {
  font-size: 12.5px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--text-sub) !important;
  margin-bottom: 8px;
  display: block;
}

/* INPUT GLOW STYLINGS */
.premium-input-glow, .premium-select-glow, .premium-textarea-glow {
  border-radius: 12px !important;
  border: 1px solid var(--border-color) !important;
  padding: 10px 16px !important;
  font-size: 14px;
  background-color: var(--card-bg) !important;
  color: var(--text-main) !important;
  box-shadow: none !important;
  transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}
.premium-input-glow:focus, .premium-select-glow:focus, .premium-textarea-glow:focus {
  border-color: #f97316 !important;
  box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.12) !important;
  transform: translateY(-1px);
  background-color: var(--card-bg) !important;
}

.text-orange-premium {
  color: #f97316 !important;
}

/* CANCEL BUTTON GLASS */
.btn-premium-cancel {
  background: transparent !important;
  border: 1px solid var(--border-color) !important;
  color: var(--text-sub) !important;
  border-radius: 30px !important;
  transition: all 0.25s ease;
}
.btn-premium-cancel:hover {
  background: var(--input-bg) !important;
  color: var(--text-main) !important;
  border-color: var(--text-sub) !important;
}

/* SHADOW GLOW ON PRIMARY */
.shadow-premium {
  box-shadow: 0 6px 20px rgba(244, 63, 94, 0.3) !important;
  transition: all 0.3s ease;
}
.shadow-premium:hover {
  box-shadow: 0 8px 25px rgba(244, 63, 94, 0.45) !important;
  transform: translateY(-1.5px);
}
.premium-textarea:focus { border-color: #f97316 !important; background-color: var(--card-bg) !important; }

.btn-refresh-premium {
  background: var(--input-bg) !important;
  border: 1px solid var(--border-color) !important;
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
</style>