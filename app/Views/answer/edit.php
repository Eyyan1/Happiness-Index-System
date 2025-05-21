<h2>Edit Answer #<?= esc($answer['ID']) ?></h2>
<form action="<?= site_url("answer/{$answer['ID']}") ?>" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="_method" value="put">

  <label>Survey ID</label>
  <input type="number" name="survey_id" value="<?= esc($answer['SURVEY_ID']) ?>" required>

  <label>User ID</label>
  <input type="number" name="user_id" value="<?= esc($answer['USER_ID']) ?>" required>

  <label>Question ID</label>
  <input type="number" name="question_id" value="<?= esc($answer['QUESTION_ID']) ?>" required>

  <label>Answer</label>
  <textarea name="answer" required><?= esc($answer['ANSWER']) ?></textarea>

  <button type="submit">Update</button>
</form>
