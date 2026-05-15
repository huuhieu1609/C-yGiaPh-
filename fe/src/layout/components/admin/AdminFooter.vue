<template>
  <footer class="admin-footer">
    <div class="footer-content">
      <p class="footer-text">
        © {{ currentYear }} <strong>Hệ Thống Gia Phả</strong>. Bảo lưu mọi quyền.
      </p>
      <p class="footer-version">v2.0</p>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AdminFooter',
  computed: {
    currentYear() {
      return new Date().getFullYear();
    }
  }
}
</script>

<style scoped>
.admin-footer {
  padding: 16px 28px;
  border-top: 1px solid rgba(0, 0, 0, 0.06);
  background: #fff;
}

.footer-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.footer-text {
  margin: 0;
  font-size: 13px;
  color: #888;
}

.footer-text strong {
  color: #555;
  font-weight: 600;
}

.footer-version {
  margin: 0;
  font-size: 12px;
  color: #aaa;
  background: #f0f2f5;
  padding: 2px 10px;
  border-radius: 20px;
}
</style>
