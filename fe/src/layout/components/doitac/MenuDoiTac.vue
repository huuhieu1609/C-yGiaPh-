<template>
	<div class="nav-container primary-menu">
		<div class="mobile-topbar-header">
			<div>
				<img src="../../../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
			</div>
			<div>
				<h4 class="logo-text">Đối Tác</h4>
			</div>
			<div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
			</div>
		</div>
		<nav class="navbar navbar-expand-xl w-100">
			<ul class="navbar-nav justify-content-start flex-grow-1 gap-1">
				<li class="nav-item">
					<router-link to="/doi-tac/dashboard" class="nav-link">
						<div class="parent-icon"><i class='bx bx-home-circle'></i>
						</div>
						<div class="menu-title">Dashboard</div>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/doi-tac/dong-ho" class="nav-link">
						<div class="parent-icon"><i class='bx bx-building-house'></i>
						</div>
						<div class="menu-title">Quản Lý Dòng Họ</div>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/doi-tac/gia-pha" class="nav-link">
						<div class="parent-icon"><i class='bx bx-git-branch'></i>
						</div>
						<div class="menu-title">Cây Gia Phả</div>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/doi-tac/thanh-vien" class="nav-link">
						<div class="parent-icon"><i class='bx bx-group'></i>
						</div>
						<div class="menu-title">Quản Lý Thành Viên</div>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/doi-tac/tra-cuu" class="nav-link">
						<div class="parent-icon"><i class='bx bx-search-alt'></i>
						</div>
						<div class="menu-title">Tra Cứu</div>
					</router-link>
				</li>
			</ul>
		</nav>
	</div>
</template>

<script>
export default {
	name: 'MenuDoiTac'
}
</script>

<style scoped>
</style>
