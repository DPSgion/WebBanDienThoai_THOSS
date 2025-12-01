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

        // thêm lại dòng mẫu nếu bạn muốn (tuỳ bạn)
        // const example = createOptionRow({ ram: '8GB', rom: '128GB', price: 6500000 });
        // optionsBody.appendChild(example);
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

    // ----- OPTION ROW -----
    function createOptionRow({ ram = '', rom = '', price = '' } = {}) {
        const tr = document.createElement('tr');

        tr.innerHTML = `
            <td><input type="text" class="small-input" placeholder="8GB" required value="${ram}"></td>
            <td><input type="text" class="small-input" placeholder="128GB" required value="${rom}"></td>
            <td><input type="number" class="small-input price-input" placeholder="6500000" min="0" step="1000" required value="${price}"></td>
            <td style="text-align:right;">
                <button type="button" class="btn-delete">Xóa</button>
            </td>
        `;

        // gán sự kiện nút xóa
        tr.querySelector('.btn-delete').addEventListener('click', () => tr.remove());
        return tr;
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

        const data = {
            name: form.name.value.trim(),
            os: form.os.value,
            cpu: form.cpu.value,
            screen: form.screen.value,
            front_cam: form.front_cam.value,
            rear_cam: form.rear_cam.value,
            pin: form.pin.value,
            quantity: Number(form.quantity.value) || 0,
            options: []
        };

        let error = false;

        optionRows.forEach(tr => {
            const inp = tr.querySelectorAll('input');
            const ram = inp[0].value.trim();
            const rom = inp[1].value.trim();
            const price = Number(inp[2].value);

            if (!ram || !rom || Number.isNaN(price)) error = true;
            else data.options.push({ ram, rom, price });
        });

        if (error) {
            alert("Lỗi dữ liệu option.");
            return;
        }

        console.log("PRODUCT DATA:", data);
        alert("Đã thu thập dữ liệu! Xem console.");
    });

};
