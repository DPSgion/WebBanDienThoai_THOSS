window.initQuanLyDanhMuc = function () {

    // DOM elements
    const modal = document.getElementById('modalDanhMuc');
    const openBtn = document.getElementById('openModalDanhMucBtn');
    const closeBtn = document.getElementById('closeModalDanhMucBtn');
    const form = document.getElementById('formDanhMuc');
    const inputName = document.getElementById('tenDanhMucInput');
    const tbody = document.getElementById('tableDanhMuc');

    // Kiểm tra phần tử HTML
    if (!modal || !openBtn || !closeBtn || !form || !tbody) {
        console.error("Thiếu phần tử HTML. Kiểm tra lại id trong HTML.");
        return;
    }

    // -------- FUNCTIONS --------
    function openModal() {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        setTimeout(() => inputName.focus(), 50);
    }

    function closeModal() {
        modal.classList.remove('show');
        document.body.style.overflow = '';
        form.reset();
    }

    // Load danh sách danh mục
    function loadDanhMuc() {
        fetch("../../admin/actions/lay_danhmuc.php")
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
                    <button class="btn-edit" data-id="${item.id}">Sửa</button>
                    <button class="btn-delete" data-id="${item.id}">Xóa</button>
                </td>
            </tr>
        `).join('');
    }

    // -------- EVENTS --------
    openBtn.addEventListener('click', openModal);
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
        fetch("../../admin/actions/luu_danhmuc.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ name })
        })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    // alert("Thêm danh mục thành công!");
                    closeModal();
                    // location.reload();
                } else {
                    alert("Lỗi: " + res.message);
                }
            })
            .catch(err => console.log("Lỗi server: " + err));
    });

    // -------- KHỞI TẠO --------
    loadDanhMuc(); // Load dữ liệu khi trang vừa mở

};