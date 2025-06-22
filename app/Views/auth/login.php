<?= view('templates/header', ['hideNavbar' => true]) ?>

<div class="container vh-100 d-flex align-items-center">
  <div class="row w-100 justify-content-center">
    <div class="col-md-5">
      <h3 class="mb-4 text-center">Login</h3>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form action="<?= site_url('login') ?>" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" class="form-control" required value="<?= old('email') ?>">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button class="btn btn-primary w-100">Login</button>
      </form>

      <p class="mt-3 text-center">
        Don’t have an account? <a href="<?= site_url('register') ?>">Register</a>
      </p>
    </div>
  </div>
</div>

<?= view('templates/footer') ?>
