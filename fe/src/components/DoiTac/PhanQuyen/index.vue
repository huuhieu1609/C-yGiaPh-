<template>
  <div class="row mb-3">
    <div class="col-12">
      <!-- Clean, transparent container to avoid double card borders -->
      <div class="border-0 bg-transparent">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div class="fw-bold text-dark fs-5">Cấu hình vai trò</div>
          <div class="text-muted small">Cấp phát và thu hồi vai trò thành viên</div>
        </div>
        <div class="p-3 mb-4 rounded-3 border-0" style="background: #f8fafc; border: 1px solid #e2e8f0 !important;">
          <div class="row g-3">
            <div class="col-md-5">
              <label class="form-label fw-bold text-dark small">Chọn thành viên trong chi nhánh</label>
              <select class="form-select border-0 shadow-sm" v-model="selectedMemberId" style="border-radius: 10px; padding: 10px 14px;">
                <option :value="null">-- Chọn thành viên --</option>
                <option v-for="m in membersInBranch" :key="m.id" :value="m.id">{{ m.ho_ten }} (Đời {{ m.doi_thu }})</option>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label fw-bold text-dark small">Chọn phạm vi quản lý</label>
              <select class="form-select border-0 shadow-sm" v-model="chiNhanhId" style="border-radius: 10px; padding: 10px 14px;">
                <option :value="null">-- Toàn hệ dòng họ --</option>
                <option v-for="c in chiNhanhs" :key="c.id" :value="c.id">{{ c.ten_chi }}</option>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-dark w-100 fw-bold shadow-sm" :disabled="!selectedMemberId" @click="refreshMemberRoles" style="border-radius: 10px; padding: 10px 14px; transition: all 0.2s;">
                <i class="bx bx-refresh me-1"></i> Tải vai trò
              </button>
            </div>
          </div>
        </div>

        <hr class="opacity-50 my-4">

        <div v-if="roles.length === 0" class="text-center text-muted py-5 card border-0 shadow-sm rounded-4">
          <i class="bx bx-shield-x fs-1 text-muted mb-2"></i>
          <p class="mb-0">Không tìm thấy vai trò nào trong hệ thống</p>
        </div>

        <div class="row g-3" v-else>
          <div v-for="r in roles" :key="r.id" class="col-md-6 col-12">
            <div class="card role-card shadow-sm h-100 border-0 p-3" :class="{ 'role-assigned': isAssigned(r.id) }">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div style="max-width: 70%;">
                  <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">{{ r.display_name || r.name }}</h6>
                  <p class="text-muted mb-0 small" style="font-size: 12px; line-height: 1.4;">{{ r.description || 'Không có mô tả chi tiết' }}</p>
                </div>
                <div>
                  <button v-if="isAssigned(r.id)" class="btn btn-sm btn-outline-danger btn-role-action px-3 fw-bold" @click="revokeRole(r.id)">
                    <i class="bx bx-x-circle me-1"></i>Thu hồi
                  </button>
                  <button v-else class="btn btn-sm btn-success btn-role-action px-3 text-white fw-bold" @click="assignRole(r.id)">
                    <i class="bx bx-check-circle me-1"></i>Gán quyền
                  </button>
                </div>
              </div>
              <div class="small mt-2 border-top pt-2">
                <span class="fw-bold text-secondary d-block mb-1.5" style="font-size: 12px;">
                  <i class="bx bx-key me-1"></i>Quyền hạn được cấp:
                </span>
                <div v-if="r.permissions && r.permissions.length" class="d-flex flex-wrap gap-1">
                  <span v-for="p in r.permissions" :key="p.id" class="badge-permission">
                    {{ p.display_name || p.name }}
                  </span>
                </div>
                <span v-else class="text-muted italic ms-1 small">—</span>
              </div>
            </div>
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
  name: 'PhanQuyen',
  props: {
    allMembers: { type: Array, required: false, default: () => [] },
    chiNhanhs: { type: Array, required: false, default: () => [] },
    selectedChiNhanhId: { type: [Number, String], required: false }
  },
  data() {
    return {
      roles: [],
      selectedMemberId: null,
      memberRoles: [],
      chiNhanhId: this.selectedChiNhanhId || null
    };
  },
  computed: {
    membersInBranch() {
      if (!this.allMembers || !this.allMembers.length) return [];
      if (!this.chiNhanhId) return this.allMembers.filter(m => m.loai_quan_he === 'Chính');
      return this.allMembers.filter(m => m.chi_nhanh_id == this.chiNhanhId && m.loai_quan_he === 'Chính');
    }
  },
  watch: {
    selectedChiNhanhId(v) { this.chiNhanhId = v; }
  },
  mounted() {
    this.loadRoles();
  },
  methods: {
    loadRoles() {
      axios.get('http://127.0.0.1:8000/api/roles/list').then(res => {
        if (res.data && res.data.data) this.roles = res.data.data;
      }).catch(err => {
        console.error(err); toastr.error('Không tải được danh sách vai trò');
      });
    },
    refreshMemberRoles() {
      if (!this.selectedMemberId) return; 
      axios.get(`http://127.0.0.1:8000/api/member/${this.selectedMemberId}/roles`).then(res => {
        this.memberRoles = res.data.data || [];
        toastr.success('Đã tải vai trò thành viên');
      }).catch(err => { console.error(err); toastr.error('Không lấy được vai trò của thành viên'); });
    },
    isAssigned(roleId) {
      return this.memberRoles.some(mr => mr.role_id == roleId || (mr.role && mr.role.id == roleId));
    },
    assignRole(roleId) {
      if (!this.selectedMemberId) { toastr.warning('Chọn thành viên trước'); return; }
      axios.post(`http://127.0.0.1:8000/api/member/${this.selectedMemberId}/assign-role`, { role_id: roleId, chi_nhanh_id: this.chiNhanhId }, { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` }})
        .then(res => {
          toastr.success('Gán quyền thành công');
          this.refreshMemberRoles();
        }).catch(err => { console.error(err); toastr.error('Gán quyền thất bại'); });
    },
    revokeRole(roleId) {
      if (!this.selectedMemberId) { toastr.warning('Chọn thành viên trước'); return; }
      axios.post(`http://127.0.0.1:8000/api/member/${this.selectedMemberId}/revoke-role`, { role_id: roleId, chi_nhanh_id: this.chiNhanhId }, { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` }})
        .then(res => {
          toastr.success('Thu hồi quyền thành công');
          this.refreshMemberRoles();
        }).catch(err => { console.error(err); toastr.error('Thu hồi quyền thất bại'); });
    }
  }
};
</script>

<style scoped>
.role-card {
  background: #ffffff !important;
  border: 1px solid rgba(0, 0, 0, 0.06) !important;
  border-radius: 16px !important;
  transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
  position: relative;
}
.role-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; bottom: 0; width: 0px;
  background: #10b981;
  transition: all 0.3s ease;
  border-radius: 16px 0 0 16px;
}
.role-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06) !important;
  border-color: rgba(212, 175, 55, 0.2) !important;
}
.role-assigned {
  background: #f0fdf4 !important;
  border-color: rgba(16, 185, 129, 0.2) !important;
}
.role-assigned::before {
  width: 4px;
}
.btn-role-action {
  border-radius: 30px !important;
  font-size: 12px !important;
  padding: 6px 14px !important;
  transition: all 0.2s ease;
}
.badge-permission {
  display: inline-block;
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 4px 8px;
  font-size: 11px;
  font-weight: 500;
  margin-right: 4px;
  margin-bottom: 4px;
}
</style>
