

<div class="container mt-4">
  <?php if (session()->getFlashdata('message')): ?>
  <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <?php $val = session()->get('validation'); ?>

  <form action="<?= site_url('survey/create') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Title -->
    <div class="form-group">
      <label for="title">Title</label>
      <input
        type="text"
        name="title"
        id="title"
        value="<?= old('title') ?>"
        class="form-control <?= isset($val) && $val->hasError('title') ? 'is-invalid' : '' ?>"
      >
      <?php if(isset($val) && $val->hasError('title')): ?>
        <div class="invalid-feedback">
          <?= $val->getError('title') ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Start Date -->
    <div class="form-group">
      <label for="start_date">Start Date</label>
      <input
        type="date"
        name="start_date"
        id="start_date"
        value="<?= old('start_date') ?>"
        class="form-control <?= isset($val) && $val->hasError('start_date') ? 'is-invalid' : '' ?>"
      >
      <?php if(isset($val) && $val->hasError('start_date')): ?>
        <div class="invalid-feedback">
          <?= $val->getError('start_date') ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- End Date -->
    <div class="form-group">
      <label for="end_date">End Date</label>
      <input
        type="date"
        name="end_date"
        id="end_date"
        value="<?= old('end_date') ?>"
        class="form-control <?= isset($val) && $val->hasError('end_date') ? 'is-invalid' : '' ?>"
      >
      <?php if(isset($val) && $val->hasError('end_date')): ?>
        <div class="invalid-feedback">
          <?= $val->getError('end_date') ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Description -->
    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        name="description"
        id="description"
        class="form-control"
        rows="3"><?= old('description') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
  </form>
</div>

<?= view('templates/footer') ?>
