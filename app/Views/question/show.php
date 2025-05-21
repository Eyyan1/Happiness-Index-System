<h2>Question Details #<?= esc($question['id']) ?></h2>
<p><strong>Text:</strong> <?= esc($question['question']) ?></p>
<p><strong>Options:</strong> <?= esc($question['frm_option']) ?></p>
<p><strong>Type:</strong> <?= esc($question['type']) ?></p>
<p><strong>Order:</strong> <?= esc($question['order_by']) ?></p>
<p><strong>Survey:</strong> <?= esc($question['survey_id']) ?></p>
<a href="<?= site_url('question') ?>">Back to list</a>
