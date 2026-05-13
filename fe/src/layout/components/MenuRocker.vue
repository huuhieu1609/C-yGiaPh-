<template>
	<div class="nav-container primary-menu">
		<div class="mobile-topbar-header">
			<div>
				<img src="../../assets/images/logo-icon.png" class="logo-icon" alt="logo icon">
			</div>
			<div>
				<h4 class="logo-text">Gia Phả</h4>
			</div>
			<div class="toggle-icon ms-auto"><i class='bx bx-arrow-to-left'></i>
			</div>
		</div>
		<nav class="navbar navbar-expand-xl w-100">
			<ul class="navbar-nav justify-content-start flex-grow-1 gap-1">
				<li class="nav-item" v-if="checkPermission('Admin Dashboard')">
					<router-link to="/admin/dashboard" class="nav-link">
						<div class="parent-icon"><i class='bx bx-home-circle'></i>
						</div>
						<div class="menu-title">Tổng Quan</div>
					</router-link>
				</li>
				<li class="nav-item" v-if="checkPermission('Cây Gia Phả')">
					<router-link to="/admin/gia-pha" class="nav-link">
						<div class="parent-icon"><i class='bx bx-git-branch'></i>
						</div>
						<div class="menu-title">Cây Gia Phả</div>
					</router-link>
				</li>
				<li class="nav-item" v-if="checkPermission('Tra Cứu Xưng Hô')">
					<router-link to="/admin/tra-cuu" class="nav-link">
						<div class="parent-icon"><i class='bx bx-search-alt'></i>
						</div>
						<div class="menu-title">Tra Cứu Xưng Hô</div>
					</router-link>
				</li>
				<li class="nav-item dropdown" v-if="checkPermission('Quản Lý Dòng Họ')">
					<a href="javascript:;" class="nav-link dropdown-toggle dropdown-toggle-nocaret"
						data-bs-toggle="dropdown">
						<div class="parent-icon"><i class='bx bx-sitemap'></i>
						</div>
						<div class="menu-title">Quản Lý Dòng Họ</div>
					</a>
					<ul class="dropdown-menu">
						<li v-if="checkPermission('Quản Lý Chi Nhánh')"> <router-link class="dropdown-item" to="/admin/chi-nhanh"><i class="bx bx-right-arrow-alt"></i>Chi Nhánh</router-link></li>
						<li v-if="checkPermission('Quản Lý Đời Tộc Họ')"> <router-link class="dropdown-item" to="/admin/doi-toc-ho"><i class="bx bx-right-arrow-alt"></i>Đời Tộc Họ</router-link></li>
						<li v-if="checkPermission('Quản Lý Nhà Thờ Họ')"> <router-link class="dropdown-item" to="/admin/nha-tho-ho"><i class="bx bx-right-arrow-alt"></i>Nhà Thờ Họ</router-link></li>
						<li v-if="checkPermission('Quản Lý Mộ Phần')"> <router-link class="dropdown-item" to="/admin/mo-phan"><i class="bx bx-right-arrow-alt"></i>Mộ Phần</router-link></li>
					</ul>
				</li>
				<li class="nav-item dropdown" v-if="checkPermission('Quỹ & Sự Kiện')">
					<a href="javascript:;" class="nav-link dropdown-toggle dropdown-toggle-nocaret"
						data-bs-toggle="dropdown">
						<div class="parent-icon"><i class='bx bx-calendar-event'></i>
						</div>
						<div class="menu-title">Quỹ & Sự Kiện</div>
					</a>
					<ul class="dropdown-menu">
						<li v-if="checkPermission('Quản Lý Sự Kiện')"> <router-link class="dropdown-item" to="/admin/su-kien"><i class="bx bx-right-arrow-alt"></i>Sự Kiện</router-link></li>
						<li v-if="checkPermission('Quản Lý Tham Gia Sự Kiện')"> <router-link class="dropdown-item" to="/admin/tham-gia-su-kien"><i class="bx bx-right-arrow-alt"></i>Tham Gia Sự Kiện</router-link></li>
						<li v-if="checkPermission('Quản Lý Đóng Góp')"> <router-link class="dropdown-item" to="/admin/dong-gop"><i class="bx bx-right-arrow-alt"></i>Đóng Góp (Quỹ)</router-link></li>
					</ul>
				</li>
				<li class="nav-item dropdown" v-if="checkPermission('Hệ Thống')">
					<a href="javascript:;" class="nav-link dropdown-toggle dropdown-toggle-nocaret"
						data-bs-toggle="dropdown">
						<div class="parent-icon"><i class='bx bx-cog'></i>
						</div>
						<div class="menu-title">Hệ Thống</div>
					</a>
					<ul class="dropdown-menu">
						<li v-if="checkPermission('Quản Lý Người Dùng')"> <router-link class="dropdown-item" to="/admin/nguoi-dung"><i class="bx bx-right-arrow-alt"></i>Người Dùng</router-link></li>
						<li v-if="checkPermission('Quản Lý Chức Vụ')"> <router-link class="dropdown-item" to="/admin/chuc-vu"><i class="bx bx-right-arrow-alt"></i>Chức Vụ</router-link></li>
						<li v-if="checkPermission('Quản Lý Chức Năng')"> <router-link class="dropdown-item" to="/admin/chuc-nang"><i class="bx bx-right-arrow-alt"></i>Chức Năng</router-link></li>
						<li v-if="checkPermission('Nhật Ký Hoạt Động')"> <router-link class="dropdown-item" to="/admin/nhat-ky-hoat-dong"><i class="bx bx-right-arrow-alt"></i>Nhật Ký Hoạt Động</router-link></li>
					</ul>
				</li>
			</ul>
		</nav>
	</div>
</template>

<script>
import axios from 'axios';

export default {
	name: 'MenuRocker',
	data() {
		return {
			permissions: [],
			user: {}
		}
	},
	mounted() {
		this.loadUser();
	},
	methods: {
		loadUser() {
			const token = localStorage.getItem('access_token');
			if (token) {
				axios.get('http://127.0.0.1:8000/api/me', {
					headers: { Authorization: `Bearer ${token}` }
				})
				.then(res => {
					this.user = res.data.user;
					this.permissions = res.data.permissions || [];
				})
				.catch(() => {
					this.permissions = [];
				});
			}
		},
		checkPermission(permissionName) {
			// Nếu chưa load xong user hoặc là Admin thì cho xem hết
			if (!this.user || Object.keys(this.user).length === 0 || this.user.vai_tro === 'Admin') {
				return true; 
			}
			// Chỉ lọc quyền cho các vai trò khác (nhân viên, thành viên...)
			return this.permissions.includes(permissionName);
		}
	}
}
</script>

<style>
</style>