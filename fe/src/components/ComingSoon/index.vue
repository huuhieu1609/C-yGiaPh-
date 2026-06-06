<template>
  <div class="coming-soon-wrapper">
    <div class="stars-bg"></div>
    <div class="glow-container">
      <div class="glow-sphere gold-glow"></div>
      <div class="glow-sphere dark-glow"></div>
    </div>
    
    <div class="coming-soon-card p-5 text-center">
      <div class="brand-badge mb-4">
        <span class="badge-dot"></span>
        <span class="badge-text text-uppercase">Premium Feature</span>
      </div>
      
      <div class="coming-soon-icon mb-4">
        <i class="bx bx-crown gold-shimmer-text"></i>
      </div>
      
      <h1 class="coming-soon-title mb-3">COMING SOON</h1>
      
      <h4 class="feature-name mb-4 text-gradient-gold">
        {{ featureName }}
      </h4>
      
      <p class="feature-desc mb-5">
        Tính năng này đang được phát triển tích cực bằng các công nghệ tối tân nhất và sẽ sớm ra mắt trong phiên bản cập nhật tiếp theo để mang đến trải nghiệm tuyệt vời nhất cho dòng tộc của bạn.
      </p>
      
      <button class="btn-back-gold px-5 py-3 fw-bold radius-30" @click="goBack">
        <i class="bx bx-arrow-back me-2"></i> Quay Lại Trang Chủ
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ComingSoonVIP',
  data() {
    return {
      featureName: 'Tính Năng Đang Phát Triển'
    };
  },
  mounted() {
    // Đọc tên tính năng từ query parameter
    const name = this.$route.query.name;
    if (name) {
      this.featureName = name;
    }
  },
  methods: {
    goBack() {
      this.$router.go(-1);
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap');

.coming-soon-wrapper {
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: #0f111a;
  font-family: 'Outfit', sans-serif;
  color: #f1f5f9;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  overflow: hidden;
}

/* Background Stars */
.stars-bg {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  background-image: 
    radial-gradient(white, rgba(255,255,255,.2) 2px, transparent 40px),
    radial-gradient(white, rgba(255,255,255,.15) 1px, transparent 30px),
    radial-gradient(white, rgba(255,255,255,.1) 2px, transparent 40px);
  background-size: 550px 550px, 350px 350px, 250px 250px;
  background-position: 0 0, 40px 60px, 130px 270px;
  opacity: 0.15;
}

/* Luxury Glow Spheres */
.glow-container {
  position: absolute;
  top: 0; left: 0; right: 0; bottom: 0;
  pointer-events: none;
}
.glow-sphere {
  position: absolute;
  border-radius: 50%;
  filter: blur(120px);
  opacity: 0.25;
}
.gold-glow {
  width: 450px; height: 450px;
  background: radial-gradient(circle, #e5a93b 0%, transparent 70%);
  top: -10%; right: 10%;
  animation: floatGold 12s infinite alternate ease-in-out;
}
.dark-glow {
  width: 500px; height: 500px;
  background: radial-gradient(circle, #1e2035 0%, transparent 75%);
  bottom: -15%; left: 15%;
  animation: floatDark 15s infinite alternate ease-in-out;
}

/* Luxury Card */
.coming-soon-card {
  position: relative;
  width: 90%;
  max-width: 600px;
  background: rgba(25, 27, 41, 0.4);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 215, 0, 0.1);
  border-radius: 24px;
  box-shadow: 
    0 30px 60px rgba(0, 0, 0, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
  z-index: 10;
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Brand Badge */
.brand-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(229, 169, 59, 0.08);
  border: 1px solid rgba(229, 169, 59, 0.2);
  padding: 6px 16px;
  border-radius: 30px;
}
.badge-dot {
  width: 8px; height: 8px;
  background: #ffd700;
  border-radius: 50%;
  box-shadow: 0 0 8px #ffd700;
  animation: pulse 1.5s infinite alternate;
}
.badge-text {
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 2px;
  color: #ffd700;
}

/* Shimmer Icon */
.coming-soon-icon {
  font-size: 64px;
}
.gold-shimmer-text {
  background: linear-gradient(135deg, #b8860b 0%, #ffd700 50%, #e5a93b 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  animation: shimmer 3s infinite linear;
  background-size: 200% auto;
}

/* Typography */
.coming-soon-title {
  font-size: 42px;
  font-weight: 900;
  letter-spacing: 8px;
  color: #ffffff;
  text-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.text-gradient-gold {
  background: linear-gradient(135deg, #b8860b 0%, #ffd700 45%, #e5a93b 80%, #ffffff 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-size: 24px;
  font-weight: 700;
}

.feature-desc {
  font-size: 15px;
  line-height: 1.7;
  color: #94a3b8;
  font-weight: 300;
  max-width: 480px;
  margin: 0 auto;
}

/* Luxury Gold Button */
.btn-back-gold {
  background: linear-gradient(135deg, #ffd700 0%, #e5a93b 50%, #b8860b 100%) !important;
  color: #0f111a !important;
  border: none !important;
  font-size: 14px;
  letter-spacing: 1px;
  box-shadow: 0 8px 25px rgba(229, 169, 59, 0.25) !important;
  transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
  cursor: pointer;
}
.btn-back-gold:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 12px 30px rgba(229, 169, 59, 0.4) !important;
}
.btn-back-gold:active {
  transform: translateY(1px);
}

/* Animations */
@keyframes floatGold {
  0% { transform: translateY(0) scale(1); }
  100% { transform: translateY(-30px) scale(1.08); }
}
@keyframes floatDark {
  0% { transform: translateY(0) scale(1); }
  100% { transform: translateY(20px) scale(1.05); }
}
@keyframes pulse {
  0% { opacity: 0.6; }
  100% { opacity: 1; }
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
@keyframes shimmer {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}
</style>
