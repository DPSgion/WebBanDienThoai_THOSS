// quanlysanpham.js
window.initQuanLySanPham = function() {
    const modal = document.getElementById("modal-them");
    const btn = document.querySelector(".themsp button");
    const span = modal.querySelector(".close");

    if (!btn) return;

    btn.onclick = () => modal.style.display = "block";
    span.onclick = () => modal.style.display = "none";
    window.onclick = e => { if(e.target === modal) modal.style.display = "none"; }
}
