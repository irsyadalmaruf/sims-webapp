<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'SIMS WebApp' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('Assets/css/login/login.css') ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container-login">
        <div class="login-form">
            <h2>
                <i class="fas fa-bag-shopping icon"></i>
                <span class="app-title">SIMS Web App</span>
            </h2>
            <p class="sub-title">Masuk atau buat akun untuk memulai</p>

            <div class="form-input-login">
                <form action="/auth/process" method="post">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-envelope"></i>
                            <input type="email" name="email" class="form-input" placeholder="Masukan email anda" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" class="form-input password-input" placeholder="Masukan password anda" required>
                            <i class="fas fa-eye eye-icon" onclick="togglePasswordVisibility()"></i>
                        </div>
                    </div>
                    <button type="submit" class="submit-btn">Masuk</button>
                </form>
            </div>
        </div>

        <div class="right-side">
            <img src="<?= base_url('Assets/img/login/login.png') ?>" alt="Login Illustration">
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: "<?= session()->getFlashdata('error') ?>",
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: "<?= session()->getFlashdata('success') ?>",
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.href = "/produk"; 
                });
            });
        </script>
    <?php endif; ?>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('.password-input');
            const eyeIcon = document.querySelector('.eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>