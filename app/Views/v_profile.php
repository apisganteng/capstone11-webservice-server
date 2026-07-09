<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-8 col-md-10 mx-auto">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title" style="background-color: #007bff; color: white; padding: 10px; border-radius: 5px; width: 100%;">Profil Pengguna</h5>
        <div class="row">
          <div class="col-sm-6">
            <div class="mb-3">
              <label class="form-label"><strong>Username:</strong></label>
              <p class="form-control-plaintext"><?= esc($username) ?></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="mb-3">
              <label class="form-label"><strong>Role:</strong></label>
              <p class="form-control-plaintext"><?= esc($role) ?></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="mb-3">
              <label class="form-label"><strong>Email:</strong></label>
              <p class="form-control-plaintext"><?= esc($email) ?></p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="mb-3">
              <label class="form-label"><strong>Waktu Login:</strong></label>
              <p class="form-control-plaintext"><?= esc($waktu_login) ?></p>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-6">
            <div class="mb-3">
              <label class="form-label"><strong>Status Login:</strong></label>
              <p class="form-control-plaintext"><?= esc($status_login) ?></p>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <a href="#" class="btn btn-primary">Edit Profil</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>