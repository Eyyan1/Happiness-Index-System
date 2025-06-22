<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><strong>All Questions</strong></h4>
    <a href="<?= site_url('question/new') ?>" class="btn btn-primary">+ New Question</a>
  </div>

  <div class="table-responsive">
    <table id="questionTable" class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Question</th>
          <th>Type</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($questions as $q): ?>
          <tr>
            <td><?= esc($q['ID']) ?></td>
            <td><?= esc(is_object($q['QUESTION']) ? $q['QUESTION']->read($q['QUESTION']->size()) : $q['QUESTION']) ?></td>
            <td><?= esc($q['TYPE']) ?></td>
            <td class="text-center">
              <a href="<?= site_url("question/{$q['ID']}") ?>" class="btn btn-sm btn-outline-info me-1" title="View">
                <i class="fas fa-eye"></i>
              </a>
              <a href="<?= site_url("question/{$q['ID']}/edit") ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                <i class="fas fa-pen"></i>
              </a>
              <a href="<?= site_url("question/{$q['ID']}") ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-outline-danger" title="Delete">
                <i class="fas fa-trash"></i>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Dependencies -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

<script>
  $(function () {
    $('#questionTable').DataTable({
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      language: {
        search: '',
        searchPlaceholder: 'Search...'
      }
    });
  });
</script>
