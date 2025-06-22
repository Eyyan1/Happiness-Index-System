<?= view('templates/header') ?>

<div class="container-fluid mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><strong>All Survey Answers</strong></h4>
  </div>

  <?php if (empty($answers)): ?>
    <div class="alert alert-info">
      No survey answers yet. Encourage everyone to submit the survey!
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Survey ID</th>
            <th>Question ID</th>
            <th>Answer</th>
            <th>Date Created</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($answers as $i => $ans): ?>
            <tr>
              <td><?= $i + 1 ?></td>
              <td><?= esc($ans['SURVEY_ID']) ?></td>
              <td><?= esc($ans['QUESTION_ID']) ?></td>
              <td><?= esc($ans['ANSWER_TEXT']) ?></td>
              <td><?= esc($ans['DATE_CREATED']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?= view('templates/footer') ?>
