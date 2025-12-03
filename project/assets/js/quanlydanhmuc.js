window.initQuanLyDanhMuc = function () {
    const openBtn = document.getElementById('openModalDanhMucBtn');
    const closeBtn = document.getElementById('closeModalDanhMucBtn');
    const modal = document.getElementById('modalDanhMuc');

    openBtn.onclick = () => {
        modal.classList.add('show');
    }

    closeBtn.onclick = () => {
        modal.classList.remove('show');
    }

    // Đóng modal khi bấm bên ngoài
    window.onclick = (e) => {
        if (e.target === modal) modal.classList.remove('show');
    }
}

