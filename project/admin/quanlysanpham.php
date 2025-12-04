<h1>Quản lý sản phẩm</h1>

<div class="quanlysp">
    <div class="quanly-trai">
        <div class="timkiem">
            <input type="text" name="tensp" placeholder="Tìm kiếm sản phẩm">
        </div>
        <div class="themsp">
            <button id="openModalBtn">+ Thêm sản phẩm</button>
        </div>
    </div>
</div>

<!-- Bộ lọc -->
<div class="filter-container">
    <div>
        <label for="filter-os">OS</label>
        <select id="filter-os">
            <option value="">Tất cả</option>
            <option>Android</option>
            <option>iOS</option>
        </select>
    </div>

    <div>
        <label for="filter-ram">RAM</label>
        <select id="filter-ram">
            <option value="">Tất cả</option>
            <option>4GB</option>
            <option>6GB</option>
            <option>8GB</option>
            <option>12GB</option>
        </select>
    </div>

    <div>
        <label for="filter-rom">ROM</label>
        <select id="filter-rom">
            <option value="">Tất cả</option>
            <option>64GB</option>
            <option>128GB</option>
            <option>256GB</option>
            <option>512GB</option>
        </select>
    </div>

    <div>
        <label for="filter-pin">Pin ≥</label>
        <input type="number" id="filter-pin" placeholder="mAh">
    </div>

    <div>
        <label for="filter-gia">Giá ≤</label>
        <input type="number" id="filter-gia" placeholder="VNĐ">
    </div>

    <div>
        <label for="filter-soluong">Số lượng ≥</label>
        <input type="number" id="filter-soluong" placeholder="">
    </div>

    <div>
        <label for="sort-by">Sắp xếp</label>
        <select id="sort-by">
            <option value="">Mặc định</option>
            <option value="gia-asc">Giá tăng dần</option>
            <option value="gia-desc">Giá giảm dần</option>
            <option value="soluong-asc">Số lượng tăng dần</option>
            <option value="soluong-desc">Số lượng giảm dần</option>
        </select>
    </div>
</div>


<table class="table-sp">
    <tr>
        <th>ID</th>
        <th>Tên sản phẩm</th>
        <th>CPU</th>
        <th>RAM - ROM</th>
        <th>Pin</th>
        <th>Giá</th>
        <th>Số lượng</th>
        <th>Hành động</th>
    </tr>
    <tr>
        <td>1</td>
        <td>Samsung Galaxy S21</td>
        <td>Snapdragon 888</td>
        <td>8GB - 128GB</td>
        <td>4000 mAh</td>
        <td>6.500.000 VNĐ</td>
        <td>50</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>
    <tr>
        <td>1</td>
        <td>Samsung Galaxy S21</td>
        <td>Snapdragon 888</td>
        <td>12GB - 256GB</td>
        <td>4000 mAh</td>
        <td>7.900.000 VNĐ</td>
        <td>50</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>

    <tr>
        <td>2</td>
        <td>iPhone 13</td>
        <td>A15 Bionic</td>
        <td>4GB - 128GB</td>
        <td>3240 mAh</td>
        <td>15.500.000 VNĐ</td>
        <td>30</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>
    <tr>
        <td>2</td>
        <td>iPhone 13</td>
        <td>A15 Bionic</td>
        <td>4GB - 256GB</td>
        <td>3240 mAh</td>
        <td>17.500.000 VNĐ</td>
        <td>30</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>
    <tr>
        <td>2</td>
        <td>iPhone 13</td>
        <td>A15 Bionic</td>
        <td>4GB - 512GB</td>
        <td>3240 mAh</td>
        <td>20.900.000 VNĐ</td>
        <td>30</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>

    <tr>
        <td>3</td>
        <td>Xiaomi 12T</td>
        <td>Dimensity 8100</td>
        <td>8GB - 128GB</td>
        <td>5000 mAh</td>
        <td>8.200.000 VNĐ</td>
        <td>80</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>
    <tr>
        <td>3</td>
        <td>Xiaomi 12T</td>
        <td>Dimensity 8100</td>
        <td>8GB - 256GB</td>
        <td>5000 mAh</td>
        <td>9.200.000 VNĐ</td>
        <td>80</td>
        <td>
            <button class="btn-edit">Sửa</button>
            <button class="btn-delete">Xóa</button>
        </td>
    </tr>

</table>


<!-- Modal -->
<div id="modal-them" class="modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card" role="document">
    <div class="modal-header">
      <div>
        <div id="modalTitle" class="modal-title">Thêm sản phẩm</div>
        <div class="modal-desc">Nhập thông tin chi tiết sản phẩm. Thêm các option RAM + ROM + Giá bằng nút "Thêm option".</div>
      </div>
      <div>
        <button class="close" id="closeModalBtn" aria-label="Đóng">&times;</button>
      </div>
    </div>

    <form id="form-them" autocomplete="off">
      <div class="info-grid">
        <div class="form-group">
          <label for="name">Tên sản phẩm *</label>
          <input id="name" name="name" type="text" placeholder="Ví dụ: Samsung Galaxy X..." required>
        </div>
        <div class="form-group">
          <label for="name">Danh mục *</label>
          <select id="danhmuc" name="danhmuc" required>
            <option value="">-- Chọn danh mục --</option>
            
          </select>
        </div>

        <div class="form-group">
          <label for="os">OS *</label>
          <select id="os" name="os" required>
            <option value="">-- Chọn OS --</option>
            <option value="android">Android</option>
            <option value="ios">iOS</option>
          </select>
        </div>

        <div class="form-group">
          <label for="cpu">CPU</label>
          <input id="cpu" name="cpu" type="text" placeholder="Ví dụ: Snapdragon 8 Gen..." >
        </div>

        <div class="form-group">
          <label for="screen">Màn hình</label>
          <input id="screen" name="screen" type="text" placeholder="Ví dụ: 6.7'' AMOLED 120Hz">
        </div>

        <div class="form-group">
          <label for="front_cam">Camera trước</label>
          <input id="front_cam" name="front_cam" type="text" placeholder="Ví dụ: 12MP">
        </div>

        <div class="form-group">
          <label for="rear_cam">Camera sau</label>
          <input id="rear_cam" name="rear_cam" type="text" placeholder="Ví dụ: 50MP + 12MP + 8MP">
        </div>

        <div class="form-group">
          <label for="pin">PIN</label>
          <input id="pin" name="pin" type="text" placeholder="Ví dụ: 5000mAh">
        </div>
      </div>

      <!-- Option header + add button -->
      <div class="option-bar">
        <div class="left">
          <strong>Tùy chọn RAM - ROM - Màu - Số lượng - Giá</strong>
        </div>
        <div>
          <button type="button" id="addOptionBtn" class="btn-add">+ Thêm option</button>
        </div>
      </div>

      <!-- Options table -->
      <table class="options-table" id="optionsTable" aria-describedby="options-desc">
        <thead>
          <tr>
            <th style="width:15%;">RAM</th>
            <th style="width:15%;">ROM</th>
            <th style="width:20%;">Màu</th>
            <th style="width:15%;">Số lượng</th>
            <th style="width:20%;">Giá (VNĐ)</th>
            <th style="width:15%;">Hành động</th>
          </tr>
        </thead>
        <tbody id="optionsBody">
          <!-- rows dynamic here -->
        </tbody>
      </table>

      <div class="actions">
        <button type="button" class="btn-cancel" id="cancelBtn">Hủy</button>
        <button type="submit" class="btn-submit">Thêm sản phẩm</button>
      </div>
    </form>
  </div>
</div>

