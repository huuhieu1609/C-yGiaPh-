<template>
    <div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-total">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Tổng Thành Viên</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">2,564</h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-trending-up"></i> +2.5% so với tháng trước</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-group'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-tree">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Cây Gia Phả</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">425</h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-trending-up"></i> +5.4% so với tháng trước</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-share-alt'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-donation">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Đóng Góp Di Sản</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">€34,652</h3>
                                <p class="stat-desc mb-0 text-danger"><i class="bx bx-trending-down"></i> -4.5% so với tháng trước</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-wallet'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card luxury-stat-card border-0 shadow-sm item-digitize">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="stat-content flex-grow-1">
                                <p class="stat-label mb-1">Yêu Cầu Số Hóa</p>
                                <h3 class="stat-number my-1 fw-bold text-dark">132</h3>
                                <p class="stat-desc mb-0 text-success"><i class="bx bx-trending-up"></i> +8.4% so với tháng trước</p>
                            </div>
                            <div class="stat-icon-box rounded-circle">
                                <i class='bx bxs-cloud-upload'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-4 border-0 d-flex align-items-center">
                        <h6 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-time-five me-1 text-emerald"></i> Cập nhật Gia Phả Gần Đây
                        </h6>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret custom-dropdown-trigger" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded'></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end luxury-dropdown border-0 shadow">
                                <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-list-ul me-2"></i> Xem tất cả</a></li>
                                <li><a class="dropdown-item" href="javascript:;"><i class="bx bx-export me-2"></i> Xuất báo cáo</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="table-responsive rounded-3 border border-light-subtle">
                            <table class="table modern-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Thành viên</th>
                                        <th>Dòng họ</th>
                                        <th>Ngày cập nhật</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name=Nguyen+An&background=eef2ff&color=4f46e5" class="rounded-circle shadow-sm" width="36" height="36">
                                                <span class="fw-bold text-dark">Nguyễn Văn A</span>
                                            </div>
                                        </td>
                                        <td class="text-secondary fw-semibold">Nguyễn Tộc</td>
                                        <td class="text-muted small fw-medium">12/05/2026</td>
                                        <td class="text-center">
                                            <span class="badge status-badge badge-approved">Đã duyệt</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action-edit" title="Sửa"><i class='bx bx-edit-alt'></i></button>
                                                <button class="btn btn-action-delete" title="Xóa"><i class='bx bx-trash'></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name=Tran+Binh&background=fff7ed&color=ea580c" class="rounded-circle shadow-sm" width="36" height="36">
                                                <span class="fw-bold text-dark">Trần Thị B</span>
                                            </div>
                                        </td>
                                        <td class="text-secondary fw-semibold">Trần Gia</td>
                                        <td class="text-muted small fw-medium">11/05/2026</td>
                                        <td class="text-center">
                                            <span class="badge status-badge badge-pending">Chờ duyệt</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action-edit" title="Sửa"><i class='bx bx-edit-alt'></i></button>
                                                <button class="btn btn-action-delete" title="Xóa"><i class='bx bx-trash'></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name=Le+Chi&background=fef2f2&color=ef4444" class="rounded-circle shadow-sm" width="36" height="36">
                                                <span class="fw-bold text-dark">Lê Văn C</span>
                                            </div>
                                        </td>
                                        <td class="text-secondary fw-semibold">Lê Tộc</td>
                                        <td class="text-muted small fw-medium">10/05/2026</td>
                                        <td class="text-center">
                                            <span class="badge status-badge badge-rejected">Từ chối</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-action-edit" title="Sửa"><i class='bx bx-edit-alt'></i></button>
                                                <button class="btn btn-action-delete" title="Xóa"><i class='bx bx-trash'></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card luxury-panel border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent py-4 border-0">
                        <h6 class="mb-0 fw-bold panel-title text-dark">
                            <i class="bx bx-pulse me-1 text-emerald"></i> Hoạt động Hệ thống
                        </h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="traffic-source-container d-flex flex-column gap-4">
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-indigo rounded-circle">
                                    <i class='bx bxs-user-plus'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Người dùng mới</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">15 tài khoản đăng ký mới hôm nay</h6>
                                </div>
                            </div>
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-emerald rounded-circle">
                                    <i class='bx bxs-image-add'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Ảnh di sản</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">45 tệp tin ảnh mới được tải lên</h6>
                                </div>
                            </div>
                            <div class="traffic-source d-flex align-items-center">
                                <div class="activity-icon-box box-amber rounded-circle">
                                    <i class='bx bxs-bell'></i>
                                </div>
                                <div class="ms-3">
                                    <p class="activity-label mb-0">Thông báo bảo mật</p>
                                    <h6 class="activity-desc mb-0 fw-bold text-dark">3 yêu cầu khôi phục mật khẩu</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AdminDashboard'
}
</script>

