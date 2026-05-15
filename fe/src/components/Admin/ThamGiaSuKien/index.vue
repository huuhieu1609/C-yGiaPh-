<template>
    <div class="row">
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div class="card-header bg-white py-3 border-0 border-bottom">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #00b4d8;">
                        <i class="bx bx-user-check me-1"></i> Tham Gia Sự Kiện
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn Sự Kiện</label>
                        <select class="form-select radius-8 border-2 shadow-none">
                            <option>Chọn sự kiện</option>
                            <option>Lễ Khánh Thành Nhà Thờ</option>
                            <option>Tết Đoàn Viên Dòng Họ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Người Tham Gia</label>
                        <input type="text" class="form-control radius-8 border-2 shadow-none"
                            placeholder="Nhập tên người tham gia..." />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số Người</label>
                        <input type="number" class="form-control radius-8 border-2 shadow-none"
                            placeholder="Nhập số lượng..." />
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng Thái</label>
                        <select class="form-select radius-8 border-2 shadow-none">
                            <option>Đã xác nhận</option>
                            <option>Chờ xác nhận</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea rows="4" class="form-control radius-8 border-2 shadow-none"
                            placeholder="Nhập ghi chú..."></textarea>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <button type="button" class="btn btn-light radius-8 px-4">Hủy</button>
                        <button type="button" class="btn text-white radius-8 px-4 fw-bold shadow-sm"
                            style="background-color: #008bf8; border-color: #008bf8;">
                            Lưu đăng ký
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="card shadow-sm border-0 radius-10 h-100">
                <div
                    class="card-header bg-white py-3 border-0 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-uppercase" style="color: #333;">
                        <i class="bx bx-list-ul me-1"></i> Danh Sách Tham Gia
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="input-group mb-4 radius-8 overflow-hidden border-2">
                        <input type="text" class="form-control border-0 shadow-none ps-4"
                            placeholder="Tìm kiếm người tham gia..." />
                        <button class="btn btn-success px-5 fw-bold" type="button"
                            style="background-color: #00c853; border-color: #00c853;">Tìm kiếm</button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="text-center text-white" style="background-color: #008bf8;">
                                <tr>
                                    <th width="5%" class="py-3">#</th>
                                    <th width="25%" class="py-3">Tên Người Tham Gia</th>
                                    <th width="20%" class="py-3">Sự Kiện</th>
                                    <th width="15%" class="py-3">Số Người</th>
                                    <th width="15%" class="py-3">Trạng Thái</th>
                                    <th width="20%" class="py-3">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold">1</td>
                                    <td class="fw-semibold">Nguyễn Văn A</td>
                                    <td>Lễ Khánh Thành Nhà Thờ</td>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><span class="badge bg-success rounded-pill px-3 py-2">Đã xác
                                            nhận</span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8" type="button"
                                                title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" type="button"
                                                title="Xóa">
                                                <i class="bx bx-trash m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">2</td>
                                    <td class="fw-semibold">Trần Thị B</td>
                                    <td>Tết Đoàn Viên Dòng Họ</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center"><span
                                            class="badge bg-warning text-dark rounded-pill px-3 py-2">Chờ xác
                                            nhận</span></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-primary radius-8" type="button"
                                                title="Sửa">
                                                <i class="bx bx-edit-alt m-0"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger radius-8" type="button"
                                                title="Xóa">
                                                <i class="bx bx-trash m-0"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.radius-10 {
    border-radius: 10px;
}

.radius-8 {
    border-radius: 8px;
}

.table thead th {
    border-bottom: none;
    font-weight: 600;
}
</style>