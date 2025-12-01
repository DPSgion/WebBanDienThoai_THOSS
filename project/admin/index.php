<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style_admin.css">

    <title>Admin Page</title>
</head>

<?php
$quanlysanpham = '../assets/js/quanlysanpham.js';
$quanlydonhang = '../assets/js/quanlydonhang.js';
$caidat = '../assets/js/caidat.js';
?>

<body>
    <div class="header">
        <div class="header-left">
            <img src="../assets/images/logodt.png" width="80px">
            <h1>Admin</h1>
        </div>
        <div class="header-right">
            <p class="admin-name">Admin Name</p>
            <button class="logout-btn">Đăng xuất</button>
        </div>
    </div>

    <div class="sidebar">
        <ul>
            <li>
                <a href="#">Dashboard</a>
            </li>
            <li>
                <a href="#" data-page="quanlysanpham.php" data-js="<?php echo $quanlysanpham ?>"
                    data-callback="initQuanLySanPham">Quản lý sản phẩm</a>
            </li>
            <li>
                <a href="#" data-page="quanlydonhang.php" data-js="<?php echo $quanlydonhang ?>"
                    data-callback="initQuanLyDonHang">Quản lý đơn hàng</a>
            </li>
            <li>
                <a href="#" data-page="caidat.php" data-js="<?php echo $caidat ?>"
                    data-callback="initCaiDat">Cài đặt</a>
            </li>
        </ul>
    </div>

    <div class="content" id="content">

    </div>


    <script>
        const loadedScripts = new Set();

        function loadPage(page, jsFile, callbackName) {
            fetch(page)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('content').innerHTML = html;

                    if (jsFile && !loadedScripts.has(jsFile)) {
                        const script = document.createElement('script');
                        script.src = jsFile;
                        script.onload = () => {
                            loadedScripts.add(jsFile);
                            setTimeout(() => {
                                if (callbackName && typeof window[callbackName] === 'function') {
                                    window[callbackName]();
                                }
                            }, 50);
                        };
                        script.onerror = () => {
                            console.error('Lỗi load script:', jsFile);
                        };
                        document.body.appendChild(script);
                    } else {
                        setTimeout(() => {
                            if (callbackName && typeof window[callbackName] === 'function') {
                                window[callbackName]();
                            }
                        }, 50);
                    }
                })
                .catch(err => {
                    document.getElementById('content').innerHTML = "<p>Lỗi tải trang</p>";
                    console.error(err);
                });
        }

        // Event delegation
        document.querySelector('.sidebar').addEventListener('click', function (e) {
            if (e.target.tagName === 'A' && e.target.dataset.page) {
                e.preventDefault();
                const page = e.target.dataset.page;
                const js = e.target.dataset.js || null;
                const callback = e.target.dataset.callback || null;
                loadPage(page, js, callback);
            }
        });
    </script>


</body>

</html>