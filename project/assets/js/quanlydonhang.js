
window.initQuanLyDonHang = function () {
    // Dummy JS chỉ để đổi trạng thái frontend
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            row.querySelector('td:nth-child(6)').textContent = 'Đang giao';
            btn.textContent = 'Hoàn thành';
            btn.classList.remove('btn-approve');
            btn.classList.add('btn-complete');
        });
    });

    document.querySelectorAll('.btn-complete').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('tr');
            row.querySelector('td:nth-child(6)').textContent = 'Hoàn thành';
            const today = new Date();
            const dateStr = today.getDate().toString().padStart(2, '0') + '/' + (today.getMonth() + 1).toString().padStart(2, '0') + '/' + today.getFullYear();
            row.querySelector('td:nth-child(8)').innerHTML = '<span class="status-completed">Hoàn thành (' + dateStr + ')</span>';
            btn.remove();
        });
    });

}

