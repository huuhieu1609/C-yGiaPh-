<template>
  <transition name="fade-slide">
    <div
      v-show="isVisible"
      class="scroll-to-top-btn"
      @click="scrollToTop"
      role="button"
      aria-label="Cuộn lên đầu trang"
    >
      <!-- Circular Progress Ring -->
      <svg class="progress-ring" width="52" height="52">
        <defs>
          <linearGradient id="progress-gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#3b82f6" />
            <stop offset="50%" stop-color="#6366f1" />
            <stop offset="100%" stop-color="#ec4899" />
          </linearGradient>
        </defs>
        <!-- Track Circle -->
        <circle
          class="progress-ring__track"
          stroke="rgba(255, 255, 255, 0.12)"
          stroke-width="3"
          fill="transparent"
          r="22"
          cx="26"
          cy="26"
        />
        <!-- Progress Circle -->
        <circle
          class="progress-ring__circle"
          stroke="url(#progress-gradient)"
          stroke-width="3.5"
          stroke-linecap="round"
          fill="transparent"
          r="22"
          cx="26"
          cy="26"
          :stroke-dasharray="strokeDasharray"
          :stroke-dashoffset="strokeDashoffset"
        />
      </svg>

      <!-- Up Arrow Icon -->
      <div class="arrow-icon-wrapper">
        <svg
          class="arrow-up-svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2.5"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <line x1="12" y1="19" x2="12" y2="5"></line>
          <polyline points="5 12 12 5 19 12"></polyline>
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
      progress: 0,
      scrollTarget: null,
      radius: 22,
    };
  },
  computed: {
    strokeDasharray() {
      return 2 * Math.PI * this.radius; // 2 * pi * 22 = ~138.23
    },
    strokeDashoffset() {
      return this.strokeDasharray * (1 - this.progress);
    },
  },
  mounted() {
    // Listen to all scroll events on page (including container scroll) via capture phase listener
    window.addEventListener('scroll', this.handleScroll, true);
    
    // Watch path change to reset scroll view
    this.$watch('$route', () => {
      this.isVisible = false;
      this.progress = 0;
      this.scrollTarget = null;
    });
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll, true);
  },
  methods: {
    handleScroll(event) {
      let target = event.target;
      
      // If it's a document/window scroll
      if (target === document || target === window || target === document.documentElement || target === document.body) {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
        
        this.progress = scrollHeight > 0 ? Math.min(scrollTop / scrollHeight, 1) : 0;
        this.isVisible = scrollTop > 300;
        this.scrollTarget = window;
      } else {
        // If it's an element scroll, verify if it is scrollable and has significant size
        if (target.classList && (target.classList.contains('main-workspace-wrapper') || target.scrollHeight > target.clientHeight)) {
          const scrollTop = target.scrollTop;
          const scrollHeight = target.scrollHeight - target.clientHeight;
          
          this.progress = scrollHeight > 0 ? Math.min(scrollTop / scrollHeight, 1) : 0;
          this.isVisible = scrollTop > 300;
          this.scrollTarget = target;
        }
      }
    },
    scrollToTop() {
      const target = this.scrollTarget || window;
      if (target === window) {
        window.scrollTo({
          top: 0,
          behavior: 'smooth',
        });
      } else if (typeof target.scrollTo === 'function') {
        target.scrollTo({
          top: 0,
          behavior: 'smooth',
        });
      }
    },
  },
};
</script>

<style scoped>
.scroll-to-top-btn {
  box-sizing: border-box;
  position: fixed;
  bottom: 30px;
  right: 30px;
  width: 52px;
  height: 52px;
  border-radius: 50%;
  background: rgba(18, 19, 32, 0.85);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.12);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.05);
  cursor: pointer;
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
  user-select: none;
  outline: none;
}

/* Light mode support if layout has theme tag */
[data-theme="light"] .scroll-to-top-btn {
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(0, 0, 0, 0.08);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(0, 0, 0, 0.02);
}

[data-theme="light"] .progress-ring__track {
  stroke: rgba(0, 0, 0, 0.06);
}

.scroll-to-top-btn:hover {
  transform: scale(1.1) translateY(-4px);
  background: rgba(24, 25, 41, 0.95);
  border-color: rgba(99, 102, 241, 0.4);
  box-shadow: 0 12px 40px rgba(99, 102, 241, 0.3), 0 0 15px rgba(99, 102, 241, 0.2);
}

[data-theme="light"] .scroll-to-top-btn:hover {
  background: rgba(255, 255, 255, 0.95);
  border-color: rgba(99, 102, 241, 0.3);
  box-shadow: 0 12px 40px rgba(99, 102, 241, 0.15), 0 0 15px rgba(99, 102, 241, 0.1);
}

.scroll-to-top-btn:active {
  transform: scale(0.92) translateY(-2px);
}

/* Progress Ring styling */
.progress-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-90deg);
  width: 52px;
  height: 52px;
  margin: 0;
  padding: 0;
  pointer-events: none;
  display: block;
}

.progress-ring__circle {
  transition: stroke-dashoffset 0.1s linear;
}

/* Arrow Icon styling */
.arrow-icon-wrapper {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  color: #ffffff;
  transition: color 0.3s;
  box-sizing: border-box;
}

[data-theme="light"] .arrow-icon-wrapper {
  color: #1f2937;
}

.arrow-up-svg {
  width: 20px;
  height: 20px;
  transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
}

.scroll-to-top-btn:hover .arrow-up-svg {
  transform: translateY(-2px);
  color: #6366f1;
}

/* Vue Fade-Slide Transition */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(20px) scale(0.8);
}
</style>
