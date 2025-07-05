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

<div class="title">
    <h2 style="text-transform: uppercase;"><?= $page['title']; ?></h2>
</div>

<div class="wrapper">
    <?= $page['content']; ?>
</div>

<!-- WhatsApp Widget -->
<div class="whatsapp-widget">
  <a href="https://wa.me/6285784327759<?= $this->config->item('whatsapp_number'); ?>" target="_blank">
    <img src="<?= base_url(); ?>assets/images/whatsapp-icon.png" alt="WhatsApp">
  </a>
</div>