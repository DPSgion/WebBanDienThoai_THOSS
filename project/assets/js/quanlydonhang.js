function initQuanLyDonHang() {
    console.log("Đã load initQuanLyDonHang"); // Dòng này để check xem JS có chạy không

    // URL API (Tính từ vị trí file admin/index.php)
    const API_URL = {
        GET: 'actions/donhang/lay_ds_donhang.php',
        DETAIL: 'actions/donhang/lay_chitiet_donhang.php',
        UPDATE: 'actions/donhang/capnhat_trangthai.php'
    };

    // --- CÁC HÀM XỬ LÝ ---

    // 1. Hàm load bảng
    window.loadTableDonHang = function() {
        const keyword = document.getElementById('searchDonHang').value;
        const status = document.getElementById('filterStatus').value;
        const tbody = document.getElementById('tableDonHang');

        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center">Đang tải dữ liệu...</td></tr>';

        // Gọi API
        fetch(`${API_URL.GET}?search=${keyword}&status=${status}`)
            .then(res => res.text()) // Dùng .text() trước để debug lỗi PHP nếu có
            .then(text => {
                try {
                    const res = JSON.parse(text); // Thử convert sang JSON
                    if (res.success) {
                        let html = '';
                        if (res.data.length === 0) {
                            html = '<tr><td colspan="6" style="text-align:center">Không tìm thấy đơn hàng</td></tr>';
                        } else {
                            res.data.forEach(dh => {
                                // Map trạng thái
                                let statusText = dh.trang_thai;
                                let statusClass = '';
                                if(dh.trang_thai == 'choxuly') { statusText = 'Chờ xử lý'; statusClass='status-wait'; }
                                else if(dh.trang_thai == 'danggiao') { statusText = 'Đang giao'; statusClass='status-shipping'; }
                                else if(dh.trang_thai == 'hoanthanh') { statusText = 'Hoàn thành'; statusClass='status-completed'; }
                                else if(dh.trang_thai == 'dahuy') { statusText = 'Đã hủy'; statusClass='status-cancelled'; }

                                html += `
                                    <tr>
                                        <td>#${dh.id_don_hang}</td>
                                        <td>${dh.ho_ten}<br><small>${dh.sdt}</small></td>
                                        <td>${new Date(dh.ngay_dat).toLocaleDateString('vi-VN')}</td>
                                        <td style="color:red; font-weight:bold">${new Intl.NumberFormat('vi-VN').format(dh.tong_tien)} đ</td>
                                        <td><span class="${statusClass}">${statusText}</span></td>
                                        <td>
                                            <button class="btn-sua" onclick="openModalDonHang(${dh.id_don_hang})">Xem</button>
                                        </td>
                                    </tr>
                                `;
                            });
                        }
                        tbody.innerHTML = html;
                    } else {
                        alert("Lỗi server: " + res.message);
                    }
                } catch (e) {
                    console.error("Lỗi JSON:", text); // Quan trọng: Xem lỗi PHP ở đây
                    tbody.innerHTML = `<tr><td colspan="6" style="color:red">Lỗi code PHP (Xem console): ${text.substring(0, 100)}...</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Lỗi mạng:", err);
                tbody.innerHTML = '<tr><td colspan="6">Lỗi kết nối server</td></tr>';
            });
    }

    // 2. Hàm mở Modal
    window.openModalDonHang = function(id) {
        const modal = document.getElementById('modalDonHang');
        const itemsBody = document.getElementById('tableChiTietDonHang');
        
        modal.style.display = 'flex';
        itemsBody.innerHTML = '<tr><td colspan="5">Đang tải...</td></tr>';

        fetch(`${API_URL.DETAIL}?id=${id}`)
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    const info = res.data.info;
                    const items = res.data.items;

                    document.getElementById('detailMaDon').innerText = info.id_don_hang;
                    document.getElementById('detailTenKH').innerText = info.ho_ten;
                    document.getElementById('detailSDT').innerText = info.sdt;
                    document.getElementById('detailDiaChi').innerText = info.dia_chi || '';
                    document.getElementById('detailNgayDat').innerText = info.ngay_dat;
                    document.getElementById('detailTongTien').innerText = new Intl.NumberFormat('vi-VN').format(info.tong_tien) + ' đ';

                    // Render list sản phẩm
                    let itemsHtml = '';
                    items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${item.ten_san_pham}</td>
                                <td>${item.mau} - ${item.ram}/${item.rom}</td>
                                <td>${item.so_luong}</td>
                                <td>${new Intl.NumberFormat('vi-VN').format(item.gia_luc_mua)}</td>
                                <td>${new Intl.NumberFormat('vi-VN').format(item.so_luong * item.gia_luc_mua)}</td>
                            </tr>
                        `;
                    });
                    itemsBody.innerHTML = itemsHtml;

                    // Nút hành động
                    renderActionButtons(info.id_don_hang, info.trang_thai);
                }
            });
    }

    // 3. Render nút bấm
    function renderActionButtons(id, status) {
        const container = document.getElementById('actionButtons');
        let html = '';
        if (status === 'choxuly') {
            html += `<button class="btnLuu" onclick="updateStatus(${id}, 'danggiao')">Duyệt đơn</button>`;
            html += `<button class="btnDong" style="background:red; color:white; margin-left:5px" onclick="updateStatus(${id}, 'dahuy')">Hủy</button>`;
        } else if (status === 'danggiao') {
            html += `<button class="btnLuu" onclick="updateStatus(${id}, 'hoanthanh')">Hoàn thành</button>`;
            html += `<button class="btnDong" style="background:red; color:white; margin-left:5px" onclick="updateStatus(${id}, 'dahuy')">Hủy giao</button>`;
        }
        container.innerHTML = html;
    }

    // 4. Update status
    window.updateStatus = function(id, status) {
        if(!confirm('Xác nhận thay đổi trạng thái?')) return;
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch(API_URL.UPDATE, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(res => {
                if(res.success) {
                    alert('Thành công!');
                    document.getElementById('modalDonHang').style.display = 'none';
                    loadTableDonHang();
                } else {
                    alert(res.message);
                }
            });
    }

    // Sự kiện đóng modal
    const closeBtn = document.getElementById('closeModalDonHangBtn');
    if(closeBtn) {
        closeBtn.onclick = function() {
            document.getElementById('modalDonHang').style.display = 'none';
        }
    }

    // --- CHẠY LẦN ĐẦU ---
    loadTableDonHang();
}