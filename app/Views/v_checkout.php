<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validasi Gagal!</strong>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-6">
            <?= form_open('buy', 'class="row g-3"') ?>

    <?= form_hidden('username', session()->get('username')) ?>
    <input type="hidden" id="total_harga" name="total_harga" value="<?= esc($total) ?>">

    <div class="col-12">
        <?= form_label('Kode Voucher', 'voucher_code', ['class' => 'form-label']) ?>
        <?= form_input([
            'name'  => 'voucher_code',
            'id'    => 'voucher_code',
            'class' => 'form-control',
            'value' => ''
        ]) ?>
    </div>

<div class="col-12">
    <?= form_label('Nama', 'nama', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'nama',
        'id'       => 'nama',
        'class'    => 'form-control',
        'value'    => session()->get('username'),
        'readonly' => true]) ?>
</div>
<div class="col-12">
    <?= form_label('Alamat', 'alamat', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'  => 'alamat',
        'id'    => 'alamat',
        'class' => 'form-control']) ?>
</div> 
<div class="col-12"> 
    <?= form_label('Kelurahan', 'kelurahan', ['class' => 'form-label']) ?>
    <?= form_dropdown('kelurahan', [], '', ['id' => 'kelurahan', 'class' => 'form-control']) ?>
</div>
<div class="col-12"> 
    <?= form_label('Layanan', 'layanan', ['class' => 'form-label']) ?> 
    <?= form_dropdown('layanan', [], '', ['id' => 'layanan', 'class' => 'form-control']) ?>
</div>
<div class="col-12">
    <?= form_label('Ongkir', 'ongkir', ['class' => 'form-label']) ?>
    <?= form_input([
        'name'     => 'ongkir',
        'id'       => 'ongkir',
        'class'    => 'form-control',
        'readonly' => true]) ?>
</div>
<div class="col-12">
    <?= form_submit(
        'submit',
        'Buat Pesanan',
        ['class' => 'btn btn-primary', 'id' => 'submit']) ?>
</div>

<?= form_close() ?> 
    </div>
    <div class="col-lg-6">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <h4 class="card-title mb-4">Ringkasan Pesanan</h4>
          <div class="table-responsive">
            <table class="table table-borderless align-middle mb-0">
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Harga</th>
                  <th>Jumlah</th>
                  <th>Sub Total</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if (!empty($items)) :
                  foreach ($items as $index => $item) :
                ?>
                    <tr>
                      <td><?= $item['name'] ?></td>
                      <td><?= number_to_currency($item['price'], 'IDR') ?></td>
                      <td><?= $item['qty'] ?></td>
                      <td><?= number_to_currency($item['price'] * $item['qty'], 'IDR') ?></td>
                    </tr>
                <?php
                  endforeach;
                endif;
                ?>
                <tr class="border-top">
                  <td colspan="2"></td>
                  <td class="text-muted">Subtotal</td>
                  <td class="fw-semibold"><?= number_to_currency($total, 'IDR') ?></td>
                </tr>
                <tr class="table-danger">
                  <td colspan="2"></td>
                  <td class="text-danger">Diskon Voucher</td>
                  <td><span id="diskon_voucher" class="fw-semibold text-danger">-IDR 0</span></td>
                </tr>
                <tr class="table-info">
                  <td colspan="2"></td>
                  <td class="text-primary">PPN (11%)</td>
                  <td><span id="ppn" class="fw-semibold text-primary">IDR 0</span></td>
                </tr>
                <tr class="table-warning">
                  <td colspan="2"></td>
                  <td class="text-warning">Biaya Admin</td>
                  <td><span id="biaya_admin" class="fw-semibold text-warning">IDR 0</span></td>
                </tr>
                <tr class="table-success border-top">
                  <td colspan="2"></td>
                  <td class="text-success">Subtotal (+PPN+Admin-Voucher)</td>
                  <td class="fw-semibold text-success"><span id="subtotal_after_voucher"><?= number_to_currency($total, 'IDR') ?></span></td>
                </tr>
                <tr class="border-top">
                  <td colspan="2"></td>
                  <td class="fw-semibold">Grand Total (incl. Ongkir)</td>
                  <td class="fw-bold"><span id="grand_total"><?= number_to_currency($total, 'IDR') ?></span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('script') ?>
