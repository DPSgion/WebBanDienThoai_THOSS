<h1>Dashboard Quản Lý</h1>
<div class="dashboard">

    <div class="container">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-info">
                        <h3>Đơn hàng mới</h3>
                        <div class="value">24</div>
                        <div class="change positive">+12% so với hôm qua</div>
                    </div>
                    <!-- <div class="stat-icon blue">🛒</div> -->
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-info">
                        <h3>Doanh thu hôm nay</h3>
                        <div class="value">45.2M đ</div>
                        <div class="change positive">+8% so với hôm qua</div>
                    </div>
                    <!-- <div class="stat-icon green">💰</div> -->
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-info">
                        <h3>Sản phẩm tồn kho</h3>
                        <div class="value">1,234</div>
                        <div class="change negative">-3% so với hôm qua</div>
                    </div>
                    <!-- <div class="stat-icon purple">📦</div> -->
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-info">
                        <h3>Tăng trưởng</h3>
                        <div class="value">23.5%</div>
                        <div class="change positive">+5% so với hôm qua</div>
                    </div>
                    <!-- <div class="stat-icon orange">📈</div> -->
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="charts-grid">
            <div class="chart-card">
                <h3>Đơn hàng theo tuần</h3>
                <canvas id="ordersChart"></canvas>
            </div>

            <div class="chart-card">
                <h3>Doanh thu theo tháng (triệu đ)</h3>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="top-products">
            <h3>Top sản phẩm bán chạy</h3>
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đã bán</th>
                        <th>Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>iPhone 15 Pro</strong></td>
                        <td>156 sản phẩm</td>
                        <td><strong>389,000,000 đ</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Samsung Galaxy S24</strong></td>
                        <td>142 sản phẩm</td>
                        <td><strong>312,000,000 đ</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Samsum Z Fold 2</strong></td>
                        <td>98 sản phẩm</td>
                        <td><strong>289,000,000 đ</strong></td>
                    </tr>
                    <tr>
                        <td><strong>iPad Pro</strong></td>
                        <td>87 sản phẩm</td>
                        <td><strong>198,000,000 đ</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Xiaomi Redmi 10T</strong></td>
                        <td>234 sản phẩm</td>
                        <td><strong>156,000,000 đ</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bottom Section -->
        <div class="bottom-grid">
            <!-- Pending Orders -->
            <div class="info-card">
                <div class="info-card-header">
                    <h3>Đơn hàng chờ xử lý</h3>
                    <span class="badge">3 mới</span>
                </div>
                <div class="order-item">
                    <div class="order-info">
                        <div class="icon-circle warning">⏱️</div>
                        <div class="order-details">
                            <h4>#DH1234</h4>
                            <p>Nguyễn Văn A</p>
                        </div>
                    </div>
                    <div class="order-amount">
                        <div class="price">2.5M đ</div>
                        <div class="time">10 phút trước</div>
                    </div>
                </div>
                <div class="order-item">
                    <div class="order-info">
                        <div class="icon-circle warning">⏱️</div>
                        <div class="order-details">
                            <h4>#DH1235</h4>
                            <p>Trần Thị B</p>
                        </div>
                    </div>
                    <div class="order-amount">
                        <div class="price">1.8M đ</div>
                        <div class="time">25 phút trước</div>
                    </div>
                </div>
                <div class="order-item">
                    <div class="order-info">
                        <div class="icon-circle warning">⏱️</div>
                        <div class="order-details">
                            <h4>#DH1236</h4>
                            <p>Lê Văn C</p>
                        </div>
                    </div>
                    <div class="order-amount">
                        <div class="price">3.2M đ</div>
                        <div class="time">1 giờ trước</div>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="info-card">
                <div class="info-card-header">
                    <h3>Hàng sắp hết</h3>
                </div>
                <div class="stock-item">
                    <div class="stock-info">
                        <div class="icon-circle alert">📦</div>
                        <div class="stock-details">
                            <h4>iPhone 15 Pro Max</h4>
                            <p>Còn 5 sản phẩm</p>
                        </div>
                    </div>
                    <span class="status-badge critical">Khẩn cấp</span>
                </div>
                <div class="stock-item">
                    <div class="stock-info">
                        <div class="icon-circle warning">📦</div>
                        <div class="stock-details">
                            <h4>Samsung Galaxy Buds</h4>
                            <p>Còn 12 sản phẩm</p>
                        </div>
                    </div>
                    <span class="status-badge warning">Cảnh báo</span>
                </div>
                <div class="stock-item">
                    <div class="stock-info">
                        <div class="icon-circle warning">📦</div>
                        <div class="stock-details">
                            <h4>Apple Watch Series 9</h4>
                            <p>Còn 8 sản phẩm</p>
                        </div>
                    </div>
                    <span class="status-badge warning">Cảnh báo</span>
                </div>
            </div>
        </div>
    </div>

</div>