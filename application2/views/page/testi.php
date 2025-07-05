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
    <div class="title-head">
        <h2 class="title">Testimoni</h2>
    </div>
    <?php if($testi->num_rows() > 0){ ?>
        <div class="testi mt-4">
            <div class="row">
            <?php foreach($testi->result_array() as $data){ ?>
                <div class="col-lg-4 mb-4">
                    <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= $data['name']; ?></h5>
                        <p class="card-text"><?= $data['content']; ?></p>
                    </div>
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    <?php }else{ ?>
        <div class="alert alert-warning mt-4">Upss.. Belum ada testimoni</div>
    <?php } ?>
</div>

<!-- WhatsApp Widget -->
<div class="whatsapp-widget">
  <a href="https://wa.me/6285784327759<?= $this->config->item('whatsapp_number'); ?>" target="_blank">
    <img src="<?= base_url(); ?>assets/images/whatsapp-icon.png" alt="WhatsApp">
  </a>
</div>