<script>
$(document).ready(function() {
    let ongkir = 0;
    let subtotal = <?= $total ?>;
    let voucherRates = {
        'FLASH10': 0.10,
        'FLASH15': 0.15,
        'MEMBER20': 0.20
    };

    function formatRupiah(value) {
        return `IDR ${value.toLocaleString('id-ID')}`;
    }

    function validateForm() {
        const alamat = $('#alamat').val().trim();
        const kelurahan = $('#kelurahan').val();
        const layanan = $('#layanan').val();
        const ongkirVal = $('#ongkir').val();

        const isValid = alamat.length >= 5 && kelurahan && layanan && ongkirVal;
        $('#submit').prop('disabled', !isValid);
        
        return isValid;
    }

    function hitungTotal() {
        let voucherCode = ($('#voucher_code').val() || '').toUpperCase().trim();
        let rate = voucherRates[voucherCode] || 0;
        let diskon = Math.round(subtotal * rate);

        let subtotalAfterVoucher = subtotal - diskon;
        let ppn = Math.round(subtotalAfterVoucher * 0.11);

        let adminRate = 0.006;
        if (subtotalAfterVoucher > 40000000) {
            adminRate = 0.01;
        } else if (subtotalAfterVoucher > 20000000) {
            adminRate = 0.008;
        }

        let biayaAdmin = Math.round(subtotalAfterVoucher * adminRate);
        let grandTotal = subtotalAfterVoucher + ppn + biayaAdmin + ongkir;

        $('#ongkir').val(ongkir);
        $('#diskon_voucher').text(rate > 0 ? `-IDR ${diskon.toLocaleString('id-ID')} (${(rate * 100).toFixed(0)}%)` : '-IDR 0');
        $('#ppn').text(formatRupiah(ppn));
        $('#biaya_admin').text(formatRupiah(biayaAdmin));
        $('#subtotal_after_voucher').text(formatRupiah(subtotalAfterVoucher));
        $('#grand_total').text(formatRupiah(grandTotal));
        $('#total_harga').val(grandTotal);
        
        validateForm();
    }

    // Initialize - disable submit button
    validateForm();
    hitungTotal();

    $('#kelurahan').select2({
        placeholder: 'Cari daerah tujuan',
        minimumInputLength: 3,
        ajax: {
            url: '<?= site_url('ajax/destinations') ?>',
            dataType: 'json',
            delay: 300,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return data;
            },
            cache: true
        }
    });

    $('#kelurahan').on('change', function() {
        const id_kelurahan = $(this).val();
        $('#layanan').empty().append('<option value="">-- Pilih Layanan --</option>');
        ongkir = 0;
        $('#ongkir').val(0);
        hitungTotal();

        if (!id_kelurahan) {
            return;
        }

        $.ajax({
            url: '<?= site_url('ajax/costs') ?>',
            dataType: 'json',
            data: {
                destination: id_kelurahan
            },
            success: function(data) {
                data.forEach(function(item) {
                    $('#layanan').append($('<option>', {
                        value: item.cost,
                        text: `${item.description} (${item.service}) : estimasi ${item.etd}`
                    }));
                });
                validateForm();
            },
            error: function() {
                alert('Error mengambil data layanan. Silakan coba lagi.');
                validateForm();
            }
        });
    });

    $('#layanan').on('change', function() {
        ongkir = parseInt($(this).val()) || 0;
        hitungTotal();
    });

    $('#alamat').on('input', function() {
        validateForm();
    });

    $('#voucher_code').on('input', function() {
        hitungTotal();
    });
});
</script>
<?= $this->endSection() ?>