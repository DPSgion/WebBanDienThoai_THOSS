window.initQuanLySanPham = function () {

    // DOM elements
    const modal = document.getElementById('modal-them');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const addOptionBtn = document.getElementById('addOptionBtn');
    const optionsBody = document.getElementById('optionsBody');
    const form = document.getElementById('form-them');

    if (!modal || !openBtn || !closeBtn) {
        console.error("Thiếu phần tử HTML. Kiểm tra lại id trong HTML.");
        return;
    }

    // ---- functions ----
    function openModal() {
        modal.classList.add('show');
        setTimeout(() => document.getElementById('name').focus(), 50);
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';

        // reset toàn bộ form
        form.reset();

        // xoá toàn bộ các option đang có
        optionsBody.innerHTML = '';
    }


    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
    });

    // ----- Lấy danh mục -----
    fetch('../../admin/actions/danhmuc/lay_danhmuc.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) return;

            const select = document.getElementById('danhmuc');
            data.data.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id_danh_muc;
                option.textContent = cat.ten_danh_muc;
                select.appendChild(option);
            });
        })
        .catch(err => console.error('Error:', err));


    // ----- OPTION ROW -----
    function createOptionRow({ ram = '', rom = '', color = '', quantity = '', price = '' } = {}) {
        const tr = document.createElement('tr');

        tr.innerHTML = `
        <td><input type="text" class="small-input" placeholder="8GB" required value="${ram}"></td>
        <td><input type="text" class="small-input" placeholder="128GB" required value="${rom}"></td>
        <td><input type="text" class="small-input" placeholder="Đen / Trắng / Xanh" required value="${color}"></td>
        <td><input type="number" class="small-input" placeholder="SL" min="0" required value="${quantity}"></td>
        <td><input type="number" class="small-input price-input" placeholder="6500000" min="0" step="1000" required value="${price}"></td>
        <td style="text-align:center;">
            <button type="button" class="btn-delete">Xóa</button>
        </td>
    `;

        tr.querySelector('.btn-delete').addEventListener('click', () => tr.remove());
        return tr;
    }

    function loadProducts() {
        fetch('../../admin/actions/sanpham/lay_sanpham.php')
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    console.error(res.message);
                    return;
                }

                const tbody = document.querySelector('.table-sp');
                tbody.innerHTML = `
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>CPU</th>
                    <th>RAM - ROM</th>
                    <th>Pin</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Hành động</th>
                </tr>
            `;

                res.data.forEach((sp, index) => {
                    const tr = document.createElement('tr');

                    tr.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${sp.ten_san_pham}</td>
                    <td>${sp.cpu ?? ''}</td>
                    <td>${sp.ram} - ${sp.rom}</td>
                    <td>${sp.pin ?? ''}</td>
                    <td>${Number(sp.gia).toLocaleString()} VNĐ</td>
                    <td>${sp.so_luong_ton}</td>
                    <td>
                        <button class="btn-edit" data-id="${sp.id_bien_the}">Sửa</button>
                        <button class="btn-delete" data-id="${sp.id_bien_the}">Xóa</button>
                    </td>
                `;

                    tbody.appendChild(tr);

                });

                attachDeleteEvents();
            })
            .catch(err => {
                console.error("Load sản phẩm lỗi:", err);
            });
    }

    function attachDeleteEvents() {
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;

                if (!confirm("Bạn chắc muốn xoá?")) return;

                fetch('../../admin/actions/sanpham/xoa_bienthe_sanpham.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_bien_the: id })
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            alert("Xoá thành công");
                            loadProducts(); // load lại danh sách
                        } else {
                            alert(res.message);
                        }
                    });
            });
        });
    }





    addOptionBtn.addEventListener('click', () => {
        const row = createOptionRow();
        optionsBody.appendChild(row);
        row.querySelector('input').focus();
    });

    // Ctrl+Enter thêm dòng
    optionsBody.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.ctrlKey) addOptionBtn.click();
    });

    // submit
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const optionRows = [...optionsBody.querySelectorAll('tr')];
        if (optionRows.length === 0) {
            alert('Bạn cần ít nhất một option.');
            return;
        }

        const fd = new FormData();
        fd.append('id_category', form.danhmuc.value);
        fd.append('name', form.name.value.trim());
        fd.append('os', form.os.value);
        fd.append('cpu', form.cpu.value);
        fd.append('screen', form.screen.value);
        fd.append('front_cam', form.front_cam.value);
        fd.append('rear_cam', form.rear_cam.value);
        fd.append('pin', form.pin.value);

        // append ảnh
        const files = document.getElementById('hinhanh').files;
        for (let i = 0; i < files.length; i++) {
            fd.append('images[]', files[i]);
        }

        // append options
        optionRows.forEach((tr, index) => {
            const inp = tr.querySelectorAll('input');

            fd.append(`options[${index}][ram]`, inp[0].value.trim() + 'GB');
            fd.append(`options[${index}][rom]`, inp[1].value.trim() + 'GB');
            fd.append(`options[${index}][color]`, inp[2].value.trim());
            fd.append(`options[${index}][quantity]`, inp[3].value);
            fd.append(`options[${index}][price]`, inp[4].value);
        });

        fetch('../../admin/actions/sanpham/save_product.php', {
            method: 'POST',
            body: fd
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert('Lưu sản phẩm thành công!');
                    closeModal();
                    loadProducts();
                } else {
                    alert('Lỗi: ' + res.message);
                }
            })
            .catch(err => console.log('Lỗi server: ' + err));
    });


    loadProducts();


};
