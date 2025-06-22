<?= view('templates/header') ?>

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title m-0">User Manager</h3>
    <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal-new-user">
      + New User
    </button>
  </div>
  <div class="card-body">
    <table id="users-table" class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Email</th>
          <th>Answered?</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $i => $u): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= esc("$u[FIRSTNAME] $u[LASTNAME]") ?></td>
          <td><?= esc($u['EMAIL']) ?></td>
          <td>
            <span class="badge badge-<?= $u['has_answered'] ? 'success' : 'secondary' ?>">
              <?= $u['has_answered'] ? 'Yes' : 'No' ?>
            </span>
          </td>
          <td class="text-nowrap">
            <!-- 1) Notify (bell) -->
            <button class="btn btn-sm btn-warning btn-notify" data-id="<?= $u['ID'] ?>">
              <i class="fas fa-bell"></i>
            </button>
            <!-- 2) View (eye) -->
            <a href="<?= site_url("user/{$u['ID']}") ?>" class="btn btn-sm btn-info">
              <i class="fas fa-eye"></i>
            </a>
            <!-- 3) Delete (trash) -->
            <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $u['ID'] ?>">
              <i class="fas fa-trash-alt"></i>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Notify Modal -->
<div class="modal fade" id="modal-send-notif" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <form id="form-send-notif" class="modal-content">
      <?= csrf_field() ?>
      <input type="hidden" name="user_id" id="notif-user-id">
      <div class="modal-header">
        <h5 class="modal-title">Send Notification</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label for="notif-message">Message</label>
          <textarea class="form-control" id="notif-message" name="message" rows="3"
                    placeholder="Type your message…"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-warning">Send</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<!-- New User Modal (reuses your register form) -->
<div class="modal fade" id="modal-new-user" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="<?= site_url('user/create') ?>" method="post" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title">New User</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?= view('auth/register') ?>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit User Modal (AJAX loaded) -->
<div class="modal fade" id="modal-edit-user">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-edit-user" method="post">
        <?= csrf_field() ?>
        <div class="modal-header">
          <h5 class="modal-title">Edit User</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <!-- AJAX injects form here -->
        </div>
        <div class="modal-footer">
          <button class="btn btn-primary" type="submit">Update</button>
          <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= view('templates/footer') ?>

<script>
$(function(){
  // 1) DataTable (hide global search)
  $('#users-table').DataTable({ dom: 'lrtip' });

  // 2) Delete
  $('.btn-delete').click(async function(){
    if (!confirm('Delete this user?')) return;
    const id = $(this).data('id');
    const res = await fetch(`<?= site_url('user/delete') ?>/${id}`, {
      method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    const json = await res.json();
    if (json.success) location.reload();
    else alert('Failed to delete');
  });

  // 3) Notify → open modal
  $('.btn-notify').click(function(){
    const id = $(this).data('id');
    $('#notif-user-id').val(id);
    $('#notif-message').val("You haven't answered your survey yet.");
    $('#modal-send-notif').modal('show');
  });

  // 4) Send notification via AJAX
  $('#form-send-notif').submit(function(e){
    e.preventDefault();
    const id = $('#notif-user-id').val();
    const msg = $('#notif-message').val().trim();
    if (!msg) return alert('Please enter a message.');
    $.post(`<?= site_url('user/notify') ?>/${id}`, {
      message: msg,
      <?= csrf_token() ?>: '<?= csrf_hash() ?>'
    })
    .done(res => {
      $('#modal-send-notif').modal('hide');
      // show a Bootstrap toast
      $('body').append(`
        <div class="toast notify-toast" data-delay="3000">
          <div class="toast-header">
            <strong class="mr-auto">Notification</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
          </div>
          <div class="toast-body">
            Sent successfully!
          </div>
        </div>
      `);
      $('.notify-toast').toast('show')
        .on('hidden.bs.toast', function(){ $(this).remove() });
    })
    .fail(() => alert('Failed to send notification.'));
  });

  // 5) Edit form into modal (optional)
  $('.btn-info').click(async function(e){
    e.preventDefault();
    const url = $(this).attr('href');
    const html = await fetch(url.replace('/user/','/user/').replace(/\/$/, '') + '/edit').then(r=>r.text());
    $('#form-edit-user')
      .attr('action', url.replace('/user/','/user/') + '/update')
      .find('.modal-body').html(html)
      .end()
      .parents('.modal').modal('show');
  });
});
</script>
