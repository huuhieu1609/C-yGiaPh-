<template>
  <div class="landing-page-wrapper">
    <div class="hero-background"></div>
    <div class="glow-blob blob-purple"></div>
    <div class="glow-blob blob-blue"></div>

    <div class="page-content">
      <div class="page-header text-center">
        <span class="subtitle-gradient d-block text-uppercase mb-2">DI SẢN TRỰC TUYẾN</span>
        <h2 class="text-gradient fw-bold display-5 mb-1">Cây Gia Phả Dòng Họ</h2>
        <p class="text-white-50 mb-0">Sơ đồ phả hệ các thế hệ dòng họ</p>
      </div>

      <div class="tree-card-wrapper">
        <div class="card-toolbar">
          <div class="toolbar-left">
            <button class="tb-btn" @click="loadData" title="Làm mới"><i class="bx bx-refresh"></i></button>
            <button class="tb-btn" @click="zoomIn"><i class="bx bx-plus"></i></button>
            <span class="zoom-label">{{ Math.round(zoom * 100) }}%</span>
            <button class="tb-btn" @click="zoomOut"><i class="bx bx-minus"></i></button>
            <button class="tb-btn" @click="resetView" title="Đặt lại"><i class="bx bx-expand-alt"></i></button>
          </div>
          <div class="toolbar-right">
             <span class="toolbar-hint">
              <i class="bx bx-mouse-alt"></i> Cuộn thu phóng &bull; Kéo di chuyển &bull; Click xem chi tiết
            </span>
          </div>
        </div>

        <div
          class="tree-viewport"
          ref="viewport"
          @mousedown="startPan"
          @mousemove="doPan"
          @mouseup="endPan"
          @mouseleave="endPan"
        >
          <div class="tree-canvas" :style="canvasStyle">
            <div v-if="isLoading" class="empty-state">
              <div class="ld-ring"></div>
              <p>Đang tải dữ liệu...</p>
            </div>
            <div v-else-if="!treeData.length" class="empty-state">
              <i class="bx bx-git-repo-forked"></i>
              <p>Chưa có dữ liệu gia phả</p>
            </div>
            <div class="tree" v-else>
              <ul class="tree-ul">
                <TreeItem
                  v-for="root in treeData"
                  :key="root.id"
                  :member="root"
                  :listDoiTocHo="listDoiTocHo"
                  @view="onView"
                />
              </ul>
            </div>
          </div>
        </div>

        <div class="view-hint">
          <i class="bx bx-mouse"></i> Cuộn để thu phóng &bull; <i class="bx bx-move"></i> Kéo để di chuyển
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="viewMemberModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content radius-15 shadow-lg border-0">
          <div class="modal-header border-0 bg-dark text-white" style="border-radius:15px 15px 0 0">
            <h5 class="modal-title fw-bold" style="color:#d4af37">
              <i class="bx bx-id-card me-2"></i>Thông Tin Thành Viên
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body p-4 text-center">
            <div class="modal-avatar-wrapper mb-3">
              <img
                :src="currentMember.avatar || `https://ui-avatars.com/api/?name=${currentMember.ho_ten}&background=d4af37&color=fff&size=128`"
                class="rounded-circle border border-3 border-warning"
                width="120" height="120" style="object-fit:cover"
                alt="avatar"
              >
            </div>
            <h4 class="fw-bold mb-1">{{ currentMember.ho_ten }}</h4>
            <span class="badge bg-light text-dark border mb-3 px-3 py-2">Đời thứ {{ currentMember.doi_thu }}</span>
            <div class="row text-start mt-3">
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Giới tính</span>
                <span class="fw-semibold">{{ currentMember.gioi_tinh }}</span>
              </div>
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Trạng thái</span>
                <span class="fw-semibold" :class="currentMember.trang_thai === 'Đã mất' ? 'text-danger' : 'text-success'">
                  {{ currentMember.trang_thai }}
                </span>
              </div>
              <div class="col-6 mb-3">
                <span class="text-muted small d-block">Ngày sinh</span>
                <span class="fw-semibold">{{ currentMember.ngay_sinh ? fmtDate(currentMember.ngay_sinh) : 'Không rõ' }}</span>
              </div>
              <div class="col-6 mb-3" v-if="currentMember.trang_thai === 'Đã mất'">
                <span class="text-muted small d-block">Ngày mất</span>
                <span class="fw-semibold">{{ currentMember.ngay_mat || 'Không rõ' }}</span>
              </div>
              <div class="col-12" v-if="currentMember.ghi_chu">
                <span class="text-muted small d-block">Ghi chú</span>
                <p class="mb-0 text-dark opacity-75">{{ currentMember.ghi_chu }}</p>
              </div>
            </div>
          </div>
          <div class="modal-footer border-0 justify-content-center">
            <button class="btn btn-secondary px-5 radius-10" data-bs-dismiss="modal">Đóng</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent, h } from 'vue';
