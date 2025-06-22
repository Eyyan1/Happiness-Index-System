<?= view('templates/header', ['hideSidebar'=>true]) ?>

<div class="container mt-5" style="max-width:600px;">
  <h3>My Profile</h3>

  <!-- display validation errors -->
  <?php if($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= site_url('profile') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Name -->
    <div class="form-group">
      <label>First Name</label>
      <input type="text" name="firstname" class="form-control"
             value="<?= old('firstname', $user['FIRSTNAME']) ?>" required>
    </div>
    <div class="form-group">
      <label>Last Name</label>
      <input type="text" name="lastname" class="form-control"
             value="<?= old('lastname', $user['LASTNAME']) ?>" required>
    </div>

    <!-- Contact & Address -->
    <div class="form-group">
      <label>Contact</label>
      <input type="text" name="contact" class="form-control"
             value="<?= old('contact', $user['CONTACT']) ?>">
    </div>
    <div class="form-group">
      <label>Address</label>
      <textarea name="address" class="form-control" rows="3"><?= old('address', $user['ADDRESS']) ?></textarea>
    </div>

    <hr>

    <!-- Change Password -->
    <h5>Change Password (optional)</h5>
    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="password" class="form-control">
    </div>
    <div class="form-group">
      <label>Confirm New Password</label>
      <input type="password" name="pass_confirm" class="form-control">
    </div>

    <button class="btn btn-primary">Save Changes</button>
  </form>
</div>

<?= view('templates/footer') ?>
