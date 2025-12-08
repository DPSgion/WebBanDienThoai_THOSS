<?php
  session_start();
  include_once 'config/config.php'; 
  // Lấy danh mục
  function get_all_categories($pdo)
  {
    try {
      $sql = "SELECT id_danh_muc, ten_danh_muc FROM danh_muc ORDER BY ten_danh_muc ASC";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // Log lỗi
      return [];
    }
  }

  $categories = get_all_categories($pdo);
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Giỏ hàng — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCart.css">
</head>
<body>

  

  <!-- MAIN HEADER / NAV -->
  <header class="main-header">
    <div class="container header-row">
      <div class="logo-left">
        <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
      </div>

      <div class="search-center">
        <input class="search-input" placeholder="Tìm kiếm sản phẩm" />
        <button class="search-btn" aria-label="Tìm kiếm">🔍</button>
      </div>

      <div class="icons-right">
        <a href="TrangChu.php" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a id="accountLink" href="User.php">👤</a>
        <div class="danh-container">
          <button type="button" class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
             <?php foreach ($categories as $cat): ?>
              <li><a href="TimKiem.php?cat_id=<?php echo htmlspecialchars($cat['id_danh_muc']); ?>" class="danh-link"><?php echo htmlspecialchars($cat['ten_danh_muc']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- CART MAIN -->
  <main class="container cart-page">
    <div class="cart-wrapper">
      <div class="cart-header">
        <h2>GIỎ HÀNG CỦA BẠN <span class="lock">🔒</span></h2>
      </div>
      <!--SỬA-->
      <div class="select-all">
          <h4><input type="checkbox" id="selectAll"> TẤT CẢ</h4>
      </div>
      <!--END SỬA-->

      <div class="cart-list" id="cartList">
        <!-- product item template -->
        <div class="cart-item" data-price="38999000">
          <div class="item-left">
            <img src="uploads/products/iphone17.webp" alt="iPhone 17 pro max 256GB, Cam" class="item-thumb">
          </div>
          <div class="item-mid">
            <div class="item-name">iPhone 17 pro max 256GB, Cam</div>
            <div class="item-price price-red">38.999.000 đ</div>
            <div class="item-controls">
              <div class="qty-box">
                <button class="qty-btn qty-minus">−</button>
                <input class="qty-input" type="number" value="1" min="1">
                <button class="qty-btn qty-plus">+</button>
              </div>
              <button class="btn choose">CHỌN</button>
            </div>
          </div>
          <div class="item-right">
            <button class="del">×</button>
            <label class="select-wrap"><input type="checkbox" class="select-item"> Chọn</label>
          </div>
        </div>

        <div class="cart-item" data-price="14599000">
          <div class="item-left">
            <img src="uploads/products/Readme-pro.jpg" alt="Readme 256GB, Xanh lam" class="item-thumb">
          </div>
          <div class="item-mid">
            <div class="item-name">Readme 256GB, Xanh lam</div>
            <div class="item-price price-red">14.599.000 đ</div>
            <div class="item-controls">
              <div class="qty-box">
                <button class="qty-btn qty-minus">-</button>
                <input class="qty-input" type="number" value="1" min="1">
                <button class="qty-btn qty-plus">+</button>
              </div>
              <button class="btn choose">CHỌN</button>
            </div>
          </div>
          <div class="item-right">
            <button class="del">×</button>
            <label class="select-wrap"><input type="checkbox" class="select-item"> Chọn</label>
          </div>
        </div>

        <div class="cart-item" data-price="17599000">
          <div class="item-left">
            <img src="uploads/products/iPhone 15 pro max.webp" alt="iPhone 17 pro max 256GB, Hồng" class="item-thumb">
          </div>
          <div class="item-mid">
            <div class="item-name">iPhone 17 pro max 256GB, Hồng</div>
            <div class="item-price price-red">17.599.000 đ</div>
            <div class="item-controls">
              <div class="qty-box">
                <button class="qty-btn qty-minus">−</button>
                <input class="qty-input" type="number" value="1" min="1">
                <button class="qty-btn qty-plus">+</button>
              </div>
              <button class="btn choose">CHỌN</button>
            </div>
          </div>
          <div class="item-right">
            <button class="del">×</button>
            <label class="select-wrap"><input type="checkbox" class="select-item"> Chọn</label>
          </div>
        </div>

      </div>

      <div class="cart-footer">
        
        <div class="summary">
          <button id="totalBtn" class="btn total">TỔNG CỘNG: 0 VND</button>
          <a id="checkout" class="btn checkout" href="ThanhToan.php">THANH TOÁN</a>
        </div>
      </div>

      <!--SỬA-->
      
    </div>
  </main>
  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-grid">
      <div class="col">
        <h4>ĐIỆN THOẠI TRỰC TUYẾN</h4>
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 1</h4>
        <p>Họ & Tên: <a href="#">...</a></p>
        
        <p>MSSV: <a href="#">...</a></p>
        
        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 2</h4>
        <p>Họ & Tên: <a href="#">...</a></p>
        
        <p>MSSV: <a href="#">...</a></p>
        
        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
      <div class="col">
        <!--SỬA-->
        <h4>THÀNH VIÊN 3</h4>
        <p>Họ & Tên: <a href="#">...</a></p>
        
        <p>MSSV: <a href="#">...</a></p>
        
        <p>Email: <a href="#">...</a></p>
        <!--END SỬA-->
      </div>
    </div>
    <!--SỬA-->
    <div class="footer-bottom">© 2025 ĐỀ TÀI XÂY DỰNG WEB BÁN ĐIỆN THOẠI TRỰC TUYẾN</div>
  </footer>

  <script>
    // Cart JS: selection, qty, total, delete
    (function(){
      function formatVND(n){
        return n.toLocaleString('vi-VN') + ' VND';
      }

      const cartList = document.getElementById('cartList');
      const totalBtn = document.getElementById('totalBtn');
      const selectAll = document.getElementById('selectAll');

      function computeTotal(){
        let sum = 0;
        cartList.querySelectorAll('.cart-item').forEach(item=>{
          const chk = item.querySelector('.select-item');
          if(chk && chk.checked){
            const price = Number(item.dataset.price || 0);
            const qty = Number(item.querySelector('.qty-input').value || 1);
            sum += price * qty;
          }
        });
        totalBtn.textContent = 'TỔNG CỘNG: ' + formatVND(sum);
      }

      // quantity handlers
      cartList.addEventListener('click', (e)=>{
        if(e.target.matches('.qty-plus')){
          const input = e.target.parentElement.querySelector('.qty-input');
          input.value = Math.max(1, Number(input.value) + 1);
          computeTotal();
        } else if(e.target.matches('.qty-minus')){
          const input = e.target.parentElement.querySelector('.qty-input');
          input.value = Math.max(1, Number(input.value) - 1);
          computeTotal();
        } else if(e.target.matches('.del')){
          const item = e.target.closest('.cart-item');
          if(item) item.remove();
          computeTotal();
        } else if(e.target.matches('.choose')){
          const item = e.target.closest('.cart-item');
          const chk = item.querySelector('.select-item');
          if(chk){ chk.checked = !chk.checked; }
          computeTotal();
        }
      });

      // checkbox change handlers
      cartList.addEventListener('change', (e)=>{
        if(e.target.matches('.select-item')){
          computeTotal();
        }
        if(e.target.matches('.qty-input')){
          e.target.value = Math.max(1, Number(e.target.value));
          computeTotal();
        }
      });

      selectAll.addEventListener('change', ()=>{
        const checked = selectAll.checked;
        cartList.querySelectorAll('.select-item').forEach(c=> c.checked = checked);
        computeTotal();
      });

      // initial total
      computeTotal();
    })();
  </script>
    <script>
      // danh mục dropdown (shared behavior)
      (function(){
        document.querySelectorAll('.danh-container').forEach(dc=>{
          const btn = dc.querySelector('.danh-muc');
          const menu = dc.querySelector('.danh-menu');
          if(!btn || !menu) return;
          btn.addEventListener('click', (e)=>{ e.stopPropagation(); dc.classList.toggle('open'); btn.setAttribute('aria-expanded', dc.classList.contains('open'))});
          menu.addEventListener('click', (e)=> e.stopPropagation());
        });
        document.addEventListener('click', ()=> document.querySelectorAll('.danh-container').forEach(dc=>{ dc.classList.remove('open'); dc.querySelector('.danh-muc')?.setAttribute('aria-expanded','false'); }));
      })();
    </script>

</body>
</html>