import axios from 'axios';

const fmtDate = (d) => d ? new Date(d).toLocaleDateString('vi-VN') : '';

const TreeItem = defineComponent({
  name: 'TreeItem',
  props: ['member', 'listDoiTocHo'],
  emits: ['view'],
  render() {
    const m = this.member;
    const hasChildren = m.children && m.children.length > 0;

    if (m.isDummy) {
      const kids = hasChildren
        ? h('ul', { class: 'tree-ul' }, m.children.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x) })))
        : null;
      return h('li', { class: 'tree-li' }, [
        h('div', { class: 'tree-node-group dummy-group' }, [
          h('div', { class: 'dummy-line' })
        ]),
        kids
      ]);
    }

    const getTenDoi = (n) => {
      if (!this.listDoiTocHo || !this.listDoiTocHo.length) return '';
      const d = this.listDoiTocHo.find(x => x.so_doi == n);
      return d && d.ten_doi ? ` (${d.ten_doi.toUpperCase()})` : '';
    };

    const genClass = `gen-${(m.doi_thu % 5) + 1}`;

    const makeCard = (person, isSpouse = false) => {
      const dead = person.trang_thai === 'Đã mất';
      const gClass = isSpouse ? '' : `gen-${(person.doi_thu % 5) + 1}`;
      const src = person.avatar
        || `https://ui-avatars.com/api/?name=${encodeURIComponent(person.ho_ten)}&background=d4af37&color=fff&size=100`;

      const initials = person.ho_ten.split(' ').map(n => n[0]).join('').slice(0, 2).toUpperCase();

      return h('div', {
        class: ['tree-node-card', gClass, { spouse: isSpouse, 'is-dead': dead }],
        onClick: e => { e.stopPropagation(); this.$emit('view', person); }
      }, [
        h('div', { class: 'node-avatar-container' }, [
          person.avatar 
            ? h('img', { src, class: 'node-avatar shadow-sm', width: 50, height: 50 })
            : h('div', { class: 'node-initials shadow-sm' }, initials)
        ]),
        h('div', { class: 'node-content' }, [
          h('div', { class: 'node-name' }, person.ho_ten),
          person.ngay_sinh ? h('div', { class: 'node-date' }, fmtDate(person.ngay_sinh)) : null,
          h('div', { class: ['node-tag', isSpouse ? 'spouse-tag' : ''] },
            isSpouse ? 'VỢ/CHỒNG' : `ĐỜI ${person.doi_thu}${getTenDoi(person.doi_thu)}`
          )
        ])
      ]);
    };

    const nodeGroup = h('div', { class: 'tree-node-group' }, [
      h('div', { class: 'couple-wrapper' }, [
        makeCard(m, false),
        ...(m.spouses && m.spouses.length
          ? m.spouses.flatMap(s => [h('div', { class: 'tree-connector-h' }), makeCard(s, true)])
          : [])
      ])
    ]);

    const children = hasChildren
      ? h('ul', { class: 'tree-ul' }, m.children.map(c => h(TreeItem, { key: c.id, member: c, listDoiTocHo: this.listDoiTocHo, onView: x => this.$emit('view', x) })))
      : null;

    return h('li', { class: 'tree-li' }, [nodeGroup, children]);
  }
});

