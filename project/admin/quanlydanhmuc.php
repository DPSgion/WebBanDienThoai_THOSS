<!-- Dùng css từ quản lý sản phẩm -->

<h1>Quản lý danh mục</h1>

<div class="quanlysp">
    <div class="quanly-trai">
        <div class="timkiem">
            <input type="text" name="tendanhmuc" placeholder="Tìm kiếm danh mục">
        </div>
        <div class="themsp">
            <button id="openModalDanhMucBtn">+ Thêm danh mục</button>
        </div>
    </div>
</div>

<table class="table-sp">
    <tr>
        <th>STT</th>
        <th>Tên danh mục</th>
        <th>Hành động</th>
    </tr>
    <tr>
        <td>1</td>
        <td>Iphone</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>

</table>

<!-- Modal thêm danh mục -->
<div id="modalDanhMuc" class="modalDM">
    <div class="modal-content">
        <h2>Thêm danh mục</h2>
        <input type="text" id="tenDanhMucInput" placeholder="Nhập tên danh mục">
        
        <div class="modal-actions">
            <button class="btnDong" id="closeModalDanhMucBtn">Đóng</button>
            <button class="btnLuu" id="saveDanhMucBtn">Lưu</button>
        </div>
    </div>
</div>
