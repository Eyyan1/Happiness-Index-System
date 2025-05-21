<?= view('templates/header') ?>

<!-- Content Header -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Users</h1>
      </div>
      <div class="col-sm-6 text-right">
        <a href="<?= site_url('user/new') ?>" class="btn btn-primary">
          <i class="fas fa-user-plus"></i> Add New User
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <div class="card">
      <div class="card-body">
        <table id="userTable" class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Contact #</th>
              <th>Role</th>
              <th>Email</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($users as $u): ?>
            <tr>
              <td><?= esc($u['ID']) ?></td>
              <td><?= esc($u['LASTNAME']) ?>, <?= esc($u['FIRSTNAME']) ?> <?= esc($u['MIDDLENAME']) ?></td>
              <td><?= esc($u['CONTACT']) ?></td>
              <td>
                <?php
                  switch($u['TYPE']) {
                    case 1: echo 'Admin'; break;
                    case 2: echo 'Staff'; break;
                    default: echo 'Subscriber';
                  }
                ?>
              </td>
              <td><?= esc($u['EMAIL']) ?></td>
              <td>
                <div class="btn-group">
                  <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
                    Action
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="<?= site_url("user/{$u['ID']}") ?>">View</a>
                    <a class="dropdown-item" href="<?= site_url("user/{$u['ID']}/edit") ?>">Edit</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" 
                       onclick="if(confirm('Delete this user?')){document.getElementById('delForm<?= $u['ID'] ?>').submit()}">
                       Delete
                    </a>
                    <form id="delForm<?= $u['ID'] ?>" method="post" action="<?= site_url("user/{$u['ID']}") ?>" style="display:none">
                      <?= csrf_field() ?>
                      <input type="hidden" name="_method" value="delete">
                    </form>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</section>

<!-- DataTables init for Users -->
<script>
  $(function () {
    $("#userTable").DataTable({
      responsive: true,
      autoWidth: false,
      pageLength: 10,
      lengthMenu: [5, 10, 25, 50],
      language: {
        search: "",
        searchPlaceholder: "Search…"
      }
    });
  });
</script>

<?= view('templates/footer') ?>