export default {
  name: 'ClientGiaPha',
  components: { TreeItem },
  data() {
    return {
      allMembers: [],
      listDoiTocHo: [],
      currentMember: {},
      modal: null,
      isLoading: true,
      zoom: 0.9,
      posX: 0,
      posY: 0,
      isPanning: false,
      lastMouseX: 0,
      lastMouseY: 0
    };
  },
  computed: {
    treeData() {
      const list = JSON.parse(JSON.stringify(this.allMembers));
      const map = {};
      const roots = [];
      list.forEach(i => { map[i.id] = i; i.children = []; i.spouses = []; });

      const getDummy = (pid, doi) => {
        const id = `dummy_${pid}_${doi}`;
        if (!map[id]) {
          map[id] = { id, isDummy: true, doi_thu: doi, children: [], spouses: [] };
          map[pid].children.push(map[id]);
        }
        return map[id];
      };

      list.forEach(i => {
        if (i.loai_quan_he === 'Vợ/Chồng' && i.spouse_of_id && map[i.spouse_of_id]) {
          map[i.spouse_of_id].spouses.push(i);
        } else if (i.cha_id && map[i.cha_id]) {
          let parent = map[i.cha_id];
          if (i.doi_thu > parent.doi_thu + 1) {
            let cur = parent;
            for (let d = parent.doi_thu + 1; d < i.doi_thu; d++) cur = getDummy(cur.id, d);
            cur.children.push(i);
          } else {
            parent.children.push(i);
          }
        } else if (i.loai_quan_he === 'Chính') {
          roots.push(i);
        }
      });
      return roots;
    },
    canvasStyle() {
      return {
        transform: `translate(${this.posX}px, ${this.posY}px) scale(${this.zoom})`,
        transformOrigin: 'top center'
      };
    }
  },
  mounted() {
    if (window.bootstrap) {
      this.modal = new window.bootstrap.Modal(document.getElementById('viewMemberModal'));
    }
    this.loadDoiTocHo();
    this.loadData();
    this.$refs.viewport.addEventListener('wheel', this.handleWheel, { passive: false });
  },
  methods: {
    fmtDate,
    loadDoiTocHo() {
      axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data')
        .then(r => { if (r.data.status) this.listDoiTocHo = r.data.data.sort((a,b)=>a.so_doi-b.so_doi); });
    },
    loadData() {
      this.isLoading = true;
      axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
        .then(r => { if (r.data.status) this.allMembers = r.data.data; })
        .finally(() => { this.isLoading = false; });
    },
    onView(m) { this.currentMember = m; this.modal.show(); },
    zoomIn() { if (this.zoom < 2.0) this.zoom = +(this.zoom + 0.1).toFixed(1); },
    zoomOut() { if (this.zoom > 0.2) this.zoom = +(this.zoom - 0.1).toFixed(1); },
    resetView() { this.zoom = 0.9; this.posX = 0; this.posY = 0; },
    handleWheel(e) { e.preventDefault(); e.deltaY < 0 ? this.zoomIn() : this.zoomOut(); },
    startPan(e) { if (e.button !== 0) return; this.isPanning = true; this.lastMouseX = e.clientX; this.lastMouseY = e.clientY; },
    doPan(e) {
      if (!this.isPanning) return;
      this.posX += e.clientX - this.lastMouseX;
      this.posY += e.clientY - this.lastMouseY;
      this.lastMouseX = e.clientX; this.lastMouseY = e.clientY;
    },
    endPan() { this.isPanning = false; }
  }
};
</script>

<style>
/* KHÔNG SỬ DỤNG SCOPED ĐỂ CSS CÓ THỂ TÁC ĐỘNG VÀO RENDER FUNCTION */

