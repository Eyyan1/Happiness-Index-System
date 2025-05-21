<h2>Edit Question #<?= esc($question['ID']) ?></h2>
<form action="<?= site_url("question/{$question['ID']}") ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="put">
  <input type="hidden" name="survey_id" value="<?= esc($question['SURVEY_ID']) ?>">

  <label>Question Text</label>
  <textarea name="question" required><?= esc($question['QUESTION']) ?></textarea>

  <label>Options (JSON)</label>
  <textarea name="frm_option"><?= esc($question['FRM_OPTION']) ?></textarea>

  <label>Type</label>
  <input type="text" name="type" value="<?= esc($question['TYPE']) ?>" required>

  <label>Order</label>
  <input type="number" name="order_by" value="<?= esc($question['ORDER_BY']) ?>" required>

  <button type="submit">Update</button>
</form>
