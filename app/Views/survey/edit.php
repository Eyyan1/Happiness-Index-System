<div class="container mt-4">
  <h4 class="mb-4"><strong>Edit Survey</strong></h4>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <form action="<?= site_url('survey/update/' . $survey['ID']) ?>" method="post">
    <?= csrf_field() ?>

    <!-- Title -->
    <div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input
        type="text"
        name="title"
        id="title"
        class="form-control"
        value="<?= esc($survey['TITLE']) ?>"
        required
      >
    </div>

    <!-- Start Date -->
    <div class="mb-3">
      <label for="start_date" class="form-label">Start Date</label>
      <input
        type="date"
        name="start_date"
        id="start_date"
        class="form-control"
        value="<?= esc($survey['START_DATE']) ?>"
        required
      >
    </div>

    <!-- End Date -->
    <div class="mb-3">
      <label for="end_date" class="form-label">End Date</label>
      <input
        type="date"
        name="end_date"
        id="end_date"
        class="form-control"
        value="<?= esc($survey['END_DATE']) ?>"
        required
      >
    </div>

    <!-- Description -->
    <div class="mb-4">
      <label for="description" class="form-label">Description</label>
      <textarea
        name="description"
        id="description"
        class="form-control"
        rows="4"
        required
      ><?= esc($survey['DESCRIPTION']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="<?= site_url('survey') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= view('templates/footer') ?>
