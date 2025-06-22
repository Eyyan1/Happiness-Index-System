<?= view('templates/header') ?>

<div class="content-header d-flex justify-content-between align-items-center">
  <h1 class="m-0">View User</h1>
  <div>
    <a href="<?= site_url('user/' . $user['ID'] . '/edit') ?>" class="btn btn-primary">
      <i class="fas fa-edit"></i> Edit
    </a>
    <a href="<?= site_url('user') ?>" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to List
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <dl class="row">
      <dt class="col-sm-3">First Name</dt>
      <dd class="col-sm-9"><?= esc($user['FIRSTNAME']) ?></dd>

      <dt class="col-sm-3">Last Name</dt>
      <dd class="col-sm-9"><?= esc($user['LASTNAME']) ?></dd>

      <dt class="col-sm-3">Email</dt>
      <dd class="col-sm-9"><?= esc($user['EMAIL']) ?></dd>

      <dt class="col-sm-3">Role</dt>
      <dd class="col-sm-9">
        <?= $user['TYPE'] == '1' ? 'Admin' : 'User' ?>
      </dd>

      <dt class="col-sm-3">Registered On</dt>
      <dd class="col-sm-9">
        <?= date('M d, Y', strtotime($user['DATE_CREATED'])) ?>
      </dd>

      <dt class="col-sm-3">Contact</dt>
      <dd class="col-sm-9"><?= esc($user['CONTACT'] ?? '—') ?></dd>

      <dt class="col-sm-3">Address</dt>
      <dd class="col-sm-9"><?= nl2br(esc($user['ADDRESS'] ?? '—')) ?></dd>

      <dt class="col-sm-3">Age Group</dt>
      <dd class="col-sm-9"><?= esc($user['AGE_GROUP'] ?? '—') ?></dd>

      <dt class="col-sm-3">Gender</dt>
      <dd class="col-sm-9"><?= esc($user['GENDER'] ?? '—') ?></dd>

      <dt class="col-sm-3">Religion</dt>
      <dd class="col-sm-9"><?= esc($user['RELIGION'] ?? '—') ?></dd>

      <dt class="col-sm-3">Ethnicity</dt>
      <dd class="col-sm-9"><?= esc($user['ETHNICITY'] ?? '—') ?></dd>

      <dt class="col-sm-3">Marital Status</dt>
      <dd class="col-sm-9"><?= esc($user['MARITAL_STATUS'] ?? '—') ?></dd>

      <dt class="col-sm-3">Children Count</dt>
      <dd class="col-sm-9"><?= esc($user['CHILDREN_COUNT'] ?? '—') ?></dd>

      <dt class="col-sm-3">Education Level</dt>
      <dd class="col-sm-9"><?= esc($user['EDUCATION_LEVEL'] ?? '—') ?></dd>

      <dt class="col-sm-3">Job Band</dt>
      <dd class="col-sm-9"><?= esc($user['JOB_BAND'] ?? '—') ?></dd>

      <dt class="col-sm-3">Service Duration</dt>
      <dd class="col-sm-9"><?= esc($user['SERVICE_DURATION'] ?? '—') ?></dd>

      <dt class="col-sm-3">Salary Range</dt>
      <dd class="col-sm-9"><?= esc($user['SALARY_RANGE'] ?? '—') ?></dd>

      <dt class="col-sm-3">Household Income</dt>
      <dd class="col-sm-9"><?= esc($user['HOUSEHOLD_INCOME'] ?? '—') ?></dd>
    </dl>
  </div>
</div>

<?= view('templates/footer') ?>
