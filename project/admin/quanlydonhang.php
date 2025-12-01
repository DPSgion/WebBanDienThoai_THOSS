
<div class="quanlydonhang">
    <h1>Quản lý đơn hàng</h1>
    <div class="quanlydonhang-header">
        <input type="text" class="search-input" placeholder="Tìm kiếm mã đơn, tên, sđt">
        <select class="status-filter">
            <option value="all">Tất cả trạng thái</option>
            <option value="pending">Chờ xử lý</option>
            <option value="shipping">Đang giao</option>
            <option value="completed">Hoàn thành</option>
        </select>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Mã đơn hàng</th>
                <th>Tên khách hàng</th>
                <th>SĐT</th>
                <th>Đơn hàng</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>DH001</td>
                <td>Nguyễn Văn A</td>
                <td>0123456789</td>
                <td>
                    <div class="order-item">3 x iPhone 15, Màu Đỏ, 8GB-128GB</div>
                    <div class="order-item">1 x Samsung S23, Màu Đen, 12GB-256GB</div>
                </td>
                <td>100,000,000 VNĐ</td>
                <td><p class="status-wait">Chờ xử lý</p></td>
                <td>01/12/2025</td>
                <td><button class="btn btn-approve">Duyệt</button></td>
            </tr>
            <tr>
                <td>DH002</td>
                <td>Trần Thị B</td>
                <td>0987654321</td>
                <td>
                    <div class="order-item">2 x iPhone 15, Màu Xanh, 12GB-256GB</div>
                </td>
                <td>30,000,000 VNĐ</td>
                <td><p class="status-shipping">Đang giao</p></td>
                <td>30/11/2025</td>
                <td><button class="btn btn-complete">Hoàn thành</button></td>
            </tr>
            <tr>
                <td>DH003</td>
                <td>Phạm Văn C</td>
                <td>0912345678</td>
                <td>
                    <div class="order-item">1 x iPhone 15, Màu Trắng, 8GB-128GB</div>
                </td>
                <td>13,000,000 VNĐ</td>
                <td><p class="status-completed">Hoàn thành</p></td>
                <td>28/11/2025</td>
                <td><span class="status-completed">Hoàn thành (29/11/2025)</span></td>
            </tr>
        </tbody>
    </table>

</div>


