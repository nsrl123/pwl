<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<h3>Profil Pengguna</h3>

<ul style="list-style-type: none; padding-left: 0;">
    <li><strong>Username:</strong> <?= session()->get('username') ?></li>
    <li><strong>Role:</strong> <?= session()->get('role') ?></li>
    <li><strong>Email:</strong> <?= session()->get('email') ?? 'Tidak tersedia' ?></li>
    <li><strong>Waktu Login:</strong> <?= session()->get('waktu_login') ?? '-' ?></li>
    <li><strong>Status Login:</strong> <?= session()->get('isLoggedIn') ? 'Sudah Login' : 'Tidak Login' ?></li>
</ul>

<?= $this->endSection() ?>