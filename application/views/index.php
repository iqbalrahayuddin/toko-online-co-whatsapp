<style>
  /* Gaya Umum */
  div.promo div.bottom div.card:hover div.card-body p.card-text,
  div.product-wrapper div.main-product div.card:hover div.card-body p.card-text {
    color: <?= $this->config->item('default_color'); ?>;
  }

  .whatsapp-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
    animation: pulse 2s infinite;
  }

  .whatsapp-widget img {
    width: 60px;
    height: 60px;
  }

  @keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
  }

  /* Grid Responsif untuk Kategori dan Produk */
  .category-menu .main-category, .main-product {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 10px;
  }

  .category-menu .item, .main-product .card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 10px;
    justify-content: center;
  }

  .category-menu .item img, .main-product .card img {
    max-width: 100%;
    height: auto;
  }

  .category-menu .item p, .main-product .card p {
    margin: 5px 0 0 0; /* Tambahkan margin atas */
    padding: 0;
  }

  .modal-body .main-category {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
  }

  .modal-body .main-category .item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .modal-body .main-category .item img {
    max-width: 100%;
    height: auto;
  }

  .modal-body .main-category .item p {
    margin: 5px 0 0 0; /* Tambahkan margin atas */
    padding: 0;
  }

  .product-wrapper {
    margin-bottom: 20px;
  }

  .product-wrapper .title {
    font-size: 1.2em;
  }

  .product-wrapper .main-product {
    margin-top: 10px;
  }

  .product-wrapper img.banner-package {
    width: 100%;
    height: auto;
  }

  .promo .card-header,
  .promo .card-body p,
  .main-product .card-body p.newPrice {
    color: <?= $this->config->item('default_color'); ?>;
  }

  @media (max-width: 767px) {
    .promo .card-header,
    .main-product .card {
      text-align: center;
    }
  }

  /* CSS untuk mengatur tata letak */
  .news-section .container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
  }
  .news-section .main-article {
    flex: 2;
    background-size: cover;
    background-position: center;
    height: 400px;
    position: relative;
  }
  .news-section .side-articles {
    flex: 1.8;
    display: flex;
    flex-direction: column;
    gap: 20px;
  }
  .news-section .sub-articles {
    display: flex;
    gap: 20px;
    flex: 1;
  }
  .news-section .article {
    position: relative;
    background-size: cover;
    background-position: center;
    height: 190px;
    flex: 1;
  }
  .news-section .article-text {
    position: absolute;
    bottom: 10px;
    left: 10px;
    color: white;
    background-color: rgba(0, 0, 0, 0.6);
    padding: 5px 10px;
    border-radius: 5px;
    max-width: 90%;
  }
  .news-section .article-text h2 {
    font-size: 1rem;
    margin: 0;
  }
  .news-section .article-text p {
    font-size: 0.8rem;
    margin: 0;
  }
  .news-section .article a {
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    width: 100%;
  }
  .news-section .article a:hover {
    text-decoration: underline;
  }

  /* Responsif untuk tablet dan perangkat yang lebih kecil */
  @media (max-width: 1024px) {
    .news-section .container {
      flex-direction: column;
    }
    .news-section .main-article {
      height: 300px;
    }
    .news-section .sub-articles {
      flex-direction: row;
      gap: 10px;
    }
    .news-section .article {
      height: 200px;
    }
  }
  /* CSS untuk menyembunyikan berita utama pada perangkat HP */
  @media (max-width: 767px) {
    .news-section .main-article {
      display: none;
    }
  }

  /* Memastikan gambar dan teks sejajar */
  .category-menu .item, .main-product .card {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 10px;
    justify-content: center;
  }

  .category-menu .item p, .main-product .card p {
    margin-top: 10px; /* Tambahkan margin atas */
  }
</style>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="category-menu">
  <div class="main-category">
    <div class="item" data-toggle="modal" data-target="#modalMoreCategory">
      <center><img src="<?= base_url(); ?>assets/images/icon/category-more.png"></center>
      <p>Lainnya</p>
    </div>
    <?php foreach($categoriesLimit->result_array() as $c): ?>
    <a href="<?= base_url(); ?>c/<?= $c['slug']; ?>">
      <div class="item">
        <center><img src="<?= base_url(); ?>assets/images/icon/<?= $c['icon']; ?>"></center>
        <p><?= $c['name']; ?></p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<!-- Modal More Category -->
