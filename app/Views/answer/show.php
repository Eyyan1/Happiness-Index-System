<h2>Answer Details #<?= esc($answer['id']) ?></h2>
<p><strong>Survey ID:</strong> <?= esc($answer['survey_id']) ?></p>
<p><strong>User ID:</strong> <?= esc($answer['user_id']) ?></p>
<p><strong>Question ID:</strong> <?= esc($answer['question_id']) ?></p>
<?php
  $ans = is_object($answer['ANSWER'])
       ? $answer['ANSWER']->load()
       : $answer['ANSWER'];
?>
<p><strong>Answer:</strong><br><?= nl2br(esc($ans)) ?></p>

<p><strong>Created:</strong> <?= esc($answer['date_created']) ?></p>
<a href="<?= site_url('answer') ?>">Back to list</a>
