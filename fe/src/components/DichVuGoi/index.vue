<template>
    <div class="package-page">
        <br>
        <br>
        <br>
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="package-card shadow-sm rounded-4 overflow-hidden">
                        <div class="package-hero p-4 text-white d-flex flex-column justify-content-between">
                            <div>
                                <span class="badge bg-warning text-dark mb-3">Dịch Vụ Gói</span>
                                <h3 class="mb-2 title-package">Gói Gia Phả Tộc</h3>
                                <p class="mb-0 opacity-75">Chỉ tạo được duy nhất 1 cây gia phả của tộc mình.</p>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <div>
                                    <div class="text-white-50 small">Giá</div>
                                    <div class="package-price">20.000đ</div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted mb-0">Gói dịch vụ gồm một cây gia phả duy nhất cho dòng họ của bạn, với khả năng cập nhật và tra cứu thông tin thành viên.</p>

                            <button type="button" class="btn btn-primary w-100 mt-3" @click="viewDetail">
                                Xem ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DichVuGoi',
    methods: {
        viewDetail() {
            const isAuthenticated = localStorage.getItem('access_token');
            if (!isAuthenticated) {
                this.$router.push({
                    path: '/login',
                    query: { redirect: '/dich-vu-goi/chi-tiet' }
                });
                return;
            }
            this.$router.push('/dich-vu-goi/chi-tiet');
        }
    }
}
</script>

<style scoped>
.package-page {
    min-height: 100vh;
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 45%, #e2e8f0 100%);
    padding: 120px 0 80px;
}

/* CARD */
.package-card {
    background: rgba(255, 255, 255, 0.96);
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(8px);
    transition: all 0.35s ease;
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.18);
}

.package-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
}

/* HERO */
.package-hero {
    position: relative;
    min-height: 260px;
    padding: 32px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    background:
        linear-gradient(
            180deg,
            rgba(0, 0, 0, 0.35) 0%,
            rgba(0, 0, 0, 0.55) 100%
        ),
        url('@/assets/images/card_product.png');

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

/* Badge */
.package-hero .badge {
    width: fit-content;
    padding: 8px 14px;
    font-size: 0.8rem;
    font-weight: 600;
    border-radius: 999px;
    background: #facc15 !important;
    color: #1e293b !important;
    box-shadow: 0 4px 10px rgba(250, 204, 21, 0.35);
}

/* TITLE */
.title-package {
    font-size: 2.3rem;
    font-weight: 800;
    color: #ffffff;
    text-shadow: 0 3px 10px rgba(0, 0, 0, 0.35);
    margin-bottom: 10px;
}

/* DESCRIPTION */
.package-hero p {
    color: rgba(255, 255, 255, 0.92);
    font-size: 1rem;
    line-height: 1.6;
    max-width: 90%;
}

/* PRICE */
.package-price {
    font-size: 2.4rem;
    font-weight: 800;
    color: #ffffff;
    text-shadow: 0 3px 12px rgba(0, 0, 0, 0.35);
}

.text-white-50.small {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 0.95rem;
}

/* BODY */
.card-body {
    background: #ffffff;
}

.card-body p {
    font-size: 1rem;
    line-height: 1.7;
    color: #475569 !important;
    font-weight: 500;
}

/* BUTTON */
.btn-primary {
    border: none;
    padding: 14px;
    border-radius: 14px;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: 0.3px;

    background: linear-gradient(
        135deg,
        #2563eb 0%,
        #7c3aed 100%
    );

    transition: all 0.3s ease;
    box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
}

.btn-primary:hover {
    transform: translateY(-2px);
    background: linear-gradient(
        135deg,
        #7c3aed 0%,
        #2563eb 100%
    );
    box-shadow: 0 12px 22px rgba(124, 58, 237, 0.4);
}

/* MOBILE */
@media (max-width: 768px) {
    .package-page {
        padding: 90px 15px 50px;
    }

    .package-hero {
        min-height: 230px;
        padding: 24px;
    }

    .title-package {
        font-size: 1.8rem;
    }

    .package-price {
        font-size: 2rem;
    }

    .card-body p {
        font-size: 0.95rem;
    }
}
</style>