<div class="modal fade" id="modalMoreCategory" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">KATEGORI</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="main-category">
          <?php foreach($categories->result_array() as $c): ?>
          <a href="<?= base_url(); ?>c/<?= $c['slug']; ?>">
            <div class="item">
              <center><img src="<?= base_url(); ?>assets/images/icon/<?= $c['icon']; ?>"></center>
              <p><?= $c['name']; ?></p>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>


  <?php if($promo->num_rows() > 0 && $setting['promo'] == 1): ?>
  <div class="promo">
    <div class="card-header" style="background-color: <?= $this->config->item('default_color'); ?>">
      <p class="lead text-light"><i class="fa fa-fire-alt"></i> Berakhir dalam <span id="countdownPromo"></span></p>
      <a href="<?= base_url(); ?>promo"><button class="float-right" style="color: <?= $this->config->item('default_color'); ?>">Lihat Semua</button></a>
    </div>
    <div class="bottom">
      <?php foreach($getPromo->result_array() as $data): ?>
      <a href="<?= base_url(); ?>p/<?= $data['slug']; ?>">
        <div class="card">
          <img src="<?= base_url(); ?>assets/images/product/<?= $data['img'] ?>" class="card-img-top">
          <div class="card-body">
            <p class="card-text mb-0"><?= $data['title'] ?></p>
            <p class="oldPrice mb-0">Rp <?= str_replace(".",",",number_format($data['price'])) ?></p>
            <p class="newPrice">Rp <?= str_replace(".",",",number_format($data['promo_price'])) ?></p>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if($best->num_rows() > 0): ?>
  <div class="product-wrapper best-product">
      </br>
    <div class="top d-flex justify-content-between">
      <h2 class="title">PRODUK</h2>
      <a href="<?= base_url(); ?>products">Lihat semua ></a>
    </div>
    <div class="main-product mt-2">
      <?php foreach($best->result_array() as $p): ?>
      <div>
        <a href="<?= base_url(); ?>p/<?= $p['slug']; ?>">
          <div class="card">
            <img src="<?= base_url(); ?>assets/images/product/<?= $p['img']; ?>" class="card-img-top">
            <div class="card-body">
              <p class="card-text mb-0"><?= $p['title']; ?></p>
              <?php if($setting['promo'] == 1 && $p['promo_price'] > 0): ?>
              <p class="oldPrice mb-0">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['promo_price'])); ?></p>
              <?php else: ?>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php foreach($package->result_array() as $p): ?>
  <div class="product-wrapper best-product">
    <h2 class="title float-left" style="text-transform: uppercase;"><?= $p['title']; ?></h2>
    <a href="<?= base_url(); ?>package/<?= $p['slug']; ?>" class="float-right">Lihat semua ></a>
    <img src="<?= base_url(); ?>assets/images/banner/<?= $p['banner'] ?>" class="banner-package" alt="banner <?= $p['title']; ?>">
    <div class="main-product">
      <?php
        $this->db->limit(6);
        $this->db->join("products", "package_product.product=products.id");
        $this->db->where('package_product.package', $p['id']);
        $packdata = $this->db->get('package_product');
      ?>
      <?php foreach($packdata->result_array() as $p): ?>
      <div>
        <a href="<?= base_url(); ?>p/<?= $p['slug']; ?>">
          <div class="card">
            <img src="<?= base_url(); ?>assets/images/product/<?= $p['img']; ?>" class="card-img-top">
            <div class="card-body">
              <p class="card-text mb-0"><?= $p['title']; ?></p>
              <?php if($setting['promo'] == 1 && $p['promo_price'] > 0): ?>
              <p class="oldPrice mb-0">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['promo_price'])); ?></p>
              <?php else: ?>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if($recent->num_rows() > 0): ?>
  <div class="product-wrapper best-product">
    <div class="top d-flex justify-content-between">
      <h2 class="title">PRODUK TERBARU</h2>
      <a href="<?= base_url(); ?>products">Lihat semua ></a>
    </div>
    <div class="main-product mt-2">
      <?php foreach($recent->result_array() as $p): ?>
      <div>
        <a href="<?= base_url(); ?>p/<?= $p['slug']; ?>">
          <div class="card">
            <img src="<?= base_url(); ?>assets/images/product/<?= $p['img']; ?>" class="card-img-top">
            <div class="card-body">
              <p class="card-text mb-0"><?= $p['title']; ?></p>
              <?php if($setting['promo'] == 1 && $p['promo_price'] > 0): ?>
              <p class="oldPrice mb-0">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['promo_price'])); ?></p>
              <?php else: ?>
              <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- WhatsApp Widget -->
  <div class="whatsapp-widget">
    <a href="https://wa.me/<?= $this->Settings_model->general()["whatsappv2"]; ?>" target="_blank">
      <img src="<?= base_url(); ?>assets/images/whatsapp-icon.png" alt="WhatsApp">
    </a>
  </div>

  <!-- News Section -->
  <!--nav>
