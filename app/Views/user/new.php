<?= view('templates/header') ?>

<!-- Content Header -->
<section class="content-header">
  <div class="container-fluid">
    <h1>Add New User</h1>
  </div>
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <div class="card card-primary">
      <div class="card-header">
        <h3 class="card-title">Personal Information & Credentials</h3>
      </div>
      <form action="<?= site_url('user') ?>" method="post">
        <?= csrf_field() ?>
        <div class="card-body">
          <div class="row">
            
            <!-- Left Column -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" class="form-control" id="firstname" name="firstname" required>
              </div>
              <div class="form-group">
                <label for="middlename">Middle Name</label>
                <input type="text" class="form-control" id="middlename" name="middlename">
              </div>
              <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" class="form-control" id="lastname" name="lastname" required>
              </div>
              <div class="form-group">
                <label for="contact">Contact #</label>
                <input type="text" class="form-control" id="contact" name="contact">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <textarea class="form-control" id="address" name="address" rows="3"></textarea>
              </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-6">
              <div class="form-group">
                <label for="type">User Role</label>
                <select class="form-control" id="type" name="type">
                  <option value="1">Admin</option>
                  <option value="2">Staff</option>
                  <option value="3" selected>Subscriber</option>
                </select>
              </div>
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
              </div>
              <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
              </div>
            </div>
          
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </form>
    </div>

  </div>
</section>

<?= view('templates/footer') ?>
