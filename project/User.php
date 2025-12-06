<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Trang người dùng — ĐIỆN THOẠI TRỰC TUYẾN</title>
  <link rel="stylesheet" href="assets/css/stylesTC.css">
  <link rel="stylesheet" href="assets/css/stylesUser.css">
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
        <!--SỬA-->
        <a href="TrangChu.html" class="icon-btn cart" aria-label="Trang chủ">🏠 </a>
        <a href="GioHang.html" class="icon-btn cart" aria-label="Giỏ hàng">🛒 </span></a>
        <div class="danh-container">
          <button class="danh-muc" aria-haspopup="true" aria-expanded="false">☰ Danh mục</button>
          <ul class="danh-menu" role="menu">
            <li><a href="TimKiem.html" class="danh-link">iPhone</a></li>
            <li><a href="#">Samsung</a></li>
            <!--SỬA-->
            <li><a href="#">Máy tính bảng</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <main class="container user-page">
    <div class="user-grid">
      <section class="profile-card">
        <h2>Thông tin cá nhân</h2>
        <form id="profileForm">
          <label class="field">
            <div class="label">Họ và tên</div>
            <input id="fullname" name="fullname" type="text" placeholder="Nguyễn Văn A" required>
          </label>

          <label class="field">
            <div class="label">Số điện thoại</div>
            <input id="phone" name="phone" type="tel" placeholder="0123456789">
          </label>

          <label class="field">
            <div class="label">Địa chỉ hiện tại</div>
            <textarea id="currentAddress" name="currentAddress" rows="2" placeholder="Địa chỉ nhận hàng"></textarea>
          </label>

          <label class="field">
            <div class="label">Mật khẩu hiện tại</div>
            <input id="currentPassword" name="currentPassword" type="password" placeholder="Mật khẩu hiện tại">
          </label>

          <label class="field">
            <div class="label">Mật khẩu mới</div>
            <input id="newPassword" name="newPassword" type="password" placeholder="Mật khẩu mới (ít nhất 6 ký tự)">
          </label>

          <label class="field">
            <div class="label">Xác nhận mật khẩu mới</div>
            <input id="confirmPassword" name="confirmPassword" type="password" placeholder="Nhập lại mật khẩu mới">
          </label>

          <div class="actions">
            <button type="submit" class="btn primary">Lưu thông tin</button>
            <a class="btn outline" href="TrangChu.html">Quay lại</a>
          </div>
        </form>

        <hr>

        <!--CHƯA XÁC NHẬN -->
        <h3>Địa chỉ đã lưu</h3>
        <div id="addresses" class="addresses"></div>

        <div class="add-address">
          <textarea id="newAddress" rows="2" placeholder="Thêm địa chỉ mới"></textarea>
          <div style="margin-top:8px"><button id="addAddrBtn" class="btn">Thêm địa chỉ</button></div>
        </div>
      </section>

      <section class="orders-card">
        <h2>Lịch sử mua hàng</h2>
        <div id="orders" class="orders"></div>
      </section>
    </div>
  </main>

  <script>
    // shared dropdown behavior
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

    // Profile and addresses
    function getProfile(){
      const raw = localStorage.getItem('demo_user_profile') || localStorage.getItem('demo_registered_user');
      if(!raw) return {};
      try{ return JSON.parse(raw); }catch(e){ return {}; }
    }

    function saveProfile(p){ localStorage.setItem('demo_user_profile', JSON.stringify(p)); }

    function getAddresses(){
      const raw = localStorage.getItem('demo_user_addresses');
      if(!raw) return [];
      try{ return JSON.parse(raw); }catch(e){ return []; }
    }

    function saveAddresses(list){ localStorage.setItem('demo_user_addresses', JSON.stringify(list)); }

    function renderAddresses(){
      const container = document.getElementById('addresses'); container.innerHTML='';
      const list = getAddresses();
      if(list.length===0){ container.innerHTML='<div class="muted">Chưa có địa chỉ đã lưu.</div>'; return; }
      list.forEach((a, idx)=>{
        const div = document.createElement('div'); div.className='address-item';
        div.innerHTML = `<div class="addr-text">${a}</div><div class="addr-actions"><button data-idx="${idx}" class="btn small edit">Sửa</button> <button data-idx="${idx}" class="btn small danger del">Xóa</button></div>`;
        container.appendChild(div);
      });
      // bind buttons
      container.querySelectorAll('.edit').forEach(b=> b.addEventListener('click', (e)=>{
        const idx = +e.target.dataset.idx; const list = getAddresses();
        const nv = prompt('Chỉnh sửa địa chỉ', list[idx]); if(nv===null) return; list[idx]=nv.trim(); saveAddresses(list); renderAddresses();
      }));
      container.querySelectorAll('.del').forEach(b=> b.addEventListener('click', (e)=>{
        const idx = +e.target.dataset.idx; if(!confirm('Xóa địa chỉ này?')) return; const list = getAddresses(); list.splice(idx,1); saveAddresses(list); renderAddresses();
      }));
    }

    document.getElementById('addAddrBtn').addEventListener('click', ()=>{
      const v = document.getElementById('newAddress').value.trim(); if(!v){ alert('Vui lòng nhập địa chỉ.'); return; }
      const list = getAddresses(); list.unshift(v); saveAddresses(list); document.getElementById('newAddress').value=''; renderAddresses();
    });

    // Orders
    function getOrders(){
      const raw = localStorage.getItem('demo_orders');
      if(!raw) return null;
      try{ return JSON.parse(raw); }catch(e){ return null; }
    }

    function seedOrders(){
      const sample = [
        { id:'DH20251203-001', date:'2025-12-03', total:'5.990.000₫', status:'Đã giao', items:[{name:'iPhone 17 Pro Max', qty:1, price:'31.990.000₫'}] },
        { id:'DH20251120-004', date:'2025-11-20', total:'1.990.000₫', status:'Đang xử lý', items:[{name:'Ốp lưng iPhone', qty:2, price:'99.000₫'}] }
      ];
      localStorage.setItem('demo_orders', JSON.stringify(sample));
      return sample;
    }

    function renderOrders(){
      const container = document.getElementById('orders'); container.innerHTML='';
      let list = getOrders(); if(!list) list = seedOrders();
      if(list.length===0){ container.innerHTML='<div class="muted">Chưa có đơn hàng.</div>'; return; }
      list.forEach((o, idx)=>{
        const div = document.createElement('div'); div.className='order-item';
        let itemsHtml = o.items.map(it=>`<div class="order-line">${it.name} × ${it.qty} — ${it.price}</div>`).join('');
        div.innerHTML = `<div class="order-head"><div><strong>${o.id}</strong> — ${o.date}</div><div class="order-right">${o.status} • <strong>${o.total}</strong></div></div>
          <div class="order-actions"><button class="btn small toggle" data-idx="${idx}">Chi tiết</button></div>
          <div class="order-details" data-idx="${idx}" style="display:none">${itemsHtml}</div>`;
        container.appendChild(div);
      });
      container.querySelectorAll('.toggle').forEach(btn=> btn.addEventListener('click', (e)=>{
        const idx = btn.dataset.idx; const det = container.querySelector('.order-details[data-idx="'+idx+'"]'); det.style.display = det.style.display==='none'? 'block':'none';
      }));
    }

    // profile form save + password change handling
    document.getElementById('profileForm').addEventListener('submit', function(e){
      e.preventDefault();
      const name = document.getElementById('fullname').value.trim();
      const phone = document.getElementById('phone').value.trim();
      const addr = document.getElementById('currentAddress').value.trim();
      const currentPw = document.getElementById('currentPassword').value;
      const newPw = document.getElementById('newPassword').value;
      const confirmPw = document.getElementById('confirmPassword').value;

      if(!name){ alert('Vui lòng nhập họ và tên.'); return; }

      // handle password change if user provided a new password
      if(newPw){
        if(newPw.length < 6){ alert('Mật khẩu mới phải có ít nhất 6 ký tự.'); return; }
        if(newPw !== confirmPw){ alert('Mật khẩu mới và xác nhận không khớp.'); return; }

        // check existing stored password (if any)
        const storedPw = localStorage.getItem('demo_user_password') || (function(){
          const reg = localStorage.getItem('demo_registered_user');
          if(!reg) return null; try{ const r = JSON.parse(reg); return r.password || null; }catch(e){return null}
        })();

        if(storedPw){
          if(!currentPw){ alert('Vui lòng nhập mật khẩu hiện tại để thay đổi mật khẩu.'); return; }
          if(currentPw !== storedPw){ alert('Mật khẩu hiện tại không đúng.'); return; }
        }

        // save new password (demo only)
        localStorage.setItem('demo_user_password', newPw);
        // also update demo_registered_user if present
        const regRaw = localStorage.getItem('demo_registered_user');
        if(regRaw){ try{ const r = JSON.parse(regRaw); r.password = newPw; localStorage.setItem('demo_registered_user', JSON.stringify(r)); }catch(e){} }
      }

      const p = { name, phone, address: addr };
      saveProfile(p);
      // clear password inputs after success
      document.getElementById('currentPassword').value = '';
      document.getElementById('newPassword').value = '';
      document.getElementById('confirmPassword').value = '';
      alert('Thông tin đã được lưu (demo).' + (newPw? ' Mật khẩu đã được cập nhật.':''));
    });

    function init(){
      const p = getProfile();
      if(p.name) document.getElementById('fullname').value = p.name;
      if(p.phone) document.getElementById('phone').value = p.phone;
      if(p.address) document.getElementById('currentAddress').value = p.address;
      // ensure addresses array exists
      if(!localStorage.getItem('demo_user_addresses')){
        const arr = p.address? [p.address] : [];
        localStorage.setItem('demo_user_addresses', JSON.stringify(arr));
      }
      renderAddresses(); renderOrders();
    }

    init();
  </script>

</body>
</html>
