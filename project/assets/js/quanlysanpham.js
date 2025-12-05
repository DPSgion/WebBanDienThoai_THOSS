window.initQuanLySanPham = function () {
    // DOM elements
    const modal = document.getElementById('modal-them');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const addOptionBtn = document.getElementById('addOptionBtn');
    const optionsBody = document.getElementById('optionsBody');
    const form = document.getElementById('form-them');
    const imagePreviewContainer = document.getElementById('image-preview-container');
    const imagesToDeleteInput = document.getElementById('images_to_delete');
    const productIdInput = document.getElementById('product_id');
    const hinhanhInput = document.getElementById('hinhanh');

    let allProducts = [];

    if (!modal || !openBtn || !closeBtn || !form) {
        console.error("Thiếu phần tử HTML. Kiểm tra lại id trong HTML.");
        return;
    }

    // ----- Modal mở/đóng -----
    function openModal(isEdit = false) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        optionsBody.innerHTML = '';
        form.reset();
        imagePreviewContainer.innerHTML = '';
        imagesToDeleteInput.value = '';

        if (!isEdit) {
            productIdInput.value = '';
        }

        setTimeout(() => document.getElementById('name').focus(), 50);
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        form.reset();
        optionsBody.innerHTML = '';
        delete form.dataset.editing; // xóa để lần sau thêm mới
    }


    openBtn.addEventListener('click', () => openModal(false));
    closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal.classList.contains('show')) closeModal(); });

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

    // ----- Option row -----
    function createOptionRow({ ram = '', rom = '', color = '', quantity = '', price = '', id_bien_the = '' } = {}) {
        const tr = document.createElement('tr');
        tr.dataset.id = id_bien_the;
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

    addOptionBtn.addEventListener('click', () => {
        const row = createOptionRow();
        optionsBody.appendChild(row);
        row.querySelector('input').focus();
    });

    optionsBody.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.ctrlKey) addOptionBtn.click();
    });

    // ----- Load sản phẩm -----
    function loadProducts() {
        fetch('../../admin/actions/sanpham/lay_sanpham.php')
            .then(res => res.json())
            .then(res => {
                if (!res.success) return console.error(res.message);

                // Lưu dữ liệu gốc
                allProducts = res.data;

                // Hiển thị sản phẩm
                displayProducts(allProducts);
            })
            .catch(err => console.error("Load sản phẩm lỗi:", err));
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
                            loadProducts();
                        } else {
                            alert(res.message);
                        }
                    });
            });
        });
    }

    // Hàm hiển thị sản phẩm
    // Hàm hiển thị sản phẩm (giữ nguyên định dạng cũ)
    function displayProducts(products) {
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

        if (products.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td colspan="8" style="text-align:center;">Không tìm thấy sản phẩm</td>`;
            tbody.appendChild(tr);
            return;
        }

        products.forEach((sp, index) => {
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
                <button class="btn-edit" data-id="${sp.id_san_pham}">Sửa</button>
                <button class="btn-delete" data-id="${sp.id_bien_the}">Xóa</button>
            </td>
        `;
            tbody.appendChild(tr);
        });

        attachDeleteEvents();
        attachEditEvents();
    }
    // Hàm lọc và sắp xếp
    function filterAndSortProducts() {
        // Lấy giá trị từ các input
        const searchTerm = document.querySelector('input[name="tensp"]').value.toLowerCase().trim();
        const filterOS = document.getElementById('filter-os').value.toLowerCase();
        const filterPrice = document.getElementById('filter-gia').value;
        const filterQuantity = document.getElementById('filter-soluong').value;
        const sortBy = document.getElementById('sort-by').value;

        // Bước 1: Lọc sản phẩm
        let filtered = allProducts.filter(sp => {
            // Lọc theo tên
            const matchName = !searchTerm || sp.ten_san_pham.toLowerCase().includes(searchTerm);

            // Lọc theo OS
            const matchOS = !filterOS || (sp.os && sp.os.toLowerCase() === filterOS);

            // Lọc theo giá (≤)
            const matchPrice = !filterPrice || Number(sp.gia) <= Number(filterPrice);

            // Lọc theo số lượng (≥)
            const matchQuantity = !filterQuantity || Number(sp.so_luong_ton) >= Number(filterQuantity);

            return matchName && matchOS && matchPrice && matchQuantity;
        });

        // Bước 2: Sắp xếp
        if (sortBy) {
            filtered.sort((a, b) => {
                switch (sortBy) {
                    case 'gia-asc':
                        return Number(a.gia) - Number(b.gia);
                    case 'gia-desc':
                        return Number(b.gia) - Number(a.gia);
                    case 'soluong-asc':
                        return Number(a.so_luong_ton) - Number(b.so_luong_ton);
                    case 'soluong-desc':
                        return Number(b.so_luong_ton) - Number(a.so_luong_ton);
                    default:
                        return 0;
                }
            });
        }

        // Hiển thị kết quả
        displayProducts(filtered);
    }

    function attachEditEvents() {
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                openEditModal(id);
            });
        });
    }


    // ----- Edit modal -----
    function openEditModal(id) {
        // Mở modal với chế độ edit
        openModal(true);

        // Gán ID sản phẩm đang sửa
        form.dataset.editing = id;

        // Load dữ liệu sản phẩm
        fetch(`../../admin/actions/sanpham/lay_chitiet_sanpham.php?id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message);
                    return;
                }

                const sp = data.product;
                form.danhmuc.value = sp.id_danh_muc;
                form.name.value = sp.ten_san_pham;
                form.os.value = sp.os;
                form.cpu.value = sp.cpu;
                form.screen.value = sp.man_hinh;
                form.front_cam.value = sp.camera_truoc;
                form.rear_cam.value = sp.camera_sau;
                form.pin.value = sp.pin;

                // load biến thể + hình
                data.options.forEach(opt => {
                    const row = createOptionRow({
                        ram: opt.ram,
                        rom: opt.rom,
                        color: opt.mau,
                        quantity: opt.so_luong_ton,
                        price: opt.gia
                    });
                    row.dataset.id = opt.id_bien_the;
                    optionsBody.appendChild(row);
                });

                loadImagePreview(data.images);
            });
    }


    function loadImagePreview(images) {
        imagePreviewContainer.innerHTML = '';
        images.forEach(img => {
            const div = document.createElement('div');
            div.classList.add('preview-item');
            div.innerHTML = `
                <img src="../../uploads/${img.duong_dan_anh}" class="preview-img" width="50px" />
                <button type="button" class="del-img-btn" data-name="${img.duong_dan_anh}">X</button>
            `;
            imagePreviewContainer.appendChild(div);
        });

        imagePreviewContainer.querySelectorAll('.del-img-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.parentElement.remove();
                imagesToDeleteInput.value += btn.dataset.name + ';';
            });
        });
    }

    // ----- Submit form -----
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // ít nhất 1 option
        const optionRows = [...optionsBody.querySelectorAll('tr')];
        if (optionRows.length === 0) return alert('Bạn cần ít nhất một option.');

        // ít nhất 1 hình (edit thì ảnh cũ phải còn)
        const hasImage = hinhanhInput.files.length > 0 || imagePreviewContainer.querySelectorAll('img').length > 0;
        if (!hasImage) return alert('Bạn cần ít nhất một hình.');

        const fd = new FormData();
        const isAdding = !form.dataset.editing; // nếu không có dataset.editing → thêm mới

        if (!isAdding) {
            // khi sửa
            fd.append('product_id', form.dataset.editing);

            // Gửi danh sách ảnh GIỮ LẠI (existing_images)
            const existingImages = [];
            imagePreviewContainer.querySelectorAll('.preview-img').forEach(img => {
                const src = img.src;
                const filename = src.substring(src.lastIndexOf('/') + 1);
                existingImages.push(filename);
            });

            // Append từng ảnh giữ lại
            existingImages.forEach((filename, index) => {
                fd.append(`existing_images[${index}]`, filename);
            });
        }

        fd.append('id_category', form.danhmuc.value);
        fd.append('name', form.name.value.trim());
        fd.append('os', form.os.value);
        fd.append('cpu', form.cpu.value);
        fd.append('screen', form.screen.value);
        fd.append('front_cam', form.front_cam.value);
        fd.append('rear_cam', form.rear_cam.value);
        fd.append('pin', form.pin.value);

        // append ảnh mới
        for (let i = 0; i < hinhanhInput.files.length; i++) {
            fd.append('images[]', hinhanhInput.files[i]);
        }

        // append options
        optionRows.forEach((tr, index) => {
            const inp = tr.querySelectorAll('input');
            fd.append(`options[${index}][id_bien_the]`, tr.dataset.id ?? '');
            fd.append(`options[${index}][ram]`, inp[0].value.trim());
            fd.append(`options[${index}][rom]`, inp[1].value.trim());
            fd.append(`options[${index}][color]`, inp[2].value.trim());
            fd.append(`options[${index}][quantity]`, inp[3].value);
            fd.append(`options[${index}][price]`, inp[4].value);
        });

        // chọn url dựa trên thêm mới hay sửa
        const url = isAdding
            ? '../../admin/actions/sanpham/save_product.php'
            : '../../admin/actions/sanpham/capnhat_sanpham.php';

        fetch(url, {
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
            .catch(err => console.error('Lỗi server: ' + err));
    });


    // Gắn sự kiện cho các input lọc và sắp xếp
    document.querySelector('input[name="tensp"]').addEventListener('input', filterAndSortProducts);
    document.getElementById('filter-os').addEventListener('change', filterAndSortProducts);
    document.getElementById('filter-gia').addEventListener('input', filterAndSortProducts);
    document.getElementById('filter-soluong').addEventListener('input', filterAndSortProducts);
    document.getElementById('sort-by').addEventListener('change', filterAndSortProducts);

    // Gọi loadProducts() để load dữ liệu ban đầu
    loadProducts();
};
