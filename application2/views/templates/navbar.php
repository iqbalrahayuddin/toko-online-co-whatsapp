<?php
$settingss = $this->db->get('settings')->row_array();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Navbar Responsif</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    .navbar {
      height: 60px; /* Atur tinggi navbar */
    }

    .navbar-brand img {
      height: 40px; /* Atur tinggi logo */
      width: auto;  /* Sesuaikan lebar secara otomatis */
    }

    .navbar .icon-search-navbar {
      cursor: pointer;
    }

    .search-form {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      justify-content: center;
      align-items: center;
      z-index: 9999;
    }

    .search-form form {
      display: flex;
      align-items: center;
    }

    .search-form input[type="text"] {
      width: 70%;
      padding: 10px;
      border: none;
      border-radius: 0;
    }

    .search-form button {
      padding: 10px 20px;
      border: none;
      background: #ff6b6b;
      color: white;
    }

    .search-form .fa-times {
      position: absolute;
      top: 20px;
      right: 20px;
      color: white;
      cursor: pointer;
      font-size: 24px;
    }

    .navbar-collapse {
      transition: all 0.3s ease;
    }

    @media (max-width: 767.98px) {
      .navbar-cart-inform {
        display: block;
        margin-top: 10px;
        text-align: center;
      }
      .navbar-toggler {
        margin-right: 15px;
      }
      .navbar-brand {
        margin-right: auto;
      }
      .navbar-collapse {
        justify-content: center;
        background-color: transparent;
      }
      .navbar-nav {
        text-align: center;
      }
      .navbar-nav .nav-item {
        margin-bottom: 10px;
      }
      .icon-search-navbar {
        display: inline-block;
      }
      .navbar-collapse.collapsing {
        height: auto;
      }
      .navbar-collapse.show {
        display: block !important;
        height: auto !important;
        background-color: rgba(0, 0, 0, 0.8);
      }
      .navbar-nav .nav-link {
        color: #fff !important; /* Mengubah warna tulisan menjadi putih */
      }
    }

    @media (min-width: 768px) {
      .navbar-cart-inform {
        display: inline-block;
      }
      .icon-search-navbar {
        display: none;
      }
      .navbar-collapse {
        justify-content: flex-end;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark" style="background-color: <?= $this->Settings_model->general()["navbar_color"]; ?> !important">
    <div class="container">
      <a class="navbar-brand mr-5" href="<?= base_url(); ?>"><img src="<?= base_url(); ?>assets/images/logo/<?= $settingss['logo']; ?>" alt="logo"></a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <i class="fa text-light ml-3 icon-search-navbar mobile-search fa-search d-lg-none"></i>
      <div class="collapse navbar-collapse ml-3" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <a class="nav-link" href="<?= base_url(); ?>">Beranda</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link text-light dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Kategori
            </a>
            <?php $categories = $this->Categories_model->getCategories(); ?>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <?php foreach($categories->result_array() as $cat): ?>
                <a class="dropdown-item" href="<?= base_url(); ?>c/<?= $cat['slug']; ?>"><?= $cat['name']; ?></a>
              <?php endforeach; ?>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="<?= base_url(); ?>products">Semua Produk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-light" href="https://hamungheulloh.com/">Berita Terkini</a>
          </li>
        </ul>
        <i class="fa text-light icon-search-navbar desktop-search fa-search d-none d-lg-block"></i>
        <a href="<?= base_url(); ?>cart" class="text-light navbar-cart-inform ml-3">
          <i class="fa fa-shopping-cart"></i>
          <?php if($this->cart->total_items() > 0){ ?>
            Keranjang(<?= count($this->cart->contents()); ?>)
          <?php }else{ ?>
            Keranjang
          <?php } ?>
        </a>
      </div>
    </div>
  </nav>
  <div class="top-nav"></div>

  <div class="search-form">
    <i class="fa fa-times"></i>
    <form action="<?= base_url(); ?>search" method="get">
      <input type="text" placeholder="Cari produk" autocomplete="off" name="q">
      <button type="submit">Cari</button>
    </form>
  </div>
  <div class="top-nav"></div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script>
    document.querySelector('.icon-search-navbar').addEventListener('click', function() {
      document.querySelector('.search-form').style.display = 'flex';
    });

    document.querySelector('.search-form .fa-times').addEventListener('click', function() {
      document.querySelector('.search-form').style.display = 'none';
    });

    $(document).ready(function(){
      $('.navbar-toggler').click(function(){
        $('.navbar-collapse').collapse('toggle');
      });
    });
  </script>
</body>
</html>
