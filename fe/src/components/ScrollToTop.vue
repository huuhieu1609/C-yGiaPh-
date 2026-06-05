<template>
  <transition name="fade-slide">
    <div
      v-show="isVisible"
      class="scroll-to-top-btn"
      @click="scrollToTop"
      title="Lên đầu trang"
    >
      <!-- Circular Progress Ring -->
      <svg class="progress-ring" width="48" height="48">
        <circle
          class="progress-ring__circle-bg"
          stroke-width="3"
          fill="transparent"
          r="21"
          cx="24"
          cy="24"
        />
        <circle
          class="progress-ring__circle"
          :stroke-dasharray="strokeDasharray"
          :stroke-dashoffset="strokeDashoffset"
          stroke-width="3"
          stroke-linecap="round"
          fill="transparent"
          r="21"
          cx="24"
          cy="24"
        />
      </svg>
      
      <!-- Inner Icon Wrapper -->
      <div class="scroll-icon-wrapper">
        <svg
          class="scroll-icon"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'ScrollToTop',
  data() {
    return {
      isVisible: false,
      scrollPercent: 0,
      radius: 21,
    };
  },
  computed: {
    strokeDasharray() {
      return 2 * Math.PI * this.radius; // ~131.95
    },
    strokeDashoffset() {
      return this.strokeDasharray - (this.scrollPercent / 100) * this.strokeDasharray;
    }
  },
  mounted() {
    // Listen to scroll events on window in capturing phase to capture scroll event from any child container (like .main-workspace-wrapper)
    window.addEventListener('scroll', this.handleScroll, true);
    window.addEventListener('resize', this.handleScroll);
    
    // Check initial scroll state
    this.handleScroll();
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll, true);
    window.removeEventListener('resize', this.handleScroll);
  },
  methods: {
    handleScroll() {
      const docEl = document.documentElement;
      const body = document.body;
      
      // 1. Check window/body scroll
      const windowScrollTop = window.scrollY || docEl.scrollTop || body.scrollTop;
      const windowHeight = docEl.scrollHeight - docEl.clientHeight;
      const windowPercent = windowHeight > 0 ? (windowScrollTop / windowHeight) * 100 : 0;

      // 2. Check admin/partner layout workspace wrapper scroll
      const workspace = document.querySelector('.main-workspace-wrapper');
      let workspaceScrollTop = 0;
      let workspacePercent = 0;
      
      if (workspace) {
        workspaceScrollTop = workspace.scrollTop;
        const workspaceHeight = workspace.scrollHeight - workspace.clientHeight;
        workspacePercent = workspaceHeight > 0 ? (workspaceScrollTop / workspaceHeight) * 100 : 0;
      }

      // Choose which scroll context is active based on whether workspace is actually scrolled
      if (workspace && workspaceScrollTop > 30) {
        this.isVisible = workspaceScrollTop > 150;
        this.scrollPercent = Math.min(100, Math.max(0, workspacePercent));
      } else {
        this.isVisible = windowScrollTop > 150;
        this.scrollPercent = Math.min(100, Math.max(0, windowPercent));
      }
    },
    scrollToTop() {
      // Scroll the main window smoothly
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });

      // Scroll the workspace wrapper smoothly if it exists
      const workspace = document.querySelector('.main-workspace-wrapper');
      if (workspace) {
        workspace.scrollTo({
          top: 0,
          behavior: 'smooth'
        });
      }
    }
  }
};
</script>

<style scoped>
.scroll-to-top-btn {
  position: fixed;
  bottom: 35px;
  right: 35px;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
  
  /* Glassmorphism Styling */
  background: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.5);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08), inset 0 0 0 1px rgba(255, 255, 255, 0.5);
  
  transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
  user-select: none;
}

/* Dark mode overrides using the app's data-theme attribute */
[data-theme="dark"] .scroll-to-top-btn {
  background: rgba(26, 28, 46, 0.85);
  border-color: rgba(255, 255, 255, 0.08);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3), inset 0 0 0 1px rgba(255, 255, 255, 0.05);
}

.scroll-to-top-btn:hover {
  transform: translateY(-5px) scale(1.08);
  background: #ffffff;
  box-shadow: 0 10px 25px rgba(67, 160, 71, 0.25), inset 0 0 0 1px rgba(67, 160, 71, 0.2);
}

[data-theme="dark"] .scroll-to-top-btn:hover {
  background: #1a1c2e;
  box-shadow: 0 10px 25px rgba(67, 160, 71, 0.35), inset 0 0 0 1px rgba(67, 160, 71, 0.3);
}

/* SVG Progress Ring */
.progress-ring {
  position: absolute;
  top: -1px;
  left: -1px;
  transform: rotate(-90deg);
  pointer-events: none;
}

.progress-ring__circle-bg {
  stroke: rgba(0, 0, 0, 0.05);
  transition: stroke 0.3s ease;
}

[data-theme="dark"] .progress-ring__circle-bg {
  stroke: rgba(255, 255, 255, 0.06);
}

.progress-ring__circle {
  stroke: #43a047; /* Premium theme green */
  transition: stroke-dashoffset 0.08s linear, stroke 0.3s ease;
}

.scroll-to-top-btn:hover .progress-ring__circle {
  stroke: #2e7d32;
}

/* Scroll Icon Wrapper */
.scroll-icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--text-main, #212529);
  transition: all 0.3s ease;
  z-index: 2;
}

.scroll-to-top-btn:hover .scroll-icon-wrapper {
  color: #43a047;
}

[data-theme="dark"] .scroll-icon-wrapper {
  color: var(--text-main, #f3f4f6);
}

.scroll-icon {
  width: 18px;
  height: 18px;
  transition: transform 0.35s cubic-bezier(0.25, 1, 0.5, 1);
}

.scroll-to-top-btn:hover .scroll-icon {
  transform: translateY(-3px);
}

/* Animation Transitions */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(30px) scale(0.7);
}
</style>
