window.initQuanLyDanhMuc = function () {

    // DOM elements
    const modal = document.getElementById('modalDanhMuc');
    const modalTitle = document.getElementById('modalTitle');
    const openBtn = document.getElementById('openModalDanhMucBtn');
    const closeBtn = document.getElementById('closeModalDanhMucBtn');
    const form = document.getElementById('formDanhMuc');
    const inputId = document.getElementById('idDanhMuc');
    const inputName = document.getElementById('tenDanhMucInput');
    const tbody = document.getElementById('tableDanhMuc');

    let isEditMode = false; // Biến để phân biệt thêm hay sửa

    // Kiểm tra phần tử HTML
    if (!modal || !openBtn || !closeBtn || !form || !tbody) {
        console.error("Thiếu phần tử HTML. Kiểm tra lại id trong HTML.");
        return;
    }

    // -------- FUNCTIONS --------
    function openModal(mode = 'add', data = null) {
        isEditMode = mode === 'edit';
        
        if (isEditMode && data) {
            modalTitle.textContent = 'Sửa danh mục';
            inputId.value = data.id;
            inputName.value = data.name;
        } else {
            modalTitle.textContent = 'Thêm danh mục';
            inputId.value = '';
            inputName.value = '';
        }

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(() => inputName.focus(), 50);
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        form.reset();
        isEditMode = false;
    }

    // Load danh sách danh mục
    function loadDanhMuc() {
        fetch("../../admin/actions/danhmuc/lay_danhmuc.php")
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    renderTable(res.data);
                } else {
                    console.error("Lỗi:", res.message);
                    tbody.innerHTML = '<tr><td colspan="3">Lỗi tải dữ liệu</td></tr>';
                }
            })
            .catch(err => {
                console.error("Lỗi fetch:", err);
                tbody.innerHTML = '<tr><td colspan="3">Không thể tải dữ liệu</td></tr>';
            });
    }

    // Render bảng
    function renderTable(data) {
        if (!data || data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="3">Chưa có danh mục nào</td></tr>';
            return;
        }

        tbody.innerHTML = data.map((item, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${item.ten_danh_muc}</td>
                <td>
                    <button class="btn-edit" data-id="${item.id_danh_muc}" data-name="${item.ten_danh_muc}">Sửa</button>
                    <button class="btn-delete" data-id="${item.id_danh_muc}">Xóa</button>
                </td>
            </tr>
        `).join('');

        // Gắn sự kiện
        attachEditEvents();
        attachDeleteEvents();
    }

    // Gắn sự kiện sửa
    function attachEditEvents() {
        const editBtns = document.querySelectorAll('.btn-edit');
        editBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                openModal('edit', { id, name });
            });
        });
    }

    // Gắn sự kiện xóa
    function attachDeleteEvents() {
        const deleteBtns = document.querySelectorAll('.btn-delete');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const row = this.closest('tr');
                const categoryName = row.querySelector('td:nth-child(2)').textContent;
                
                if (confirm(`Bạn có chắc muốn xóa danh mục "${categoryName}"?`)) {
                    deleteCategory(id);
                }
            });
        });
    }

    // Xóa danh mục
    function deleteCategory(id) {
        fetch("../../admin/actions/danhmuc/xoa_danhmuc.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id })
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    alert("Xóa danh mục thành công!");
                    loadDanhMuc();
                } else {
                    alert("Lỗi: " + res.message);
                }
            })
            .catch(err => {
                console.error("Lỗi server:", err);
                alert("Lỗi khi xóa danh mục");
            });
    }

    // -------- EVENTS --------
    openBtn.addEventListener('click', () => openModal('add'));
    closeBtn.addEventListener('click', closeModal);

    // Đóng khi bấm ra ngoài
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

    // Đóng khi bấm ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('show')) closeModal();
    });

    // -------- SUBMIT FORM --------
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const name = inputName.value.trim();
        if (!name) {
            alert("Tên danh mục không được để trống.");
            return;
        }

        const url = isEditMode 
            ? "../../admin/actions/danhmuc/sua_danhmuc.php" 
            : "../../admin/actions/danhmuc/luu_danhmuc.php";

        const data = isEditMode 
            ? { id: inputId.value, name } 
            : { name };

        fetch(url, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(data)
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // alert(isEditMode ? "Cập nhật danh mục thành công!" : "Thêm danh mục thành công!");
                    closeModal();
                    loadDanhMuc();
                } else {
                    alert("Lỗi: " + res.message);
                }
            })
            .catch(err => {
                console.error("Lỗi server:", err);
                alert("Lỗi server");
            });
    });

    // -------- KHỞI TẠO --------
    loadDanhMuc();

};