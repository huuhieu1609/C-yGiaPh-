<template>
    <div class="card shadow-sm border-0 radius-10">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-group text-primary"></i> Quản Lý Thành Viên Dòng Họ</h5>
                </div>
                <div class="col-md-6 text-md-end">
                    <button class="btn btn-primary radius-30 px-4 shadow-sm" @click="openAddModal">
                        <i class="bx bx-plus"></i> Thêm Thành Viên
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3 g-3">
                <div class="col-md-4">
                    <div class="position-relative">
                        <input type="text" class="form-control ps-5 radius-10" v-model="searchQuery" placeholder="Tìm tên thành viên...">
                        <span class="position-absolute top-50 translate-middle-y start-0 ms-3 text-secondary"><i class="bx bx-search"></i></span>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select radius-10" v-model="filterDoi">
                        <option :value="null">-- Tất cả các đời --</option>
                        <option v-for="doi in listDoiTocHo" :key="doi.id" :value="doi.so_doi">Đời {{ doi.so_doi }}</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-uppercase small fw-bold">
                        <tr>
                            <th>Thành Viên</th>
                            <th>Đời Thứ</th>
                            <th>Mối Quan Hệ</th>
                            <th>Ngày Sinh</th>
                            <th>Trạng Thái</th>
                            <th class="text-center">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in filteredMembers" :key="item.id">
                            <td>
                                <div class="d-flex align-items-center">
                                    <img :src="item.avatar || ('https://ui-avatars.com/api/?name=' + item.ho_ten + '&background=d4af37&color=fff')" class="rounded-circle border me-3" width="45" height="45" style="object-fit: cover;">
                                    <div>
                                        <div class="fw-bold">{{ item.ho_ten }}</div>
                                        <div class="small text-muted">{{ item.gioi_tinh }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light-primary text-primary px-3">Đời {{ item.doi_thu }}</span>
                            </td>
                            <td>{{ item.loai_quan_he }}</td>
                            <td>{{ formatDate(item.ngay_sinh) }}</td>
                            <td>
                                <span v-if="item.trang_thai === 'Còn sống'" class="badge bg-success">Còn sống</span>
                                <span v-else class="badge bg-danger">Đã mất</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-warning" @click="onEdit(item)"><i class="bx bx-edit-alt"></i></button>
                                    <button class="btn btn-sm btn-outline-danger" @click="handleDelete(item.id)"><i class="bx bx-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Thêm/Sửa tương tự như trang Cây Gia Phả -->
        <div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
            <!-- ... modal content ... -->
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import toastr from 'toastr';

export default {
    name: 'PartnerMemberManagement',
    data() {
        return {
            allMembers: [],
            listDoiTocHo: [],
            searchQuery: '',
            filterDoi: null,
            // ... common data for modal ...
        }
    },
    computed: {
        filteredMembers() {
            return this.allMembers.filter(m => {
                const matchSearch = m.ho_ten.toLowerCase().includes(this.searchQuery.toLowerCase());
                const matchDoi = this.filterDoi === null || m.doi_thu == this.filterDoi;
                return matchSearch && matchDoi;
            });
        }
    },
    mounted() {
        this.loadDoiTocHo();
        this.loadData();
    },
    methods: {
        loadDoiTocHo() {
            axios.get('http://127.0.0.1:8000/api/doi-toc-ho/get-data')
                .then(res => { if(res.data.status) this.listDoiTocHo = res.data.data; });
        },
        loadData() {
            axios.get('http://127.0.0.1:8000/api/thanh-vien/get-data')
                .then(res => { if(res.data.status) this.allMembers = res.data.data; });
        },
        formatDate(date) {
            if(!date) return 'N/A';
            return new Date(date).toLocaleDateString('vi-VN');
        },
        handleDelete(id) {
            if(confirm('Xác nhận xóa thành viên này?')) {
                axios.post('http://127.0.0.1:8000/api/thanh-vien/delete', { id })
                    .then(res => {
                        if(res.data.status) {
                            toastr.success(res.data.message);
                            this.loadData();
                        }
                    });
            }
        }
    }
}
</script>
