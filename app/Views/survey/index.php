<?php helper('text'); ?>

<div class="container-fluid mt-4">
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><strong>Survey List</strong></h4>
        <a href="<?= site_url('survey/new') ?>" class="btn btn-primary">+ Add Survey</a>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Description</th>
              <th>Start</th>
              <th>End</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (! empty($surveys)): ?>
              <?php foreach ($surveys as $index => $survey): ?>
                <?php
                  // Decode entities, strip any HTML, then truncate cleanly
                  $rawDesc   = html_entity_decode($survey['DESCRIPTION'], ENT_QUOTES|ENT_HTML5, 'UTF-8');
                  $cleanDesc = strip_tags($rawDesc);
                  $limit     = 80;
                  $shortDesc = character_limiter($cleanDesc, $limit, '...');
                  $isLong    = mb_strlen($cleanDesc) > $limit;
                ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td><strong><?= esc($survey['TITLE']) ?></strong></td>
                  <td>
                    <?= esc($shortDesc) ?>
                    <?php if ($isLong): ?>
                      <a href="#" data-bs-toggle="modal" data-bs-target="#descModal<?= $survey['ID'] ?>">View More</a>

                      <!-- Modal for full description -->
                      <div class="modal fade" id="descModal<?= $survey['ID'] ?>" tabindex="-1" aria-labelledby="descLabel<?= $survey['ID'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="descLabel<?= $survey['ID'] ?>">Survey Description</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <?= nl2br(esc($cleanDesc)) ?>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><?= date('M d, Y', strtotime($survey['START_DATE'])) ?></td>
                  <td><?= date('M d, Y', strtotime($survey['END_DATE'])) ?></td>
                  <td class="text-center">
                    <a href="<?= site_url('survey/' . $survey['ID'] . '/edit') ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                      <i class="fas fa-pen"></i>
                    </a>
                    <a href="<?= site_url('survey/' . $survey['ID']) ?>" class="btn btn-sm btn-outline-info me-1" title="View">
                      <i class="fas fa-eye"></i>
                    </a>
                    <form action="<?= site_url('survey/delete/' . $survey['ID']) ?>" method="post" class="d-inline">
                      <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this survey?')">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                    <a href="<?= site_url('answer/' . $survey['ID']) ?>" class="btn btn-sm btn-outline-success ms-1" title="Answer">
                      <i class="fas fa-check-circle"></i> Answer
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">No surveys available.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- FontAwesome (for icons) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<!-- Bootstrap Modal JS (required for modal to work) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
