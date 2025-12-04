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
    <thead>
        <tr>
            <th>STT</th>
            <th>Tên danh mục</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody id="tableDanhMuc">
        <!-- Dữ liệu sẽ được load bằng JavaScript -->
    </tbody>
</table>

<!-- Modal thêm danh mục -->
<div id="modalDanhMuc" class="modalDM">
    <div class="modal-content">
        <h2>Thêm danh mục</h2>

        <form id="formDanhMuc">
            <input type="text" id="tenDanhMucInput" name="name" placeholder="Nhập tên danh mục">

            <div class="modal-actions">
                <button type="button" class="btnDong" id="closeModalDanhMucBtn">Đóng</button>
                <button type="submit" class="btnLuu">Lưu</button>
            </div>
        </form>

    </div>
</div>

