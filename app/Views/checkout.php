<?= $this->extend('layout'); ?>
<?= $this->section('content'); ?>

<?php
$errors = session()->getFlashdata('errors') ?? [];

// Tentukan tier promo hadiah untuk JS
$tier_hadiah = 0;
if ($cart_total > 15000000)     $tier_hadiah = 2; // parfum eksklusif
elseif ($cart_total > 5000000)  $tier_hadiah = 1; // parfum lokal
?>

<style>
/* ── Checkout page custom styles ──────────────────────────── */
.checkout-card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 2px 16px rgba(0,0,0,.08);
}
.checkout-card .card-header {
    border-radius: 14px 14px 0 0;
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    color: #fff;
    padding: 14px 20px;
    font-weight: 600;
    font-size: .95rem;
    letter-spacing: .3px;
}
.checkout-card .card-header i { opacity: .85; }

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    border-bottom: 1px dashed #e9ecef;
    font-size: .9rem;
}
.summary-row:last-child { border-bottom: none; }
.summary-row.grand {
    border-top: 2px solid #dee2e6;
    border-bottom: none;
    padding-top: 12px;
    margin-top: 4px;
    font-size: 1.05rem;
    font-weight: 700;
}
.badge-voucher {
    font-size: .72rem;
    padding: 3px 8px;
    border-radius: 20px;
}
.voucher-list-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border: 1.5px solid #e9ecef;
    border-radius: 10px;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: 8px;
}
.voucher-list-item:hover,
.voucher-list-item.active {
    border-color: #6f42c1;
    background: #f8f4ff;
}
.voucher-list-item .kode {
    font-weight: 700;
    font-size: .9rem;
    color: #6f42c1;
    font-family: monospace;
    background: #ede9fb;
    padding: 3px 10px;
    border-radius: 6px;
    letter-spacing: 1px;
}
.voucher-list-item .diskon { font-weight: 600; color: #198754; }
.promo-banner {
    border-radius: 12px;
    padding: 12px 16px;
    font-size: .85rem;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.promo-banner .icon { font-size: 1.5rem; flex-shrink: 0; }
.step-badge {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg,#6f42c1,#e83e8c);
    color: #fff;
    font-size: .78rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 8px;
    flex-shrink: 0;
}
.section-title {
    font-size: .92rem;
    font-weight: 700;
    color: #444;
    display: flex;
    align-items: center;
    margin-bottom: 14px;
    padding-bottom: 8px;
    border-bottom: 2px solid #f0e8ff;
}
.form-control:focus, .form-select:focus {
    border-color: #6f42c1;
    box-shadow: 0 0 0 .2rem rgba(111,66,193,.15);
}
.btn-checkout {
    background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
    border: none;
    border-radius: 10px;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: .4px;
    padding: 13px;
    transition: opacity .2s;
}
.btn-checkout:hover { opacity: .9; }
.cart-item-row td { vertical-align: middle; font-size: .88rem; }
.cart-item-row .item-name { font-weight: 600; }
.qty-badge {
    background: #f0e8ff;
    color: #6f42c1;
    font-weight: 700;
    border-radius: 6px;
    padding: 2px 8px;
    font-size: .8rem;
}

/* ── Kurir card ───────────────────────────────────────────── */
.kurir-card {
    display: block;
    cursor: pointer;
    margin: 0;
}
.kurir-inner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    transition: all .2s;
    background: #fff;
    position: relative;
}
.kurir-card:hover .kurir-inner {
    border-color: #a78bfa;
    background: #faf8ff;
}
.kurir-card.selected .kurir-inner {
    border-color: #6f42c1;
    background: #f3f0ff;
    box-shadow: 0 0 0 3px rgba(111,66,193,.12);
}
.kurir-logo { font-size: 1.5rem; flex-shrink: 0; }
.kurir-info { flex: 1; min-width: 0; }
.kurir-name { font-weight: 700; font-size: .88rem; }
.kurir-etd  { font-size: .75rem; }
.kurir-price {
    font-weight: 700;
    font-size: .88rem;
    color: #6f42c1;
    white-space: nowrap;
}
.kurir-check {
    font-size: 1.1rem;
    color: #6f42c1;
    display: none;
    flex-shrink: 0;
}
.kurir-card.selected .kurir-check { display: block; }
</style>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon lengkapi data berikut:</strong>
        <ul class="mb-0 mt-1 ps-3">
            <?php foreach ($errors as $err) : ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
        <i class="bi bi-x-circle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?= form_open('transaksi/buat_pesanan') ?>
<input type="hidden" name="username" value="<?= session()->get('username') ?>">
<input type="hidden" name="ongkir"   id="ongkir_value" value="0">

<div class="row g-4">

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- KOLOM KIRI                                                  -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="col-xl-7 col-lg-7">

        <!-- ── 1. Data Penerima ─────────────────────────────────── -->
        <div class="card checkout-card mb-4">
            <div class="card-header">
                <span class="step-badge">1</span> Data Penerima
            </div>
            <div class="card-body p-4">

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama Penerima <span class="text-danger">*</span></label>
                        <input type="text" name="nama_penerima" id="nama_penerima"
                               class="form-control <?= isset($errors['nama_penerima']) ? 'is-invalid' : '' ?>"
                               value="<?= old('nama_penerima', session()->get('username')) ?>"
                               placeholder="Nama lengkap penerima">
                        <?php if (isset($errors['nama_penerima'])) : ?>
                            <div class="invalid-feedback"><?= esc($errors['nama_penerima']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Nomor HP <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                            <input type="text" name="no_hp" id="no_hp"
                                   class="form-control <?= isset($errors['no_hp']) ? 'is-invalid' : '' ?>"
                                   value="<?= old('no_hp') ?>" placeholder="08xxxxxxxxxx">
                            <?php if (isset($errors['no_hp'])) : ?>
                                <div class="invalid-feedback"><?= esc($errors['no_hp']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" name="email" id="email"
                                   class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   value="<?= old('email', session()->get('email')) ?>"
                                   placeholder="email@example.com">
                            <?php if (isset($errors['email'])) : ?>
                                <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" id="alamat" rows="3"
                                  class="form-control <?= isset($errors['alamat']) ? 'is-invalid' : '' ?>"
                                  placeholder="Nama jalan, nomor rumah, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos"><?= old('alamat') ?></textarea>
                        <?php if (isset($errors['alamat'])) : ?>
                            <div class="invalid-feedback"><?= esc($errors['alamat']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Contoh: Jl. Melati No.12, RT 03/RW 05, Banyumanik, Semarang 50261</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Catatan untuk Kurir <small class="text-muted fw-normal">(opsional)</small></label>
                        <textarea name="catatan" id="catatan" rows="2"
                                  class="form-control"
                                  placeholder="Contoh: Titip ke satpam jika tidak ada di rumah"><?= old('catatan') ?></textarea>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── 2. Pilih Kurir ───────────────────────────────────── -->
        <div class="card checkout-card mb-4">
            <div class="card-header">
                <span class="step-badge">2</span> Pilih Kurir Pengiriman
            </div>
            <div class="card-body p-4">

                <p class="text-muted small mb-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Pilih salah satu kurir. Ongkos kirim sudah termasuk biaya pengemasan.
                </p>

                <div class="row g-3" id="kurir-options">

                    <!-- JNE Regular -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="15000">
                            <input type="radio" name="kurir_pilihan" value="15000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">🚚</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">JNE Regular</div>
                                    <div class="kurir-etd text-muted">Est. 3–5 hari</div>
                                </div>
                                <div class="kurir-price">Rp 15.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                    <!-- JNE YES -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="25000">
                            <input type="radio" name="kurir_pilihan" value="25000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">⚡</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">JNE YES</div>
                                    <div class="kurir-etd text-muted">Est. 1–2 hari</div>
                                </div>
                                <div class="kurir-price">Rp 25.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                    <!-- J&T Express -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="18000">
                            <input type="radio" name="kurir_pilihan" value="18000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">📦</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">J&amp;T Express</div>
                                    <div class="kurir-etd text-muted">Est. 2–4 hari</div>
                                </div>
                                <div class="kurir-price">Rp 18.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                    <!-- SiCepat -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="20000">
                            <input type="radio" name="kurir_pilihan" value="20000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">🏃</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">SiCepat REG</div>
                                    <div class="kurir-etd text-muted">Est. 2–3 hari</div>
                                </div>
                                <div class="kurir-price">Rp 20.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                    <!-- AnterAja -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="12000">
                            <input type="radio" name="kurir_pilihan" value="12000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">🛵</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">AnterAja</div>
                                    <div class="kurir-etd text-muted">Est. 3–6 hari</div>
                                </div>
                                <div class="kurir-price">Rp 12.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                    <!-- Gosend Instant -->
                    <div class="col-sm-6">
                        <label class="kurir-card" data-ongkir="35000">
                            <input type="radio" name="kurir_pilihan" value="35000" class="d-none kurir-radio">
                            <div class="kurir-inner">
                                <div class="kurir-logo">🏍️</div>
                                <div class="kurir-info">
                                    <div class="kurir-name">GoSend Instant</div>
                                    <div class="kurir-etd text-muted">Hari yang sama</div>
                                </div>
                                <div class="kurir-price">Rp 35.000</div>
                                <i class="bi bi-check-circle-fill kurir-check"></i>
                            </div>
                        </label>
                    </div>

                </div>

                <!-- Tampilan ongkir terpilih -->
                <div class="mt-3 p-3 rounded-3 bg-light d-flex justify-content-between align-items-center" id="ongkir-summary" style="display:none!important;">
                    <span class="text-muted small">Ongkos kirim terpilih:</span>
                    <span class="fw-bold text-primary" id="ongkir_display_label">—</span>
                </div>

            </div>
        </div>

        <!-- ── 3. Voucher ────────────────────────────────────────── -->
        <div class="card checkout-card mb-4">
            <div class="card-header">
                <span class="step-badge">3</span> Voucher Promo
            </div>
            <div class="card-body p-4">

                <!-- Daftar voucher tersedia -->
                <p class="section-title"><i class="bi bi-tags me-2 text-purple"></i>Voucher Tersedia</p>

                <div class="voucher-list-item" onclick="pakaiVoucher('PROMO2025')">
                    <span class="kode">PROMO2025</span>
                    <div>
                        <div class="diskon">Diskon 10%</div>
                        <div class="text-muted" style="font-size:.8rem;">Berlaku untuk semua produk</div>
                    </div>
                    <span class="ms-auto badge bg-success badge-voucher">Aktif</span>
                </div>

                <div class="voucher-list-item" onclick="pakaiVoucher('PROMO2026')">
                    <span class="kode">PROMO2026</span>
                    <div>
                        <div class="diskon">Diskon 15%</div>
                        <div class="text-muted" style="font-size:.8rem;">Berlaku untuk semua produk</div>
                    </div>
                    <span class="ms-auto badge bg-success badge-voucher">Aktif</span>
                </div>

                <div class="voucher-list-item" onclick="pakaiVoucher('AKHIRTAHUN')">
                    <span class="kode">AKHIRTAHUN</span>
                    <div>
                        <div class="diskon">Diskon 25%</div>
                        <div class="text-muted" style="font-size:.8rem;">Promo spesial akhir tahun</div>
                    </div>
                    <span class="ms-auto badge bg-danger badge-voucher">Spesial</span>
                </div>

                <!-- Input manual voucher -->
                <div class="mt-3">
                    <label class="form-label fw-semibold">Masukkan Kode Voucher</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-ticket-perforated"></i></span>
                        <input type="text" name="voucher_code" id="voucher_code"
                               class="form-control text-uppercase fw-bold"
                               value="<?= old('voucher_code') ?>"
                               placeholder="Ketik atau klik voucher di atas"
                               maxlength="50">
                        <button type="button" class="btn btn-outline-primary" id="btn_cek_voucher">
                            <i class="bi bi-check2-circle me-1"></i>Pakai
                        </button>
                    </div>
                    <div id="voucher_info" class="mt-2"></div>
                </div>

            </div>
        </div>

    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!-- KOLOM KANAN: Ringkasan Pesanan                            -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <div class="col-xl-5 col-lg-5">

        <!-- ── Banner Promo Hadiah ─────────────────────────────── -->
        <?php if ($tier_hadiah === 2) : ?>
        <div class="promo-banner bg-success bg-opacity-10 border border-success mb-4">
            <div class="icon">🎁</div>
            <div>
                <strong class="text-success">Selamat! Kamu dapat hadiah!</strong><br>
                <span class="text-success">Free Parfum Eksklusif 30ml senilai <strong>Rp 1.500.000</strong> sudah ditambahkan.</span>
            </div>
        </div>
        <?php elseif ($tier_hadiah === 1) : ?>
        <div class="promo-banner bg-success bg-opacity-10 border border-success mb-4">
            <div class="icon">🎁</div>
            <div>
                <strong class="text-success">Selamat! Kamu dapat hadiah!</strong><br>
                <span class="text-success">Free Parfum Lokal 30ml senilai <strong>Rp 500.000</strong> sudah ditambahkan.</span>
            </div>
        </div>
        <?php else : ?>
        <div class="promo-banner bg-warning bg-opacity-10 border border-warning mb-4">
            <div class="icon">✨</div>
            <div>
                <strong>Promo Hadiah Parfum!</strong><br>
                Belanja <strong>Rp 5.000.001+</strong> → Free Parfum Lokal 30ml (Rp 500.000)<br>
                Belanja <strong>Rp 15.000.001+</strong> → Free Parfum Eksklusif 30ml (Rp 1.500.000)
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Ringkasan Pesanan ──────────────────────────────── -->
        <div class="card checkout-card mb-4">
            <div class="card-header">
                <i class="bi bi-receipt me-2"></i> Ringkasan Pesanan
            </div>
            <div class="card-body p-4">

                <!-- Item produk -->
                <?php if (!empty($cart_items)) : ?>
                    <?php foreach ($cart_items as $item) : ?>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div style="max-width:60%">
                            <div style="font-size:.87rem;font-weight:600;"><?= esc($item['name']) ?></div>
                            <span class="qty-badge"><?= $item['qty'] ?>x</span>
                        </div>
                        <div class="text-end" style="font-size:.87rem;font-weight:600;">
                            Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <hr class="my-3">
                <?php endif; ?>

                <!-- Kalkulasi -->
                <div class="summary-row">
                    <span class="text-muted">Subtotal</span>
                    <span class="fw-semibold">Rp <?= number_format($cart_total, 0, ',', '.') ?></span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">Ongkos Kirim</span>
                    <span id="display_ongkir" class="fw-semibold">Rp 0</span>
                </div>

                <div class="summary-row">
                    <span class="text-muted">
                        Biaya Jasa
                        <span class="badge bg-secondary rounded-pill ms-1" style="font-size:.7rem;">
                            <?= $cart_total <= 10000000 ? '1%' : '2%' ?>
                        </span>
                    </span>
                    <span id="display_biaya_jasa" class="fw-semibold">
                        Rp <?= number_format($biaya_jasa, 0, ',', '.') ?>
                    </span>
                </div>

                <div class="summary-row text-success" id="row_diskon_voucher">
                    <span>
                        <i class="bi bi-ticket-perforated me-1"></i>Diskon Voucher
                        <span id="label_voucher" class="badge bg-success rounded-pill ms-1" style="font-size:.7rem;display:none;"></span>
                    </span>
                    <span id="display_diskon_voucher" class="fw-semibold">- Rp 0</span>
                </div>

                <div class="summary-row text-success" id="row_free_parfum" <?= $free_parfum <= 0 ? 'style="display:none"' : '' ?>>
                    <span>
                        <i class="bi bi-gift me-1"></i>
                        <?= !empty($label_hadiah) ? esc($label_hadiah) : 'Free Parfum' ?>
                    </span>
                    <span id="display_free_parfum" class="fw-semibold">
                        - Rp <?= number_format($free_parfum, 0, ',', '.') ?>
                    </span>
                </div>

                <div class="summary-row grand">
                    <span>Grand Total</span>
                    <span id="display_grand_total" class="text-primary">
                        Rp <?= number_format($cart_total + $biaya_jasa - $free_parfum, 0, ',', '.') ?>
                    </span>
                </div>

            </div>
        </div>

        <!-- Hidden kalkulasi -->
        <input type="hidden" id="js_subtotal"    value="<?= $cart_total ?>">
        <input type="hidden" id="js_biaya_jasa"  value="<?= $biaya_jasa ?>">
        <input type="hidden" id="js_free_parfum" value="<?= $free_parfum ?>">

        <!-- Tombol submit -->
        <button type="submit" class="btn btn-checkout text-white w-100 mb-3">
            <i class="bi bi-bag-check-fill me-2"></i>Konfirmasi Pesanan
        </button>

        <div class="text-center">
            <a href="<?= base_url('keranjang') ?>" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i>Kembali ke Keranjang
            </a>
        </div>

    </div>
</div>

<?= form_close() ?>

<?= $this->endSection(); ?>

<?= $this->section('script') ?>
<script>
$(document).ready(function () {

    // ── Nilai dasar dari PHP ──────────────────────────────────────
    const subtotal   = parseFloat($('#js_subtotal').val())   || 0;
    const biayaJasa  = parseFloat($('#js_biaya_jasa').val()) || 0;
    const freeParfum = parseFloat($('#js_free_parfum').val())|| 0;
    let ongkir        = 0;
    let diskonVoucher = 0;

    // Daftar voucher — harus sinkron dengan promo_helper.php
    const daftarVoucher = {
        'PROMO2025':  0.10,
        'PROMO2026':  0.15,
        'AKHIRTAHUN': 0.25
    };

    function rupiah(n) {
        return 'Rp ' + Math.round(n).toLocaleString('id-ID');
    }

    function hitungTotal() {
        const grand = Math.max(subtotal + ongkir + biayaJasa - diskonVoucher - freeParfum, 0);
        $('#display_ongkir').text(rupiah(ongkir));
        $('#display_diskon_voucher').text('- ' + rupiah(diskonVoucher));
        $('#display_grand_total').text(rupiah(grand));
    }

    hitungTotal();

    // ── Klik item voucher dari daftar ─────────────────────────────
    window.pakaiVoucher = function(kode) {
        $('#voucher_code').val(kode);
        // tandai item aktif
        $('.voucher-list-item').removeClass('active');
        $('.voucher-list-item').filter(function() {
            return $(this).find('.kode').text() === kode;
        }).addClass('active');
        // trigger cek
        $('#btn_cek_voucher').trigger('click');
    };

    // ── Tombol Pakai Voucher ──────────────────────────────────────
    $('#btn_cek_voucher').on('click', function () {
        const kode  = $('#voucher_code').val().trim().toUpperCase();
        const $info = $('#voucher_info');

        if (kode === '') {
            diskonVoucher = 0;
            $info.html('<span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Tidak menggunakan voucher.</span>');
            $('#label_voucher').hide().text('');
        } else if (daftarVoucher.hasOwnProperty(kode)) {
            const pct  = daftarVoucher[kode];
            diskonVoucher = subtotal * pct;
            $info.html(
                '<span class="text-success small fw-semibold">' +
                '<i class="bi bi-check-circle-fill me-1"></i>' +
                'Voucher <strong>' + kode + '</strong> berhasil diterapkan! ' +
                'Hemat ' + (pct * 100) + '% = <strong>' + rupiah(diskonVoucher) + '</strong></span>'
            );
            $('#label_voucher').text(kode).show();
            // sync active state di list
            $('.voucher-list-item').removeClass('active');
            $('.voucher-list-item').filter(function() {
                return $(this).find('.kode').text() === kode;
            }).addClass('active');
        } else {
            diskonVoucher = 0;
            $info.html(
                '<span class="text-danger small"><i class="bi bi-x-circle-fill me-1"></i>' +
                'Kode <strong>' + kode + '</strong> tidak valid.</span>'
            );
            $('#label_voucher').hide().text('');
            $('.voucher-list-item').removeClass('active');
        }

        hitungTotal();
    });

    // Uppercase otomatis saat ketik manual
    $('#voucher_code').on('input', function () {
        $(this).val($(this).val().toUpperCase());
    });

    // Auto-cek jika ada nilai lama (withInput setelah validasi gagal)
    if ($('#voucher_code').val().trim() !== '') {
        $('#btn_cek_voucher').trigger('click');
    }

    // ── Pilih kurir flat-rate ─────────────────────────────────────
    $('.kurir-card').on('click', function () {
        $('.kurir-card').removeClass('selected');
        $(this).addClass('selected');

        ongkir = parseInt($(this).data('ongkir')) || 0;
        $('#ongkir_value').val(ongkir);
        $('#ongkir_display_label').text(rupiah(ongkir));
        $('#ongkir-summary').css('display', 'flex');

        hitungTotal();
    });

});
</script>
<?= $this->endSection() ?>
