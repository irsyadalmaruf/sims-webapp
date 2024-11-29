<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'SIMS WebApp' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="<?= base_url('Assets/css/main.css') ?>">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?= $this->renderSection('head') ?>
</head>
<body>
    <div class="wrapper">
        <aside id="sidebar" class="bg-danger text-white">
            <div class="sidebar-header p-3 d-flex justify-content-between align-items-center">
                <span class="sidebar-logo fs-5"><i class="fas fa-bag-shopping icon"></i> SIMS Web App</span>
                <button id="sidebarToggle" class="btn text-white">
                    <i class="fas fa-bars"></i>
                </button>
            </div>

            <ul class="sidebar-nav">
                <li class="sidebar-item" title="Produk">
                    <a href="/produk" class="nav-link <?= (current_url() == base_url('produk')) ? 'active' : '' ?>">
                        <i class="fas fa-box"></i>
                        <span>Produk</span>
                    </a>
                </li>
                <li class="sidebar-item" title="Profil">
                    <a href="/profil" class="nav-link <?= (current_url() == base_url('profil')) ? 'active' : '' ?>">
                        <i class="fas fa-user"></i>
                        <span>Profil</span>
                    </a>
                </li>
                <li class="sidebar-item" title="Logout">
                    <a href="/logout" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main">
            <div class="container mt-4">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <?php if (session()->has('swal')): ?>
        <script>
            let swalData = <?= json_encode(session()->getFlashdata('swal')) ?>;
            swalData.showConfirmButton = false; 
            swalData.timer = 1500;  

            Swal.fire(swalData);
        </script>
    <?php endif; ?>

    <script>
        const sidebar = document.getElementById("sidebar");
        const sidebarToggle = document.getElementById("sidebarToggle");

        sidebarToggle.addEventListener("click", function () {
            if (window.innerWidth > 768) {
                sidebar.classList.toggle("collapsed");
            }
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth <= 768) {
                sidebar.classList.add("collapsed");
                sidebarToggle.style.pointerEvents = "none";  
            } else {
                sidebar.classList.remove("collapsed");
                sidebarToggle.style.pointerEvents = "auto";  
            }
        });

        if (window.innerWidth <= 768) {
            sidebar.classList.add("collapsed");
            sidebarToggle.style.pointerEvents = "none";  
        } else {
            sidebarToggle.style.pointerEvents = "auto"; 
        }

        window.onbeforeunload = function () {
            navigator.sendBeacon('<?= base_url('/logout') ?>');
        };
    </script>
</body>
</html>