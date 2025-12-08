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
  <title>Thanh toán — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesCheckout.css">
</head>
<body>
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
        <a href="GioHang.php" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </a>
        <a id="accountLink" href="User.php">👤</a>
        <div class="danh-container">
          <button class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
          <?php foreach ($categories as $cat): ?>
              <li><a href="TimKiem.php?cat_id=<?php echo htmlspecialchars($cat['id_danh_muc']); ?>" class="danh-link"><?php echo htmlspecialchars($cat['ten_danh_muc']); ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main class="container checkout-page">
    <h1>Thanh toán</h1>
    <div class="checkout-grid">
      <section class="checkout-left">
        <form id="checkoutForm">
          <h2>Thông tin người đặt</h2>
          <!--SỬA-->

          <div class="form-row">
            <label>Địa chỉ nhận hàng <input name="address" type="text" required></label>
          </div>

          <div class="form-row">
            <label>Tỉnh / Thành <input name="city" type="text" required></label>
            <label>Quận / Huyện <input name="district" type="text" required></label>
          </div>

          <!--SỬA-->

          <div class="actions">
            <button type="submit" class="btn primary"></a>Đặt hàng và thanh toán</button>
            <a class="btn outline" href="GioHang.php">Quay lại giỏ hàng</a>
          </div>
        </form>
      </section>

      <aside class="checkout-right">
        <h2>Đơn hàng của bạn</h2>
        <div class="order-list">
          <div class="order-item">
            <div class="order-card" data-price="38999000">
              <img src="uploads/products/iphone17.webp" alt="iPhone 17 pro max 256GB, Cam" class="prod-thumb">
              <div class="order-mid">
                <div class="prod-name">iPhone 17 pro max 256GB, Cam</div>
                <div class="prod-meta">256GB • Cam</div>
                <div class="qty-controls">
                  <button class="qty-btn qty-minus" aria-label="Giảm">−</button>
                  <input class="qty-input" type="number" min="1" value="1">
                  <button class="qty-btn qty-plus" aria-label="Tăng">+</button>
                </div>
              </div>
              <div class="order-price"> <span class="price-red">38.999.000đ</span> </div>
            </div>
          </div>

          <div class="order-item">
            <div class="order-card" data-price="14599000">
              <img src="uploads/products/Readme-pro.jpg" alt="Readme 256GB, Xanh lam" class="prod-thumb">
              <div class="order-mid">
                <div class="prod-name">Readme 256GB, Xanh lam</div>
                <div class="prod-meta">256GB • Xanh lam</div>
                <div class="qty-controls">
                  <button class="qty-btn qty-minus" aria-label="Giảm">−</button>
                  <input class="qty-input" type="number" min="1" value="1">
                  <button class="qty-btn qty-plus" aria-label="Tăng">+</button>
                </div>
              </div>
              <div class="order-price"> <span class="price-red">14.599.000đ</span> </div>
            </div>
          </div>

          <div class="order-item">
            <div class="order-card" data-price="17599000">
              <img src="uploads/products/iphone17.webp" alt="iPhone 17 pro max 256GB, Hồng" class="prod-thumb">
              <div class="order-mid">
                <div class="prod-name">iPhone 17 pro max 256GB, Hồng</div>
                <div class="prod-meta">256GB • Hồng</div>
                <div class="qty-controls">
                  <button class="qty-btn qty-minus" aria-label="Giảm">−</button>
                  <input class="qty-input" type="number" min="1" value="1">
                  <button class="qty-btn qty-plus" aria-label="Tăng">+</button>
                </div>
              </div>
              <div class="order-price"> <span class="price-red">17.599.000đ</span> </div>
            </div>
          </div>
        </div>

        <div class="summary">
          <div class="row"><span>Tạm tính</span><span class="value">72.998.000đ</span></div>
          <div class="row"><span>Phí vận chuyển</span><span class="value">0đ</span></div>
          <div class="row total"><span>Tổng cộng</span><span class="value">72.998.000đ</span></div>
        </div>
      </aside>
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
    // payment method toggle
    document.querySelectorAll('input[name="pay"]').forEach(r=> r.addEventListener('change', (e)=>{
      const card = document.getElementById('cardDetails');
      if(e.target.value === 'card') card.style.display = 'block'; else card.style.display = 'none';
    }));

    // simple submit handler
    document.getElementById('checkoutForm').addEventListener('submit', function(e){
      e.preventDefault();
      const fd = new FormData(this);
      if(!fd.get('name') || !fd.get('phone') || !fd.get('address')){
        alert('Vui lòng điền đầy đủ thông tin giao hàng.');
        return;
      }
      alert('Cảm ơn! Đơn hàng của bạn đã được nhận (demo).');
      window.location.href = 'TrangChu.html';
    });

    // qty controls for checkout order list + totals
    function parseNumber(str){ return Number(String(str).replace(/[^0-9]/g,'')) || 0; }
    function formatVND(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + 'đ'; }
    function recalcTotals(){
      let subtotal = 0;
      document.querySelectorAll('.order-card').forEach(card=>{
        const price = Number(card.dataset.price) || 0;
        const qty = Number(card.querySelector('.qty-input').value) || 1;
        subtotal += price * qty;
      });
      const subtEl = document.querySelector('.summary .row .value');
      // set first .value to subtotal and last to total
      const values = document.querySelectorAll('.summary .value');
      if(values.length>=1) values[0].textContent = formatVND(subtotal);
      if(values.length>=3) values[2].textContent = formatVND(subtotal);
    }

    document.querySelectorAll('.qty-minus').forEach(btn=> btn.addEventListener('click', (e)=>{
      const input = btn.parentElement.querySelector('.qty-input');
      input.value = Math.max(1, Number(input.value)-1);
      recalcTotals();
    }));
    document.querySelectorAll('.qty-plus').forEach(btn=> btn.addEventListener('click', (e)=>{
      const input = btn.parentElement.querySelector('.qty-input');
      input.value = Math.max(1, Number(input.value)+1);
      recalcTotals();
    }));
    document.querySelectorAll('.qty-input').forEach(inp=> inp.addEventListener('change', ()=>{ if(Number(inp.value)<1) inp.value=1; recalcTotals(); }));

    // initial totals
    recalcTotals();

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
