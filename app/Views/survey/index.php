<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><strong>Survey List</strong></h4>
        <a href="<?= site_url('survey/new') ?>" class="btn btn-primary">+ Add Survey</a>
      </div>

      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Start</th>
            <th>End</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($surveys as $index => $survey): ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><strong><?= esc($survey['TITLE']) ?></strong></td>
              <td><?= esc($survey['DESCRIPTION']) ?></td>
              <td><?= date('M d, Y', strtotime($survey['START_DATE'])) ?></td>
              <td><?= date('M d, Y', strtotime($survey['END_DATE'])) ?></td>
              <td class="text-center">
                <a href="<?= site_url('survey/' . $survey['ID'] . '/edit') ?>" class="btn btn-sm btn-primary" title="Edit">
                  <i class="fas fa-pen"></i>
                </a>
                <a href="<?= site_url('survey/' . $survey['ID']) ?>" class="btn btn-sm btn-info" title="View">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="<?= site_url('survey/delete/' . $survey['ID']) ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                  <i class="fas fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
