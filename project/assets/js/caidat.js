window.initCaiDat = function () {
    const emailForm = document.getElementById('emailForm');
    const emailMessage = document.getElementById('emailMessage');

    emailForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const email = document.getElementById('email').value;

        // Gửi dữ liệu lên server
        emailMessage.textContent = "Đang lưu email...";
        emailMessage.className = "message";
    });

    const passwordForm = document.getElementById('passwordForm');
    const passwordMessage = document.getElementById('passwordMessage');

    passwordForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            passwordMessage.textContent = "Mật khẩu mới không khớp!";
            passwordMessage.className = "message error";
            return;
        }

        passwordMessage.textContent = "Đang lưu mật khẩu...";
        passwordMessage.className = "message";
    });
}


