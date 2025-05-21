<h2>Create New Question</h2>
<form action="<?= site_url('question') ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="question_id" value="0">

  <label>Question Text</label>
  <textarea name="question" required></textarea>

  <label>Options (JSON)</label>
  <textarea name="frm_option"></textarea>

  <label>Type</label>
  <input type="text" name="type" required>

  <label>Order</label>
  <input type="number" name="order_by" required>

  <label>Survey ID</label>
  <input type="number" name="survey_id" required>

  <button type="submit">Save</button>
</form>
