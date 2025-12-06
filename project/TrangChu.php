<?php require_once './config/config.php';
session_start();
$sqlDM = "SELECT * FROM danh_muc";
$stmtDM = $pdo->prepare($sqlDM);
$stmtDM->execute();
$dsDanhMuc = $stmtDM->fetchAll(PDO::FETCH_ASSOC);

$sqlIphone = "
SELECT sp.*, bt.gia, a.duong_dan_anh 
FROM san_pham sp
JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
JOIN anh_san_pham a ON sp.id_san_pham = a.id_san_pham
JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
WHERE dm.ten_danh_muc = 'iPhone'
GROUP BY sp.id_san_pham
LIMIT 5";

$stmtIphone = $pdo->prepare($sqlIphone);
$stmtIphone->execute();
$iphoneList = $stmtIphone->fetchAll(PDO::FETCH_ASSOC);

$sqlSamsung = "
SELECT sp.*, bt.gia, a.duong_dan_anh 
FROM san_pham sp
JOIN bien_the bt ON sp.id_san_pham = bt.id_san_pham
JOIN anh_san_pham a ON sp.id_san_pham = a.id_san_pham
JOIN danh_muc dm ON sp.id_danh_muc = dm.id_danh_muc
WHERE dm.ten_danh_muc = 'Samsung'
GROUP BY sp.id_san_pham
LIMIT 5";

$stmtSamsung = $pdo->prepare($sqlSamsung);
$stmtSamsung->execute();
$samsungList = $stmtSamsung->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets\css\stylesTC.css">
</head>

