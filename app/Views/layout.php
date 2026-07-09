<?php
$hlm = "Home";
if (uri_string() != "") {
  $hlm = ucwords(uri_string());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>- Toko - <?php echo $hlm ?></title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <link href="<?= base_url('NiceAdmin/assets/img/favicon.png') ?>" rel="icon">
  <link href="<?= base_url('NiceAdmin/assets/img/apple-touch-icon.png') ?>" rel="apple-touch-icon">

  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="<?= base_url('NiceAdmin/assets/vendor/bootstrap/css/bootstrap.min.css') ?>" rel="stylesheet">
  <link href="<?= base_url('NiceAdmin/assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>" rel="stylesheet">
  <link href="<?= base_url('NiceAdmin/assets/vendor/boxicons/css/boxicons.min.css') ?>" rel="stylesheet">
  
  <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  <link href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/remixicon@2.2.0/fonts/remixicon.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">

  <link href="<?= base_url('NiceAdmin/assets/css/style.css') ?>" rel="stylesheet">
</head>

<body>

  <?= $this->include('components/header') ?>
  <?= $this->include('components/sidebar') ?>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Data Tables</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item">Home</li>
          <?php if ($hlm != "Home") : ?>
            <li class="breadcrumb-item"><?php echo $hlm ?></li>
          <?php endif; ?>
        </ol>
      </nav>
    </div><section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?php echo $hlm ?></h5>
              <?= $this->renderSection('content') ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main><?= $this->include('components/footer') ?>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

  <script src="<?= base_url('NiceAdmin/assets/vendor/apexcharts/apexcharts.min.js') ?>"></script>
  <script src="<?= base_url('NiceAdmin/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('NiceAdmin/assets/vendor/chart.js/chart.umd.js') ?>"></script>
  <script src="<?= base_url('NiceAdmin/assets/vendor/echarts/echarts.min.js') ?>"></script>

  <?= $this->renderSection('script') ?> 

  <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>

  <script src="<?= base_url('NiceAdmin/assets/js/main.js') ?>"></script>

</body>
</html>