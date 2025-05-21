
<div class="container mt-4">
  <h4>Edit Survey</h4>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <form action="<?= site_url('survey/update/' . $survey['ID']) ?>" method="post">
    <?= csrf_field() ?>

    <!-- Title -->
    <div class="form-group">
      <label for="title">Title</label>
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
    <div class="form-group">
      <label for="start_date">Start Date</label>
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
    <div class="form-group">
      <label for="end_date">End Date</label>
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
    <div class="form-group">
      <label for="description">Description</label>
      <textarea
        name="description"
        id="description"
        class="form-control"
        rows="3"
      ><?= esc($survey['DESCRIPTION']) ?></textarea>
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="<?= site_url('survey') ?>" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?= view('templates/footer') ?>