<body>



  <!-- Main header split into two rows: logo row and controls row -->
  <header class="main-header">
    <div class="container header-inner">
      <!-- Row 1: centered logo -->
      <div class="header-top">
        <div class="logo">ĐIỆN THOẠI TRỰC TUYẾN</div>
      </div>

      <!-- Row 2: navigation (center) + search and action icons (right) -->
      <div class="header-bottom">
        <div class="header-row">
          <!-- Bordered area containing categories, search and menu -->
          <div class="header-bottom-border">
            <div class="categories-short">
              <!--SỬA-->
              <div class="danh-container">
                <button type="button" class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
                <ul class="danh-menu">
                  <?php foreach ($dsDanhMuc as $dm): ?>
                    <li><a href="TimKiem.php?dm=<?= $dm['id_danh_muc'] ?>">
                        <?= $dm['ten_danh_muc'] ?>
                      </a></li>
                  <?php endforeach; ?>
                </ul>
              </div>
              <!--END SỬA-->
            </div>

            <div class="search-wrap">
              <input class="search" placeholder="Tìm kiếm" aria-label="Tìm kiếm" />
              <button class="search-btn" aria-label="Tìm kiếm">🔍</button>
            </div>

            <nav class="main-nav" aria-label="Main navigation">
              <!--SỬA-->
              <a href="SanPham.php">📱SẢN PHẨM</a>
              <a href="GioHang.html">🛒GIỎ HÀNG</a>
              <a id="accountLink" href="">
                <?php
                if (isset($_SESSION['ho_ten'])) {
                  echo "👤 Xin chào, " . $_SESSION['ho_ten'];
                } else {
                  echo "👤 TÀI KHOẢN";
                }
                ?>
              </a>
              <!--END SỬA-->
            </nav>
            <!-- Contact block moved inside the bordered area (right side) -->
            <!--SỬA-->
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- Main banner (carousel) -->
  <section class="main-banner">
    <div class="container">
      <div class="carousel" id="mainCarousel" tabindex="0" aria-roledescription="carousel">
        <div class="slides">
          <div class="slide">
            <div class="banner-inner">
              <div class="banner-text">
                <h1>Siêu ưu đãi cho Galaxy S9</h1>
                <p class="price">Giá <strong>15.990.000₫</strong></p>
                <p class="promo">Khuyến mãi giảm giá đến <strong>4.000.000₫</strong></p>
                <p class="cta"><a class="btn" href="#">SỞ HỮU NGAY</a></p>
              </div>
              <div class="banner-image" aria-hidden="true">
                <svg width="240" height="420" viewBox="0 0 240 420" xmlns="http://www.w3.org/2000/svg">
                  <rect rx="28" width="240" height="420" fill="#0f172a" />
                  <rect x="14" y="30" width="212" height="360" rx="18" fill="#eef2ff" />
                  <circle cx="120" cy="80" r="22" fill="#7c3aed" />
                </svg>
              </div>
            </div>
          </div>
          <div class="carousel-dots" aria-hidden="false"></div>
        </div>
      </div>
  </section>

  <!-- Services -->
  <section class="services container">
    <div class="service">🚚<div>Giao hàng tận nơi</div>
    </div>
    <div class="service">🔁<div>Hỗ trợ đổi trả 30 ngày</div>
    </div>
    <div class="service">🔒<div>100% thanh toán an toàn</div>
    </div>
    <div class="service">✔️<div>Cam kết sản phẩm chính hãng</div>
    </div>
  </section>

  <!-- Categories -->
  <section class="categories container">
    <h3 class="categories-title">Danh mục sản phẩm</h3>
    <div class="categories-list">

      <?php foreach ($dsDanhMuc as $cat): ?>
        <div class="cat">
          <a href="TimKiem.php?danhmuc=<?php echo $cat['id_danh_muc']; ?>">
            📱 <?php echo htmlspecialchars($cat['ten_danh_muc']); ?>
          </a>
        </div>
      <?php endforeach; ?>

    </div>
  </section>

  <!-- Sub banner -->
  <section class="sub-banner">
    <div class="container">
      <div class="sub-carousel" id="subCarousel">
        <div class="sub-slides">
          <div class="sub-slide">
            <div class="sub-inner">
              <div class="sub-text">Nhiều mẫu điện thoại - Giá tốt, lựa chọn đa dạng</div>
              <div class="sub-graphic">📱📱📱📱</div>
            </div>
          </div>

        </div>
        <!--SỬA-->
        <div class="sub-dots"></div>
      </div>
    </div>
  </section>

  <!-- Featured products: iPhone -->
  <section class="featured container">
    <div class="section-header">
      <h2>iPhone chính hãng</h2>
      <a class="view-more" href="#">Xem Thêm →</a>
    </div>
    <div class="products">
      <?php foreach ($iphoneList as $sp): ?>
        <div class="product">
          <a href="ChiTietSanPham.php?id=<?= $sp['id_san_pham'] ?>">
            <img src="<?= $sp['duong_dan_anh'] ?>" width="120">
          </a>
          <div class="name"><?= $sp['ten_san_pham'] ?></div>
          <div class="current-price">
            <?= number_format($sp['gia'], 0, ',', '.') ?>₫
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- Featured products: Samsung -->
  <section class="featured container">
    <div class="section-header">
      <h2>Samsung chính hãng</h2>
      <a class="view-more" href="#">Xem Thêm →</a>
    </div>
    <div class="products">
      <?php foreach ($samsungList as $sp): ?>
        <div class="product">
          <a href="ChiTietSanPham.php?id=<?= $sp['id_san_pham'] ?>">
            <img src="<?= $sp['duong_dan_anh'] ?>" width="120">
          </a>
          <div class="name"><?= $sp['ten_san_pham'] ?></div>
          <div class="current-price">
            <?= number_format($sp['gia'], 0, ',', '.') ?>₫
          </div>
        </div>
      <?php endforeach; ?>
    </div>

  </section>

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

  <!--SỬA THÊM CHAT-->
  <!-- Chat widget -->
  <div class="chat-widget" aria-live="polite">
    <div id="chatPanel" class="chat-panel" aria-hidden="true">
      <div class="chat-header">
        <div class="title">Trợ lý bán hàng</div>
        <button id="chatClose" aria-label="Đóng chat"
          style="background:transparent;border:0;color:rgba(255,255,255,0.9);font-size:20px;cursor:pointer">✕</button>
      </div>
      <div id="chatMessages" class="chat-messages">
        <div class="message bot">Chào bạn! Mình có thể giúp gì hôm nay?</div>
      </div>
      <div class="chat-input">
        <input id="chatInput" type="text" placeholder="Nhập câu hỏi của bạn..." aria-label="Nhập tin nhắn">
        <button id="chatSend">Gửi</button>
      </div>
    </div>
    <button id="chatToggle" class="chat-toggle" aria-label="Mở chat">💬</button>
  </div>

  <script>
    (function() {
      const panel = document.getElementById('chatPanel');
      const toggle = document.getElementById('chatToggle');
      const closeBtn = document.getElementById('chatClose');
      const input = document.getElementById('chatInput');
      const messages = document.getElementById('chatMessages');

      function openChat() {
        panel.classList.add('open');
        panel.setAttribute('aria-hidden', 'false');
        input.focus();
      }

      function closeChat() {
        panel.classList.remove('open');
        panel.setAttribute('aria-hidden', 'true');
      }

      toggle.addEventListener('click', () => {
        if (panel.classList.contains('open')) closeChat();
        else openChat();
      });
      closeBtn.addEventListener('click', closeChat);

      function appendMessage(text, who) {
        const div = document.createElement('div');
        div.className = 'message ' + (who === 'user' ? 'user' : 'bot');
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
      }

      document.getElementById('chatSend').addEventListener('click', () => {
        const v = input.value.trim();
        if (!v) return;
        appendMessage(v, 'user');
        input.value = '';
        // demo bot reply
        setTimeout(() => {
          appendMessage('Cảm ơn! Chúng tôi đã nhận yêu cầu: "' + v + '". Nhân viên sẽ liên hệ sớm.', 'bot');
        }, 700);
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          document.getElementById('chatSend').click();
        }
      });

      // close on outside click
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.chat-widget') && panel.classList.contains('open')) closeChat();
      });
    })();
  </script>
  <!--END SỬA THÊM CHAT-->

  <script>
    // danh mục dropdown (shared behavior)
    (function() {
      document.querySelectorAll('.danh-container').forEach(dc => {
        const btn = dc.querySelector('.danh-muc');

        const menu = dc.querySelector('.danh-menu');
        if (!btn || !menu) return;
        btn.addEventListener('click', (e) => {
          e.stopPropagation();
          dc.classList.toggle('open');
          btn.setAttribute('aria-expanded', dc.classList.contains('open'))
        });
        menu.addEventListener('click', (e) => e.stopPropagation());
      });
      document.addEventListener('click', () => document.querySelectorAll('.danh-container').forEach(dc => {
        dc.classList.remove('open');
        dc.querySelector('.danh-muc')?.setAttribute('aria-expanded', 'false');
      }));
    })();
  </script>


  <!--SỬA-->
  <script>
  </script>

</body>

</html>