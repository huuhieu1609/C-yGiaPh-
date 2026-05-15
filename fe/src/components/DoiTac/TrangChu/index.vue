<template>
    <div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3">
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-info">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Thành Viên Chi Nhánh</p>
                                <h4 class="my-1 text-info">128</h4>
                                <p class="mb-0 font-13">Thành viên trong dòng họ</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                                <i class='bx bxs-group'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Số Đời Tộc Họ</p>
                                <h4 class="my-1 text-danger">12</h4>
                                <p class="mb-0 font-13">Thế hệ đã ghi nhận</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                                <i class='bx bxs-share-alt'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 border-start border-0 border-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">Sự Kiện Sắp Tới</p>
                                <h4 class="my-1 text-success">3</h4>
                                <p class="mb-0 font-13">Trong tháng này</p>
                            </div>
                            <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                <i class='bx bxs-calendar-event'></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--end row-->

        <div class="row">
            <div class="col-12">
                <div class="card radius-10">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div>
                                <h6 class="mb-0">Cập Nhật Thành Viên Gần Đây</h6>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Họ và Tên</th>
                                        <th>Mối Quan Hệ</th>
                                        <th>Ngày Thêm</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Nguyễn Văn Bình</td>
                                        <td>Con trai</td>
                                        <td>14/05/2026</td>
                                        <td><span class="badge bg-gradient-quepal text-white shadow-sm w-100">Đã cập
                                                nhật</span></td>
                                    </tr>
                                    <tr>
                                        <td>Trần Thị Hồng</td>
                                        <td>Vợ</td>
                                        <td>12/05/2026</td>
                                        <td><span class="badge bg-gradient-quepal text-white shadow-sm w-100">Đã cập
                                                nhật</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'PartnerDashboard'
}
</script>

<style scoped></style>
