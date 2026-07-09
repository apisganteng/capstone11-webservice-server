<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

History Transaksi Pembelian <strong><?= esc($username) ?></strong>
<hr>

<div class="table-responsive">
    <table class="table datatable">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID Pembelian</th>
                <th scope="col">Waktu Pembelian</th>
                <th scope="col">Grand Total</th>
                <th scope="col">Penerima</th>
                <th scope="col">Status</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($transactions)) : ?>
                <?php foreach ($transactions as $index => $item) : ?>
                    <tr>
                        <th scope="row"><?= $index + 1 ?></th>
                        <td><?= $item['id'] ?></td>
                        <td><?= $item['created_at'] ?></td>
                        <td><?= number_to_currency($item['total_harga'], 'IDR') ?></td>
                        <td><?= esc($item['nama_penerima'] ?? $item['username']) ?></td>
                        <td>
                            <?= ($item['status'] == '1' || $item['status'] === 'Selesai')
                                ? '<span class="badge bg-success">Sudah Selesai</span>'
                                : '<span class="badge bg-warning text-dark">Belum Selesai</span>' ?>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-success btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal-<?= $item['id'] ?>">
                                Detail
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if (!empty($transactions)) : ?>
    <?php foreach ($transactions as $item) : ?>

        <!-- ======================================================== -->
        <!-- Detail Modal — Transaksi #<?= $item['id'] ?>             -->
        <!-- ======================================================== -->
        <div class="modal fade" id="detailModal-<?= $item['id'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-receipt me-1"></i>
                            Detail Pesanan #<?= $item['id'] ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">

                            <!-- ── Kolom Kiri: Data Pengiriman ── -->
                            <div class="col-md-6">
                                <h6 class="fw-semibold border-bottom pb-1 mb-2">
                                    <i class="bi bi-truck me-1"></i> Data Pengiriman
                                </h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" style="width:45%">Nama Penerima</td>
                                            <td>: <strong><?= esc($item['nama_penerima'] ?? '-') ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Nomor HP</td>
                                            <td>: <?= esc($item['no_hp'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Email</td>
                                            <td>: <?= esc($item['email'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Provinsi</td>
                                            <td>: <?= esc($item['provinsi'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kota / Kab.</td>
                                            <td>: <?= esc($item['kota'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kecamatan</td>
                                            <td>: <?= esc($item['kecamatan'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Kode Pos</td>
                                            <td>: <?= esc($item['kode_pos'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Alamat</td>
                                            <td>: <?= esc($item['alamat'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Catatan</td>
                                            <td>: <?= !empty($item['catatan']) ? esc($item['catatan']) : '<span class="text-muted fst-italic">-</span>' ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- ── Kolom Kanan: Produk + Ringkasan Pembayaran ── -->
                            <div class="col-md-6">

                                <!-- Daftar Produk -->
                                <h6 class="fw-semibold border-bottom pb-1 mb-2">
                                    <i class="bi bi-bag me-1"></i> Produk Dipesan
                                </h6>

                                <?php if (!empty($products[$item['id']])) : ?>
                                    <?php foreach ($products[$item['id']] as $idx => $prod) : ?>
                                        <div class="d-flex align-items-start mb-2">
                                            <?php
                                            $imgPath = FCPATH . 'img/' . ($prod['foto'] ?? '');
                                            if (!empty($prod['foto']) && file_exists($imgPath)) : ?>
                                                <img src="<?= base_url('img/' . $prod['foto']) ?>"
                                                     width="55" height="55"
                                                     class="img-thumbnail me-2 flex-shrink-0"
                                                     style="object-fit:cover;">
                                            <?php endif; ?>
                                            <div class="small">
                                                <div class="fw-semibold"><?= esc($prod['nama']) ?></div>
                                                <div class="text-muted">
                                                    <?= $prod['jumlah'] ?> pcs
                                                    &times; <?= number_to_currency($prod['harga'], 'IDR') ?>
                                                </div>
                                                <div><?= number_to_currency($prod['subtotal_harga'], 'IDR') ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p class="text-muted small">Tidak ada produk.</p>
                                <?php endif; ?>

                                <!-- Ringkasan Pembayaran -->
                                <h6 class="fw-semibold border-bottom pb-1 mb-2 mt-3">
                                    <i class="bi bi-credit-card me-1"></i> Ringkasan Pembayaran
                                </h6>

                                <?php
                                // Hitung subtotal produk dari detail (bukan total_harga)
                                $subtotal_produk = 0;
                                if (!empty($products[$item['id']])) {
                                    foreach ($products[$item['id']] as $prod) {
                                        $subtotal_produk += (float)($prod['subtotal_harga'] ?? 0);
                                    }
                                }
                                ?>

                                <table class="table table-sm table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted">Total Belanja</td>
                                            <td class="text-end">
                                                <?= number_to_currency($subtotal_produk, 'IDR') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Ongkos Kirim</td>
                                            <td class="text-end">
                                                <?= number_to_currency($item['ongkir'] ?? 0, 'IDR') ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Biaya Jasa</td>
                                            <td class="text-end">
                                                <?= number_to_currency($item['biaya_jasa'] ?? 0, 'IDR') ?>
                                            </td>
                                        </tr>
                                        <tr class="text-success">
                                            <td>
                                                Voucher
                                                <?php if (!empty($item['voucher_code'])) : ?>
                                                    <span class="badge bg-success ms-1">
                                                        <?= esc($item['voucher_code']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end text-success">
                                                - <?= number_to_currency($item['diskon_voucher'] ?? 0, 'IDR') ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['free_parfum']) && $item['free_parfum'] > 0) : ?>
                                        <tr class="text-success">
                                            <td>
                                                <i class="bi bi-gift me-1"></i>
                                                <?php
                                                // Tentukan label hadiah berdasarkan nilai
                                                if ($item['free_parfum'] >= 1500000) {
                                                    echo 'Free Parfum Eksklusif 30ml';
                                                } else {
                                                    echo 'Free Parfum Lokal 30ml';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-end text-success">
                                                - <?= number_to_currency($item['free_parfum'], 'IDR') ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr class="fw-bold border-top">
                                            <td>Grand Total</td>
                                            <td class="text-end text-primary">
                                                <?= number_to_currency($item['total_harga'], 'IDR') ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div><!-- /.row -->
                    </div><!-- /.modal-body -->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Detail Modal -->

    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
