<style>
  div.promo div.bottom div.card:hover div.card-body p.card-text {
    color: <?= $this->config->item('default_color'); ?>;
  }
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
    0% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.1);
    }
    100% {
      transform: scale(1);
    }
  }
</style>

<div class="wrapper">
    <?php if($promo->num_rows() > 0){ ?>
    <?php if($setting['promo'] == 1){ ?>
    <div class="countdown">
        <p class="lead text-light"><i class="fa fa-fire-alt"></i> Berakhir dalam <span id="countdownPromo"></span></p>
    </div>
    <div class="main-product">
        <?php foreach($promo->result_array() as $p): ?>
            <a href="<?= base_url(); ?>p/<?= $p['slug']; ?>">
            <div class="card">
                <img src="<?= base_url(); ?>assets/images/product/<?= $p['img']; ?>" class="card-img-top" >
                <div class="card-body">
                <p class="card-text mb-0"><?= $p['title']; ?></p>
                <p class="oldPrice mb-0">Rp <?= str_replace(",",".",number_format($p['price'])); ?></p>
                <p class="newPrice">Rp <?= str_replace(",",".",number_format($p['promo_price'])); ?></p>
                </div>
            </div>
            </a>
        <?php endforeach; ?>
        <div class="clearfix"></div>
    </div>
    <?php }else{ ?>
    <div class="countdown">
        <p class="lead text-light"><i class="fa fa-fire-alt"></i> PROMO</span></p>
    </div>
    <div class="alert alert-warning mt-4">Upss.. Tidak ada promo untuk saat ini.</div>
    <?php } ?>
    <?php }else{ ?>
    <div class="countdown">
        <p class="lead text-light"><i class="fa fa-fire-alt"></i> PROMO</span></p>
    </div>
    <div class="alert alert-warning mt-4">Upss.. Tidak ada promo untuk saat ini.</div>
    <?php } ?>
</div>

<!-- WhatsApp Widget -->
<div class="whatsapp-widget">
  <a href="https://wa.me/6285784327759<?= $this->config->item('whatsapp_number'); ?>" target="_blank">
    <img src="<?= base_url(); ?>assets/images/whatsapp-icon.png" alt="WhatsApp">
  </a>
</div>