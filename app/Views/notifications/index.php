<?= view('templates/header') ?>

<div class="container mt-4">
  <h2>All Notifications</h2>
  <?php if (empty($notifications)): ?>
    <p class="text-muted">You have no notifications.</p>
  <?php else: ?>
    <ul class="list-group">
      <?php foreach ($notifications as $n): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center<?= $n['IS_READ']==='N' ? ' list-group-item-warning' : '' ?>">
          <div>
            <?= esc($n['MESSAGE']) ?>
            <br>
            <small class="text-muted"><?= date('M d, Y H:i', strtotime($n['DATE_CREATED'])) ?></small>
          </div>
          <?php if ($n['IS_READ']==='N'): ?>
            <form method="post" action="<?= site_url('notifications/mark-read') ?>">
              <?= csrf_field() ?>
              <button class="btn btn-sm btn-outline-success">Mark Read</button>
            </form>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</div>

<?= view('templates/footer') ?>
