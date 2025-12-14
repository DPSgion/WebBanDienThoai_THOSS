<h1>Quản lý đơn hàng</h1>
<div class="quanlysp">
    <div class="quanly-trai" style="display:flex; gap:10px;">
        <input type="text" id="searchDonHang" placeholder="Tìm mã đơn, tên, sđt...">
        <select id="filterStatus" style="padding:5px">
            <option value="all">Tất cả</option>
            <option value="choxuly">Chờ xử lý</option>
            <option value="danggiao">Đang giao</option>
            <option value="hoanthanh">Hoàn thành</option>
            <option value="dahuy">Đã hủy</option>
        </select>
        <button onclick="loadTableDonHang()">Tìm</button>
    </div>
</div>

<table class="table-sp">
    <thead>
        <tr>
            <th>Mã</th><th>Khách hàng</th><th>Ngày đặt</th><th>Tổng tiền</th><th>Trạng thái</th><th>Hành động</th>
        </tr>
    </thead>
    <tbody id="tableDonHang"></tbody>
</table>

<div id="modalDonHang" class="modalDM" style="display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#fff; padding:20px; width:70%; max-height:80vh; overflow-y:auto; border-radius:5px;">
        <h2 id="modalTitle">Chi tiết đơn hàng #<span id="detailMaDon"></span></h2>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px">
            <div>
                <p>KH: <b id="detailTenKH"></b></p>
                <p>SĐT: <b id="detailSDT"></b></p>
                <p>Đ/c: <span id="detailDiaChi"></span></p>
            </div>
            <div style="text-align:right">
                <p>Ngày: <span id="detailNgayDat"></span></p>
                <p>Tổng: <b id="detailTongTien" style="color:red"></b></p>
            </div>
        </div>
        <table class="table-sp">
            <thead><tr><th>Tên SP</th><th>Loại</th><th>SL</th><th>Giá</th><th>Thành tiền</th></tr></thead>
            <tbody id="tableChiTietDonHang"></tbody>
        </table>
        <div class="modal-actions" style="margin-top:20px; text-align:right">
            <div id="actionButtons" style="display:inline-block"></div>
            <button class="btnDong" id="closeModalDonHangBtn">Đóng</button>
        </div>
    </div>
</div>