<h2>User Details #<?= esc($user['id']) ?></h2>
<p><strong>Name:</strong> <?= esc($user['firstname'].' '.$user['middlename'].' '.$user['lastname']) ?></p>
<p><strong>Contact:</strong> <?= esc($user['contact']) ?></p>
<p><strong>Address:</strong><br><?= nl2br(esc($user['address'])) ?></p>
<p><strong>Email:</strong> <?= esc($user['email']) ?></p>
<p><strong>Type:</strong> <?= esc($user['type']) ?></p>
<p><strong>Created:</strong> <?= esc($user['date_created']) ?></p>
<a href="<?= site_url('user') ?>">Back to list</a>