<style scoped>
/* ─── CONTEMPORARY SYSTEM LUXURY CONTROLS ─── */
.luxury-panel {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 20px !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
}

.panel-title {
    font-size: 14.5px;
    letter-spacing: 0.3px;
}
.text-emerald { color: #059669 !important; }

/* ─── CARD THỐNG KÊ PHỒNG KHỐI TRẮNG VIP ─── */
.luxury-stat-card {
    background: #ffffff !important;
    border: 1px solid rgba(0, 0, 0, 0.05) !important;
    border-radius: 18px !important;
    box-shadow: 0 6px 20px rgba(0,0,0,0.015);
}
.stat-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
}
.stat-number {
    font-size: 26px;
    color: #111827;
}
.stat-desc {
    font-size: 11.5px;
    font-weight: 500;
}
.stat-desc i { font-size: 13px; vertical-align: -1px; }

/* Ô tròn icon bọc kén mờ phát sáng nhẹ */
.stat-icon-box {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    border: 1px solid transparent;
}
.item-total .stat-icon-box { background: #e0f2fe; color: #0284c7; border-color: rgba(2, 132, 199, 0.1); }
.item-tree .stat-icon-box { background: #fff5f7; color: #db2777; border-color: rgba(219, 39, 119, 0.1); }
.item-donation .stat-icon-box { background: #f0fdf4; color: #059669; border-color: rgba(5, 150, 105, 0.1); }
.item-digitize .stat-icon-box { background: #fef3c7; color: #d97706; border-color: rgba(217, 119, 6, 0.1); }

/* ─── LUXURY LIGHT TABLE SYSTEM ─── */
.modern-table { width: 100%; }
.modern-table thead { background: #f4f5f7; }
.modern-table thead th {
    color: #4b5563;
    font-weight: 700;
    font-size: 12.5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.06);
    padding: 14px 16px;
}
.modern-table tbody tr { transition: background 0.2s ease; }
.modern-table tbody tr:hover { background-color: #fdfdfd; }
.modern-table tbody td {
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.03);
    font-size: 14px;
}

/* Kiểu dáng thuốc nhuộm hạt kén cho Trạng thái duyệt */
.status-badge {
    font-size: 11.5px;
    font-weight: 600;
    padding: 5px 14px;
    border-radius: 30px;
    display: inline-block;
    width: 95px;
}
.badge-approved { background-color: #f0fdf4 !important; color: #16a34a !important; border: 1px solid rgba(22, 163, 74, 0.15); }
.badge-pending { background-color: #fff7ed !important; color: #ea580c !important; border: 1px solid rgba(234, 88, 12, 0.15); }
.badge-rejected { background-color: #fef2f2 !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.15); }

/* Nút hành động mini tinh xảo */
.btn-action-edit, .btn-action-delete {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.06);
    font-size: 14px;
    border-radius: 6px;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}
.btn-action-edit { color: #d97706; }
.btn-action-edit:hover { background: #d97706; color: #fff; border-color: transparent; }
.btn-action-delete { color: #dc3545; }
.btn-action-delete:hover { background: #dc3545; color: #fff; border-color: transparent; }

/* ─── DROPDOWN & THREE DOTS LUXURY ─── */
.custom-dropdown-trigger {
    color: #9ca3af;
    font-size: 20px;
    transition: color 0.2s ease;
}
.custom-dropdown-trigger:hover { color: #111827; }
.luxury-dropdown {
    background: #ffffff;
    border-radius: 12px;
    padding: 6px;
}
.luxury-dropdown .dropdown-item {
    font-size: 13.5px;
    font-weight: 500;
    color: #4b5563;
    padding: 8px 14px;
    border-radius: 8px;
}
.luxury-dropdown .dropdown-item:hover {
    background: #f9fafb;
    color: #111827;
}

/* ─── TRAFFIC SYSTEM ACTIVITY (Hoạt động hệ thống) ─── */
.activity-icon-box {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.box-indigo { background: #eef2ff; color: #4f46e5; }
.box-emerald { background: #f0fdf4; color: #059669; }
.box-amber { background: #fffbeb; color: #d97706; }

.activity-label {
    font-size: 11.5px;
    font-weight: 600;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.activity-desc {
    font-size: 13.5px;
    color: #111827;
    margin-top: 2px;
}
</style>