.landing-page-wrapper {
  background: #0b1120;
  min-height: 100vh;
  font-family: 'Inter', sans-serif;
  position: relative;
  overflow: hidden;
}
.hero-background {
  position: absolute; inset: 0;
  background-image: linear-gradient(to bottom, rgba(11,17,32,0.5), rgba(11,17,32,1)), url('@/assets/images/bg_product.webp');
  background-size: cover; background-position: center;
  z-index: 0;
}
.glow-blob { position: absolute; width: 400px; height: 400px; border-radius: 50%; filter: blur(120px); opacity: 0.3; z-index: 1; }
.blob-purple { background: #1e0b3a; top: 0; left: -5%; }
.blob-blue { background: #031948; top: 30%; right: -5%; }

.page-content { position: relative; z-index: 2; padding: 0 20px 40px; max-width: 1500px; margin: 0 auto; }
.page-header { padding: 60px 0 30px; }
.subtitle-gradient { font-size: 0.8rem; font-weight: 700; letter-spacing: 0.2em; color: #d4af37; }
.text-gradient { background: linear-gradient(90deg,#fbff00,#ff8c00); -webkit-background-clip: text; background-clip: text; color: transparent; }

.tree-card-wrapper {
  background: #fff;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 25px 70px rgba(0,0,0,0.5);
  position: relative;
}

.card-toolbar {
  display: flex; justify-content: space-between; align-items: center; padding: 15px 25px; border-bottom: 1px solid #eee; background: #fff;
}
.toolbar-left { display: flex; align-items: center; gap: 10px; }
.tb-btn {
  width: 40px; height: 40px; border-radius: 12px; border: 1px solid #e5e7eb; background: #f9fafb;
  color: #4b5563; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
}
.tb-btn:hover { background: #d4af37; border-color: #d4af37; color: #fff; transform: translateY(-2px); }
.zoom-label { font-size: 0.9rem; font-weight: 800; color: #111827; min-width: 50px; text-align: center; }
.toolbar-hint { font-size: 0.85rem; color: #6b7280; }

.tree-viewport {
  height: 800px;
  background: #fdfdfd;
  background-image: radial-gradient(#d1d5db 1px, transparent 1px);
  background-size: 35px 35px;
  position: relative;
  overflow: hidden;
  cursor: grab;
}
.tree-viewport:active { cursor: grabbing; }

.tree-canvas {
  padding: 100px;
  transition: transform 0.05s linear;
  display: inline-block;
  min-width: 100%;
  text-align: center;
}

/* ─── CSS TREE STRUCTURE (FORCED HORIZONTAL) ─── */
.tree, .tree-ul {
  display: flex !important;
  flex-direction: row !important;
  justify-content: center !important;
  padding: 0 !important;
  margin: 0 !important;
  list-style: none !important;
  position: relative;
}

.tree-ul {
  padding-top: 50px !important;
}

.tree-li {
  float: none !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  position: relative;
  padding: 50px 20px 0 20px !important;
}

/* Vertical line from parent to current horizontal bar */
.tree-li::before, .tree-li::after {
  content: ''; position: absolute; top: 0; right: 50%;
  border-top: 2px solid #cbd5e1; width: 50%; height: 50px;
}
.tree-li::after { right: auto; left: 50%; border-left: 2px solid #cbd5e1; }

/* Remove lines for single child or edges */
.tree-li:only-child::after, .tree-li:only-child::before { display: none; }
.tree-li:only-child { padding-top: 0 !important; }
.tree-li:first-child::before, .tree-li:last-child::after { border: 0 none; }
.tree-li:last-child::before { border-right: 2px solid #cbd5e1; border-radius: 0 10px 0 0; }
.tree-li:first-child::after { border-radius: 10px 0 0 0; }

/* Vertical line down from current node to children ul */
.tree-ul::before {
  content: ''; position: absolute; top: 0; left: 50%;
  border-left: 2px solid #cbd5e1; width: 0; height: 50px;
}

/* ─── NODE STYLING ─── */
.tree-node-group {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 10;
}

.couple-wrapper {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0;
}

.tree-connector-h {
  width: 40px;
  height: 2px;
  background: #cbd5e1;
  position: relative;
}

/* The card itself */
.tree-node-card {
  background: #fff;
  border: 2px solid #e2e8f0;
  padding: 12px 16px;
  border-radius: 18px;
  min-width: 230px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.06);
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
}

.tree-node-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 20px 40px rgba(0,0,0,0.12);
  border-color: #d4af37;
}

/* Generation Border Colors (Matches User Image) */
.gen-1 { border-left: 6px solid #8b4513 !important; } /* Brown */
.gen-2 { border-left: 6px solid #9333ea !important; } /* Purple */
.gen-3 { border-left: 6px solid #3b82f6 !important; } /* Blue */
.gen-4 { border-left: 6px solid #10b981 !important; } /* Green */
.gen-5 { border-left: 6px solid #f59e0b !important; } /* Amber */

.tree-node-card.spouse {
  border-style: dashed;
  background: #f9fafb;
  min-width: 190px;
}

.tree-node-card.is-dead {
  filter: grayscale(0.8);
  opacity: 0.7;
}

/* Avatar styling inside card */
.node-avatar-container {
  width: 50px;
  height: 50px;
  flex-shrink: 0;
}

.node-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  display: block;
}

.node-initials {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #d4af37;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 1.1rem;
  border: 2px solid #fff;
}

/* Card Content Styling */
.node-content {
  text-align: left;
  flex-grow: 1;
  min-width: 0;
}

.node-name {
  font-weight: 700;
  font-size: 14px;
  color: #111827;
  margin-bottom: 2px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.node-date {
  font-size: 11px;
  color: #6b7280;
}

.node-tag {
  font-size: 10px;
  font-weight: 800;
  color: #3b82f6;
  text-transform: uppercase;
  margin-top: 4px;
  letter-spacing: 0.02em;
}

.spouse-tag {
  color: #f59e0b;
}

/* Dummy Node for generation jumping */
.dummy-group {
  padding: 20px 0;
}
.dummy-line {
  width: 2px;
  height: 60px;
  background: #cbd5e1;
}

/* ─── UI ELEMENTS ─── */
.empty-state { text-align: center; padding: 120px; color: #9ca3af; }
.empty-state i { font-size: 4rem; display: block; margin-bottom: 15px; opacity: 0.3; }
.ld-ring { width: 50px; height: 50px; border: 4px solid #f3f4f6; border-top-color: #d4af37; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px; }
@keyframes spin { to { transform: rotate(360deg); } }

.view-hint {
  position: absolute; bottom: 20px; right: 25px;
  font-size: 12px; color: #6b7280; font-weight: 500;
  background: rgba(255,255,255,0.9);
  padding: 6px 18px; border-radius: 30px;
  border: 1px solid #e5e7eb;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.radius-15 { border-radius: 15px !important; }
.radius-10 { border-radius: 10px !important; }
</style>