<div class="news-section">
  <div class="product-wrapper best-product">
    <div class="top d-flex justify-content-between">
      <h2 class="title">BERITA TERKINI</h2>
      <a href="https://hamungheulloh.com/">Lihat semua ></a>
    </div>
    <div class="container">
      <div id="main-article" class="main-article">
        <a href="#" id="main-article-link" class="article-link">
          <div class="article-text">
            <h2>Menunggu Berita Terbaru</h2>
            <p>Tanggal dan Penulis</p>
          </div>
        </a>
      </div>
      <div class="side-articles">
        <div class="sub-articles">
          <div id="side-article1" class="article">
            <a href="#" id="side-article1-link" class="article-link">
              <div class="article-text">
                <h2>Menunggu Berita Terbaru</h2>
                <p>Tanggal dan Penulis</p>
              </div>
            </a>
          </div>
          <div id="side-article2" class="article">
            <a href="#" id="side-article2-link" class="article-link">
              <div class="article-text">
                <h2>Menunggu Berita Terbaru</h2>
                <p>Tanggal dan Penulis</p>
              </div>
            </a>
          </div>
        </div>
        <div class="sub-articles">
          <div id="side-article3" class="article">
            <a href="#" id="side-article3-link" class="article-link">
              <div class="article-text">
                <h2>Menunggu Berita Terbaru</h2>
                <p>Tanggal dan Penulis</p>
              </div>
            </a>
          </div>
          <div id="side-article4" class="article">
            <a href="#" id="side-article4-link" class="article-link">
              <div class="article-text">
                <h2>Menunggu Berita Terbaru</h2>
                <p>Tanggal dan Penulis</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</nav-->
  <script>
    async function fetchArticles() {
      const response = await fetch('https://nandradigital.com/wp-json/wp/v2/posts?_embed');
      const articles = await response.json();

      if (articles.length > 0) {
        const mainArticle = articles[0];
        const mainImage = mainArticle._embedded['wp:featuredmedia'][0].source_url;
        const mainLink = mainArticle.link;
        document.getElementById('main-article').style.backgroundImage = `url(${mainImage})`;
        document.getElementById('main-article-link').href = mainLink;
        document.querySelector('#main-article .article-text h2').innerText = mainArticle.title.rendered;
        document.querySelector('#main-article .article-text p').innerText = new Date(mainArticle.date).toLocaleDateString();

        for (let i = 1; i <= 4; i++) {
          const sideArticle = articles[i];
          const sideImage = sideArticle._embedded['wp:featuredmedia'][0].source_url;
          const sideLink = sideArticle.link;
          document.getElementById(`side-article${i}`).style.backgroundImage = `url(${sideImage})`;
          document.getElementById(`side-article${i}-link`).href = sideLink;
          document.querySelector(`#side-article${i} .article-text h2`).innerText = sideArticle.title.rendered;
          document.querySelector(`#side-article${i} .article-text p`).innerText = new Date(sideArticle.date).toLocaleDateString();
        }
      }
    }

    fetchArticles();
  </script>
  <p></p>
</body>
</html>
