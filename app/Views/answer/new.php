<h2>Create New Answer</h2>
<form action="<?= site_url('answer') ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="answer_id" value="0">

  <label>Survey ID</label>
  <input type="number" name="survey_id" required>

  <label>User ID</label>
  <input type="number" name="user_id" required>

  <label>Question ID</label>
  <input type="number" name="question_id" required>

  <label>Answer</label>
  <textarea name="answer" required></textarea>

  <button type="submit">Save</button>
</form>
