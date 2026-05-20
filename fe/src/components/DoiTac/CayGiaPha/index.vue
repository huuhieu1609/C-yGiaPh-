<template>
  <div class="genealogy-container py-3">
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
      <!-- Header & Toolbar -->
      <div class="card-header bg-white p-4 border-bottom">
        <div class="row align-items-center gap-3 gap-md-0">
          <div class="col-md-4">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
              <div
                class="icon-box bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px"
              >
                <i class="bx bx-git-branch fs-5"></i>
              </div>
              Trực Quan Cây Gia Phả
            </h5>
          </div>
          <div
            class="col-md-8 d-flex flex-wrap align-items-center justify-content-md-end gap-3"
          >
            <!-- Bộ lọc Chi nhánh -->
            <div class="branch-selector" style="min-width: 200px">
              <select
                class="form-select form-select-sm border-0 bg-light rounded-pill px-3 py-2 fw-medium"
                v-model="selectedChiNhanhId"
                @change="resetView"
              >
                <option v-for="cn in listChiNhanh" :key="cn.id" :value="cn.id">
                  {{ cn.ten_chi }}
                </option>
              </select>
            </div>

            <!-- Thanh tìm kiếm -->
            <div
              class="search-box position-relative d-none d-lg-block"
              style="width: 250px"
            >
              <input
                type="text"
                class="form-control form-control-sm rounded-pill ps-5 py-2 bg-light border-0"
                v-model="searchQuery"
                placeholder="Tìm tên thành viên..."
              />
              <i
                class="bx bx-search position-absolute top-50 translate-middle-y start-0 ms-3 text-muted fs-6"
              ></i>
            </div>

            <!-- Công cụ Zoom -->
            <div
              class="zoom-controls btn-group bg-light rounded-pill p-1 shadow-sm"
            >
              <button
                class="btn btn-sm btn-light rounded-circle zoom-btn"
                @click="zoomOut"
                title="Thu nhỏ"
              >
                <i class="bx bx-minus"></i>
              </button>
              <span
                class="d-flex align-items-center px-2 fw-bold text-muted small"
                style="min-width: 55px; justify-content: center"
              >
                {{ Math.round(zoom * 100) }}%
              </span>
              <button
                class="btn btn-sm btn-light rounded-circle zoom-btn"
                @click="zoomIn"
                title="Phóng to"
              >
                <i class="bx bx-plus"></i>
              </button>
              <button
                class="btn btn-sm btn-light rounded-circle zoom-btn ms-1"
                @click="resetView"
                title="Khôi phục góc nhìn"
              >
                <i class="bx bx-target-lock"></i>
              </button>
            </div>

            <!-- Nút Thêm Mới -->
            <button
              class="btn btn-primary rounded-pill px-4 py-2 fw-medium shadow-sm d-flex align-items-center gap-2"
              @click="openAddModal"
            >
              <i class="bx bx-plus fs-5"></i> Thêm thành viên
            </button>
          </div>
        </div>
      </div>

      <!-- Body (Canvas) -->
      <div class="card-body p-0 position-relative bg-grid">
        <!-- Trạng thái chưa có Chi nhánh -->
        <div
          v-if="listChiNhanh.length === 0"
          class="empty-state d-flex flex-column align-items-center justify-content-center text-center py-5"
        >
          <div
            class="empty-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-4"
            style="width: 120px; height: 120px"
          >
            <i
              class="bx bx-building-house text-muted opacity-50"
              style="font-size: 60px"
            ></i>
          </div>
          <h4 class="fw-bold text-dark">Chưa có thông tin Dòng Họ!</h4>
          <p class="text-muted max-w-md">
            Bạn cần khởi tạo Dòng Họ (Chi Nhánh) để bắt đầu xây dựng cây gia phả
            của mình.
          </p>
          <router-link
            to="/doi-tac/dong-ho"
            class="btn btn-primary rounded-pill px-5 py-2 mt-2 shadow-sm"
          >
            <i class="bx bx-plus-circle me-1"></i> Khởi Tạo Ngay
          </router-link>
        </div>

        <!-- Khu vực Tree Viewport -->
        <div
          v-else
          class="tree-viewport"
          ref="viewport"
          @mousedown="startPan"
          @mousemove="doPan"
          @mouseup="endPan"
          @mouseleave="endPan"
          :style="{ cursor: isPanning ? 'grabbing' : 'grab' }"
        >
          <div class="tree-canvas" :style="canvasStyle">
            <div class="tree" v-if="treeData.length">
              <ul>
                <TreeItem
                  v-for="member in treeData"
                  :key="member.id"
                  :member="member"
                  :listDoiTocHo="listDoiTocHo"
                  :searchQuery="searchQuery"
                  @edit="onEdit"
                />
              </ul>
            </div>
            <div
              v-else
              class="d-flex flex-column align-items-center justify-content-center h-100 mt-5 pt-5"
            >
              <div
                class="empty-icon bg-light rounded-circle d-flex align-items-center justify-content-center mb-3"
                style="width: 80px; height: 80px"
              >
                <i class="bx bx-git-repo-forked text-muted opacity-50 fs-1"></i>
              </div>
              <h5 class="fw-bold text-secondary">Cây gia phả đang trống</h5>
              <p class="text-muted small">
                Hãy bắt đầu bằng cách thêm Thủy Tổ.
              </p>
              <button
                class="btn btn-outline-primary rounded-pill px-4 mt-2"
                @click="openAddModal"
              >
                Thêm ngay
              </button>
            </div>
          </div>
        </div>

        <!-- Gợi ý điều khiển -->
        <div
          class="view-controls position-absolute bottom-0 start-50 translate-middle-x mb-4 px-4 py-2 bg-white bg-opacity-75 rounded-pill shadow-sm border small text-muted d-none d-md-flex align-items-center gap-3 backdrop-blur"
        >
          <span
            ><i class="bx bx-mouse fs-6 align-middle me-1"></i> Cuộn để thu
            phóng</span
          >
          <span class="border-start ps-3"
            ><i class="bx bx-move fs-6 align-middle me-1"></i> Kéo để di chuyển
            không gian</span
          >
        </div>
      </div>
    </div>

    <!-- Modal Thêm/Sửa Thành Viên -->
    <div
      class="modal fade"
      id="memberModal"
      tabindex="-1"
      aria-hidden="true"
      data-bs-backdrop="static"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
          <div
            class="modal-header border-bottom px-4 py-3"
            :class="isEditing ? 'bg-warning-subtle' : 'bg-primary-subtle'"
          >
            <h5
              class="modal-title fw-bold d-flex align-items-center gap-2"
              :class="isEditing ? 'text-dark' : 'text-primary'"
            >
              <i
                class="bx"
                :class="isEditing ? 'bx-edit-alt' : 'bx-user-plus'"
              ></i>
              {{
                isEditing
                  ? "Chỉnh Sửa Thông Tin Thành Viên"
                  : "Thêm Thành Viên Mới"
              }}
            </h5>
            <button
              type="button"
              class="btn-close shadow-none"
              data-bs-dismiss="modal"
            ></button>
          </div>
          <div class="modal-body p-4">
            <div class="row g-4">
              <!-- Avatar Upload -->
              <div class="col-12 text-center mb-2">
                <div class="position-relative d-inline-block">
                  <div
                    class="avatar-wrapper p-1 bg-white rounded-circle shadow-sm border"
                    :class="isEditing ? 'border-warning' : 'border-primary'"
                  >
                    <img
                      :src="
                        avatarPreview ||
                        currentMember.avatar ||
                        'https://ui-avatars.com/api/?name=' +
                          (currentMember.ho_ten || 'A') +
                          '&background=f3f4f6&color=475569'
                      "
                      class="rounded-circle object-fit-cover"
                      alt="Avatar"
                      width="100"
                      height="100"
                    />
                  </div>
                  <label
                    for="member-avatar-upload"
                    class="btn btn-sm rounded-circle position-absolute bottom-0 end-0 shadow"
                    :class="isEditing ? 'btn-warning' : 'btn-primary'"
                    style="
                      width: 35px;
                      height: 35px;
                      display: flex;
                      align-items: center;
                      justify-content: center;
                      cursor: pointer;
                      border: 2px solid #fff;
                    "
                    title="Đổi ảnh"
                  >
                    <i class="bx bx-camera text-white fs-6"></i>
                  </label>
                  <input
                    type="file"
                    id="member-avatar-upload"
                    @change="onAvatarChange"
                    class="d-none"
                    accept="image/*"
                  />
                </div>
              </div>

              <!-- Basic Info -->
              <div class="col-md-8">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Họ và Tên <span class="text-danger">*</span></label
                >
                <input
                  type="text"
                  class="form-control form-control-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.ho_ten"
                  placeholder="Ví dụ: Nguyễn Văn A"
                />
              </div>
              <div class="col-md-4">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Giới tính</label
                >
                <select
                  class="form-select form-select-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.gioi_tinh"
                >
                  <option value="Nam">Nam</option>
                  <option value="Nữ">Nữ</option>
                </select>
              </div>

              <div class="col-md-6">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Ngày sinh</label
                >
                <input
                  type="date"
                  class="form-control form-control-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.ngay_sinh"
                />
              </div>

              <div class="col-md-6">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Trạng thái</label
                >
                <div class="d-flex gap-2 h-100 align-items-center">
                  <input
                    type="radio"
                    class="btn-check"
                    value="Còn sống"
                    v-model="currentMember.trang_thai"
                    id="status-alive"
                  />
                  <label
                    class="btn btn-outline-success rounded-3 w-50"
                    for="status-alive"
                    >Còn sống</label
                  >

                  <input
                    type="radio"
                    class="btn-check"
                    value="Đã mất"
                    v-model="currentMember.trang_thai"
                    id="status-dead"
                  />
                  <label
                    class="btn btn-outline-secondary rounded-3 w-50"
                    for="status-dead"
                    >Đã mất</label
                  >
                </div>
              </div>

              <div
                class="col-md-6"
                v-if="currentMember.trang_thai === 'Đã mất'"
              >
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Ngày mất</label
                >
                <input
                  type="date"
                  class="form-control form-control-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.ngay_mat"
                />
              </div>

              <!-- Nhánh Gia đình -->
              <div class="col-12"><hr class="text-muted opacity-25" /></div>

              <div class="col-md-4">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Vai vế</label
                >
                <select
                  class="form-select form-select-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.loai_quan_he"
                >
                  <option value="Chính">Dòng máu (Con cháu)</option>
                  <option value="Vợ/Chồng">Phối ngẫu (Vợ/Chồng)</option>
                </select>
              </div>

              <div
                class="col-md-4"
                v-if="currentMember.loai_quan_he === 'Chính'"
              >
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Đời thứ</label
                >
                <select
                  class="form-select form-select-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.doi_thu"
                >
                  <option
                    v-for="doi in listDoiTocHo"
                    :key="doi.id"
                    :value="doi.so_doi"
                  >
                    Đời {{ doi.so_doi }} - {{ doi.ten_doi }}
                  </option>
                </select>
              </div>

              <div
                class="col-md-4"
                v-if="currentMember.loai_quan_he === 'Chính'"
              >
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Con của (Cha)</label
                >
                <select
                  class="form-select form-select-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.cha_id"
                >
                  <option :value="null" class="fw-bold text-primary">
                    --- Thủy Tổ (Người đầu tiên) ---
                  </option>
                  <option
                    v-for="m in allMembers"
                    :key="m.id"
                    :value="m.id"
                    v-show="
                      m.id !== currentMember.id && m.loai_quan_he === 'Chính'
                    "
                  >
                    {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                  </option>
                </select>
              </div>

              <div
                class="col-md-8"
                v-if="currentMember.loai_quan_he === 'Vợ/Chồng'"
              >
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Là Vợ/Chồng của</label
                >
                <select
                  class="form-select form-select-lg bg-light border-0 rounded-3 fs-6"
                  v-model="currentMember.spouse_of_id"
                >
                  <option :value="null">-- Chọn người phối ngẫu --</option>
                  <option
                    v-for="m in allMembers"
                    :key="m.id"
                    :value="m.id"
                    v-show="m.loai_quan_he === 'Chính'"
                  >
                    {{ m.ho_ten }} (Đời {{ m.doi_thu }})
                  </option>
                </select>
              </div>

              <!-- Notes -->
              <div class="col-12">
                <label
                  class="form-label fw-semibold text-secondary small text-uppercase"
                  >Ghi chú thêm</label
                >
                <textarea
                  class="form-control bg-light border-0 rounded-3 fs-6 p-3"
                  rows="3"
                  v-model="currentMember.ghi_chu"
                  placeholder="Thông tin về thành tựu, nơi an nghỉ, nghề nghiệp..."
                ></textarea>
              </div>
            </div>
          </div>
          <div
            class="modal-footer bg-light border-top-0 px-4 py-3 rounded-bottom-4"
          >
            <button
              v-if="isEditing"
              type="button"
              class="btn btn-outline-danger me-auto rounded-pill px-4"
              @click="handleDelete"
            >
              <i class="bx bx-trash me-1"></i> Xóa
            </button>
            <button
              type="button"
              class="btn btn-secondary rounded-pill px-4"
              data-bs-dismiss="modal"
            >
              Hủy
            </button>
            <button
              type="button"
              class="btn rounded-pill px-5 fw-bold shadow-sm"
              :class="isEditing ? 'btn-warning text-dark' : 'btn-primary'"
              @click="saveMember"
            >
              <i class="bx me-1" :class="isEditing ? 'bx-save' : 'bx-plus'"></i>
              {{ isEditing ? "Cập Nhật" : "Lưu Thành Viên" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import toastr from "toastr";

// ==========================================
// COMPONENT ĐỆ QUY VẼ CÂY (Dùng Template String dễ bảo trì hơn h() function)
// ==========================================
const TreeItem = {
  name: "TreeItem",
  props: ["member", "listDoiTocHo", "searchQuery"],
  emits: ["edit"],
  methods: {
    getTenDoi(doi_thu) {
      if (!this.listDoiTocHo || !this.listDoiTocHo.length) return "";
      const doi = this.listDoiTocHo.find((d) => d.so_doi == doi_thu);
      return doi && doi.ten_doi ? ` (${doi.ten_doi})` : "";
    },
    formatDate(dateString) {
      if (!dateString) return "";
      return new Date(dateString).toLocaleDateString("vi-VN");
    },
    isHighlighted(name) {
      if (!this.searchQuery) return false;
      return name.toLowerCase().includes(this.searchQuery.toLowerCase());
    },
    getAvatar(member) {
      return member.avatar
        ? member.avatar
        : `https://ui-avatars.com/api/?name=${member.ho_ten}&background=e2e8f0&color=475569`;
    },
  },
  template: `
        <li>
            <!-- Node Giả (Dành cho việc nhảy qua đời/thế hệ) -->
            <template v-if="member.isDummy">
                <div class="tree-node-group">
                    <div class="tree-dummy-node"></div>
                </div>
                <ul v-if="member.children && member.children.length">
                    <TreeItem v-for="child in member.children" :key="child.id" :member="child" :listDoiTocHo="listDoiTocHo" :searchQuery="searchQuery" @edit="$emit('edit', $event)" />
                </ul>
            </template>
            
            <!-- Node Thật -->
            <template v-else>
                <div class="tree-node-group">
                    <!-- Thành viên chính -->
                    <div class="tree-node-card" 
                         :class="[
                            'gen-' + ((member.doi_thu % 5) + 1), 
                            { 'principal': !member.cha_id, 'is-dead': member.trang_thai === 'Đã mất', 'highlighted': isHighlighted(member.ho_ten) }
                         ]" 
                         @click.stop="$emit('edit', member)">
                        <div class="node-avatar-container">
                            <img :src="getAvatar(member)" class="node-avatar shadow-sm" alt="avatar">
                        </div>
                        <div class="node-content">
                            <div class="node-name">{{ member.ho_ten }}</div>
                            <div class="node-date" v-if="member.ngay_sinh"><i class='bx bx-calendar-alt'></i> {{ formatDate(member.ngay_sinh) }}</div>
                            <div class="node-tag">Đời {{ member.doi_thu }}{{ getTenDoi(member.doi_thu) }}</div>
                        </div>
                        <div class="node-edit-btn"><i class="bx bx-pencil"></i></div>
                    </div>

                    <!-- Vợ / Chồng -->
                    <template v-if="member.spouses && member.spouses.length">
                        <template v-for="spouse in member.spouses" :key="spouse.id">
                            <div class="tree-connector-h"></div>
                            <div class="tree-node-card spouse" 
                                 :class="{ 'is-dead': spouse.trang_thai === 'Đã mất', 'highlighted': isHighlighted(spouse.ho_ten) }"
                                 @click.stop="$emit('edit', spouse)">
                                <div class="node-avatar-container">
                                    <img :src="getAvatar(spouse)" class="node-avatar shadow-sm" alt="avatar">
                                </div>
                                <div class="node-content">
                                    <div class="node-name">{{ spouse.ho_ten }}</div>
                                    <div class="node-tag spouse-tag"><i class='bx bx-heart text-danger'></i> Vợ/Chồng</div>
                                </div>
                                <div class="node-edit-btn"><i class="bx bx-pencil"></i></div>
                            </div>
                        </template>
                    </template>
                </div>

                <!-- Đệ quy Con Cháu -->
                <ul v-if="member.children && member.children.length">
                    <TreeItem v-for="child in member.children" :key="child.id" :member="child" :listDoiTocHo="listDoiTocHo" :searchQuery="searchQuery" @edit="$emit('edit', $event)" />
                </ul>
            </template>
        </li>
    `,
};

export default {
  name: "PartnerGenealogy",
  components: { TreeItem },
  data() {
    return {
      allMembers: [],
      listChiNhanh: [],
      selectedChiNhanhId: null,
      listDoiTocHo: [],
      currentMember: {
        id: null,
        ho_ten: "",
        doi_thu: 1,
        cha_id: null,
        gioi_tinh: "Nam",
        loai_quan_he: "Chính",
        spouse_of_id: null,
        trang_thai: "Còn sống",
        ngay_mat: null,
        ngay_sinh: null,
        ghi_chu: "",
        avatar: null,
      },
      avatarPreview: null,
      avatarFile: null,
      isEditing: false,
      modal: null,
      searchQuery: "",

      // Zoom & Pan state
      zoom: 1,
      posX: 0,
      posY: 0,
      isPanning: false,
      lastMouseX: 0,
      lastMouseY: 0,
    };
  },
  computed: {
    treeData() {
      let list = JSON.parse(JSON.stringify(this.allMembers));

      if (this.selectedChiNhanhId) {
        list = list.filter((m) => m.chi_nhanh_id === this.selectedChiNhanhId);
      }

      const map = {};
      const roots = [];
      list.forEach((item) => {
        map[item.id] = item;
        item.children = [];
        item.spouses = [];
      });

      // Xử lý tạo node ẩn (Dummy Node) nếu có thế hệ bị nhảy (ví dụ Đời 1 nhảy thẳng đến cháu Đời 3)
      const getDummyNode = (parentId, doi_thu) => {
        let dummyId = "dummy_" + parentId + "_" + doi_thu;
        if (!map[dummyId]) {
          map[dummyId] = {
            id: dummyId,
            isDummy: true,
            doi_thu: doi_thu,
            children: [],
            spouses: [],
          };
          map[parentId].children.push(map[dummyId]);
        }
        return map[dummyId];
      };

      list.forEach((item) => {
        if (
          item.loai_quan_he === "Vợ/Chồng" &&
          item.spouse_of_id &&
          map[item.spouse_of_id]
        ) {
          map[item.spouse_of_id].spouses.push(item);
        } else if (item.cha_id && map[item.cha_id]) {
          let parent = map[item.cha_id];
          if (item.doi_thu > parent.doi_thu + 1) {
            let currentParent = parent;
            for (let d = parent.doi_thu + 1; d < item.doi_thu; d++) {
              currentParent = getDummyNode(currentParent.id, d);
            }
            currentParent.children.push(item);
          } else {
            parent.children.push(item);
          }
        } else if (item.loai_quan_he === "Chính") {
          roots.push(item);
        }
      });
      return roots;
    },
    canvasStyle() {
      return {
        transform: `translate(${this.posX}px, ${this.posY}px) scale(${this.zoom})`,
        transformOrigin: "top center",
      };
    },
  },
  mounted() {
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(
        document.getElementById("memberModal")
      );
    }
    this.loadDoiTocHo();
    this.loadChiNhanh();
    this.loadData();
    this.$refs.viewport.addEventListener("wheel", this.handleWheel, {
      passive: false,
    });
  },
  beforeUnmount() {
    this.$refs.viewport.removeEventListener("wheel", this.handleWheel);
  },
  methods: {
    getHeaders() {
      return {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("access_token")}`,
        },
      };
    },
    loadDoiTocHo() {
      axios
        .get("http://127.0.0.1:8000/api/doi-toc-ho/get-data", this.getHeaders())
        .then((res) => {
          if (res.data.status) {
            this.listDoiTocHo = res.data.data.sort(
              (a, b) => a.so_doi - b.so_doi
            );
          }
        });
    },
    loadData() {
      axios
        .get("http://127.0.0.1:8000/api/thanh-vien/get-data", this.getHeaders())
        .then((res) => {
          if (res.data.status) {
            this.allMembers = res.data.data;
          }
        });
    },
    loadChiNhanh() {
      axios
        .get("http://127.0.0.1:8000/api/chi-nhanh/get-data", this.getHeaders())
        .then((res) => {
          if (res.data.status) {
            this.listChiNhanh = res.data.data;
            if (this.listChiNhanh.length > 0 && !this.selectedChiNhanhId) {
              this.selectedChiNhanhId = this.listChiNhanh[0].id;
            }
          }
        });
    },
    // --- View Controls (Pan / Zoom) ---
    zoomIn() {
      if (this.zoom < 2.5) this.zoom += 0.1;
    },
    zoomOut() {
      if (this.zoom > 0.3) this.zoom -= 0.1;
    },
    resetView() {
      this.zoom = 1;
      this.posX = 0;
      this.posY = 0;
    },
    handleWheel(e) {
      e.preventDefault();
      if (e.deltaY < 0) this.zoomIn();
      else this.zoomOut();
    },
    startPan(e) {
      if (e.button !== 0) return;
      this.isPanning = true;
      this.lastMouseX = e.clientX;
      this.lastMouseY = e.clientY;
    },
    doPan(e) {
      if (!this.isPanning) return;
      this.posX += e.clientX - this.lastMouseX;
      this.posY += e.clientY - this.lastMouseY;
      this.lastMouseX = e.clientX;
      this.lastMouseY = e.clientY;
    },
    endPan() {
      this.isPanning = false;
    },
    // --- Modal & API ---
    openAddModal() {
      this.isEditing = false;
      this.currentMember = {
        id: null,
        ho_ten: "",
        doi_thu: 1,
        cha_id: null,
        gioi_tinh: "Nam",
        loai_quan_he: "Chính",
        spouse_of_id: null,
        trang_thai: "Còn sống",
        ngay_mat: null,
        ngay_sinh: null,
        ghi_chu: "",
        avatar: null,
      };
      this.avatarFile = null;
      this.avatarPreview = null;
      this.modal.show();
    },
    onEdit(member) {
      this.isEditing = true;
      this.currentMember = { ...member };
      this.avatarFile = null;
      this.avatarPreview = null;
      this.modal.show();
    },
    onAvatarChange(e) {
      const file = e.target.files[0];
      if (file) {
        this.avatarFile = file;
        this.avatarPreview = URL.createObjectURL(file);
      }
    },
    saveMember() {
      if (!this.currentMember.ho_ten) {
        toastr.warning("Vui lòng nhập Họ và Tên!");
        return;
      }
      const url = this.isEditing
        ? "http://127.0.0.1:8000/api/thanh-vien/update"
        : "http://127.0.0.1:8000/api/thanh-vien/create";
      const formData = new FormData();
      for (let key in this.currentMember) {
        if (
          this.currentMember[key] !== null &&
          this.currentMember[key] !== undefined
        ) {
          formData.append(key, this.currentMember[key]);
        }
      }
      if (this.avatarFile) formData.set("avatar", this.avatarFile);

      axios
        .post(url, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            ...this.getHeaders().headers,
          },
        })
        .then((res) => {
          if (res.data.status) {
            toastr.success(res.data.message);
            this.loadData();
            this.modal.hide();
          } else {
            toastr.error(res.data.message);
          }
        });
    },
    handleDelete() {
      if (
        confirm(
          "Bạn có chắc chắn muốn xóa thành viên này và toàn bộ con cháu (nếu có)?"
        )
      ) {
        axios
          .post(
            "http://127.0.0.1:8000/api/thanh-vien/delete",
            { id: this.currentMember.id },
            this.getHeaders()
          )
          .then((res) => {
            if (res.data.status) {
              toastr.success(res.data.message);
              this.loadData();
              this.modal.hide();
            }
          });
      }
    },
  },
};
</script>

<style scoped>
/* Container & Viewport */
.genealogy-container {
  min-height: calc(100vh - 100px);
}
.tree-viewport {
  height: 75vh;
  min-height: 600px;
  background-color: #f8fafc;
  position: relative;
  overflow: hidden;
  user-select: none;
  border-radius: 0 0 1rem 1rem;
}
.bg-grid {
  background-image: linear-gradient(to right, #e2e8f0 1px, transparent 1px),
    linear-gradient(to bottom, #e2e8f0 1px, transparent 1px);
  background-size: 40px 40px;
}
.backdrop-blur {
  backdrop-filter: blur(8px);
}
.tree-canvas {
  padding: 120px;
  transition: transform 0.1s ease-out;
  display: inline-block;
  min-width: 100%;
}

/* Basic Tree Structure */
.tree,
.tree ul,
.tree li {
  position: relative;
  transition: all 0.3s;
}
.tree ul {
  padding-top: 60px;
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: nowrap !important;
  justify-content: center;
  padding-left: 0;
  margin-bottom: 0;
}
.tree li {
  text-align: center;
  list-style-type: none;
  padding: 60px 15px 0 15px;
  flex: 0 0 auto !important;
}

/* Các đường nối (Connectors) */
.tree li::before,
.tree li::after {
  content: "";
  position: absolute;
  top: 0;
  right: 50%;
  border-top: 2.5px solid #cbd5e1;
  width: 50%;
  height: 60px;
}
.tree li::after {
  right: auto;
  left: 50%;
  border-left: 2.5px solid #cbd5e1;
}
.tree li:only-child::after,
.tree li:only-child::before {
  display: none;
}
.tree li:only-child {
  padding-top: 0;
}
.tree li:first-child::before,
.tree li:last-child::after {
  border: 0 none;
}
.tree li:last-child::before {
  border-right: 2.5px solid #cbd5e1;
  border-radius: 0 12px 0 0;
}
.tree li:first-child::after {
  border-radius: 12px 0 0 0;
}
.tree ul ul::before {
  content: "";
  position: absolute;
  top: 0;
  left: 50%;
  border-left: 2.5px solid #cbd5e1;
  width: 0;
  height: 60px;
}

/* Node Styling */
.tree-node-group {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  position: relative;
  z-index: 10;
}
.tree-connector-h {
  width: 35px;
  height: 2.5px;
  background: #cbd5e1;
}
.tree-dummy-node {
  width: 2.5px;
  height: 120px;
  background: repeating-linear-gradient(
    to bottom,
    #cbd5e1,
    #cbd5e1 5px,
    transparent 5px,
    transparent 10px
  );
  margin: 0 auto;
}

.tree-node-card {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  padding: 12px;
  border-radius: 16px;
  min-width: 220px;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05),
    0 2px 4px -1px rgba(0, 0, 0, 0.03);
  cursor: pointer;
  position: relative;
  display: flex;
  align-items: center;
  gap: 14px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.tree-node-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
    0 10px 10px -5px rgba(0, 0, 0, 0.04);
  z-index: 100;
  border-color: #cbd5e1;
}

/* Colors theo thế hệ (Gen Colors) - Soft Palette */
.gen-1 {
  border-left: 6px solid #3b82f6;
} /* Blue */
.gen-2 {
  border-left: 6px solid #10b981;
} /* Emerald */
.gen-3 {
  border-left: 6px solid #f59e0b;
} /* Amber */
.gen-4 {
  border-left: 6px solid #ef4444;
} /* Red */
.gen-5 {
  border-left: 6px solid #8b5cf6;
} /* Purple */

/* Highlight State */
.tree-node-card.highlighted {
  border: 2px solid #eab308;
  background: #fefce8;
  box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.3);
}

/* Dead State */
.tree-node-card.is-dead {
  background: #f1f5f9;
  filter: grayscale(0.6);
}
.tree-node-card.is-dead .node-name {
  color: #475569;
  text-decoration: line-through;
}

/* Spouse Node */
.tree-node-card.spouse {
  border-style: dashed;
  border-left-width: 1px;
  min-width: 190px;
  background: #fffbfa;
}
.tree-node-card.spouse.is-dead {
  background: #f8fafc;
}

/* Node Content */
.node-avatar-container {
  position: relative;
}
.node-avatar {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
.node-content {
  text-align: left;
  flex-grow: 1;
}
.node-name {
  font-weight: 700;
  font-size: 15px;
  color: #1e293b;
  line-height: 1.2;
  margin-bottom: 3px;
}
.node-date {
  font-size: 12px;
  color: #64748b;
  margin-bottom: 3px;
}
.node-tag {
  display: inline-block;
  font-size: 11px;
  font-weight: 600;
  color: #3b82f6;
  background: #eff6ff;
  padding: 2px 8px;
  border-radius: 20px;
}
.spouse-tag {
  color: #e11d48;
  background: #fff1f2;
}

/* Nút Chỉnh Sửa Trực Tiếp Trên Card */
.node-edit-btn {
  position: absolute;
  top: -12px;
  right: -12px;
  width: 32px;
  height: 32px;
  background: #fff;
  border: 1px solid #e2e8f0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  opacity: 0;
  transform: scale(0.8);
  transition: all 0.2s;
  color: #64748b;
}
.tree-node-card:hover .node-edit-btn {
  opacity: 1;
  transform: scale(1);
}
.node-edit-btn:hover {
  background: #3b82f6;
  color: #fff;
  border-color: #3b82f6;
}

/* Utilities */
.zoom-btn:hover {
  background-color: #e2e8f0 !important;
}
textarea {
  resize: none;
}
</style>