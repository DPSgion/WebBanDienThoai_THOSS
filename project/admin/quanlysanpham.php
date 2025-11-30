<h1>Quản lý sản phẩm</h1>

<div class="quanlysp">
    <div class="quanly-trai">
        <div class="timkiem">
            <input type="text" name="tensp" placeholder="Tìm kiếm sản phẩm">
        </div>
        <div class="themsp">
            <button>+ Thêm sản phẩm</button>
        </div>
    </div>
</div>

<!-- Bộ lọc dưới quanlysp -->

<div class="filter-container">
    <div>
        <label for="filter-cpu">CPU</label>
        <select id="filter-cpu">
            <option value="">Tất cả</option>
            <option>Snapdragon 888</option>
            <option>MediaTek Dimensity 920</option>
        </select>
    </div>
    <div>
        <label for="filter-os">OS</label>
        <select id="filter-os">
            <option value="">Tất cả</option>
            <option>Android</option>
            <option>iOS</option>
        </select>
    </div>
    <div>
        <label for="filter-ram">RAM - ROM</label>
        <select id="filter-ram">
            <option value="">Tất cả</option>
            <option>8GB - 128GB</option>
            <option>12GB - 256GB</option>
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
        <td>Điện thoại ABC</td>
        <td>Snapdragon 888</td>
        <td>8GB - 128GB</td>
        <td>4000</td>
        <td>5,000,000₫</td>
        <td>10</td>
        <td>
            <button class="btn-action btn-edit">Sửa</button>
            <button class="btn-action btn-delete">Xóa</button>
        </td>
    </tr>
    <tr>
        <td>2</td>
        <td>Điện thoại XYZ</td>
        <td>MediaTek Dimensity 920</td>
        <td>12GB - 256GB</td>
        <td>4500</td>
        <td>8,500,000₫</td>
        <td>5</td>
        <td>
            <button class="btn-action btn-edit">Sửa</button>
            <button class="btn-action btn-delete">Xóa</button>
        </td>
    </tr>
</table>

<div id="modal-them" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Thêm sản phẩm</h2>
    <form id="form-them" class="form-grid">
      <div class="form-group">
        <label>Tên sản phẩm</label>
        <input type="text" placeholder="Tên sản phẩm" required>
      </div>
      <div class="form-group">
        <label>CPU</label>
        <input type="text" placeholder="CPU">
      </div>
      <div class="form-group">
        <label>RAM - ROM</label>
        <input type="text" placeholder="RAM - ROM">
      </div>
      <div class="form-group">
        <label>Pin</label>
        <input type="number" placeholder="mAh">
      </div>
      <div class="form-group">
        <label>Giá</label>
        <input type="number" placeholder="VNĐ">
      </div>
      <div class="form-group">
        <label>Số lượng</label>
        <input type="number" placeholder="">
      </div>
      <button type="submit" class="btn-submit">Thêm sản phẩm</button>
    </form>
  </div>
</div>
