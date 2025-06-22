<?= view('templates/header') ?>

<div class="content-header d-flex justify-content-between align-items-center">
  <h1 class="m-0">Edit User</h1>
  <div>
    <a href="<?= site_url('user/' . $user['ID']) ?>" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back
    </a>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <form action="<?= site_url('user/update/' . $user['ID']) ?>" method="post">
      <?= csrf_field() ?>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="firstname">First Name</label>
          <input type="text" name="firstname" id="firstname" class="form-control"
                 value="<?= esc(old('firstname', $user['FIRSTNAME'])) ?>" required>
        </div>
        <div class="form-group col-md-6">
          <label for="lastname">Last Name</label>
          <input type="text" name="lastname" id="lastname" class="form-control"
                 value="<?= esc(old('lastname', $user['LASTNAME'])) ?>" required>
        </div>
      </div>

      <div class="form-group">
        <label for="email">Email (username)</label>
        <input type="email" id="email" class="form-control"
               value="<?= esc($user['EMAIL']) ?>" readonly>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="contact">Contact</label>
          <input type="text" name="contact" id="contact" class="form-control"
                 value="<?= esc(old('contact', $user['CONTACT'])) ?>">
        </div>
        <div class="form-group col-md-6">
          <label for="address">Address</label>
          <textarea name="address" id="address" class="form-control" rows="2"><?= esc(old('address', $user['ADDRESS'])) ?></textarea>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="password">New Password <small>(leave blank to keep)</small></label>
          <input type="password" name="password" id="password" class="form-control">
        </div>
        <div class="form-group col-md-6">
          <label for="pass_confirm">Confirm Password</label>
          <input type="password" name="pass_confirm" id="pass_confirm" class="form-control">
        </div>
      </div>

      <?php
        // re-use your demographics array to generate each radio group
        $demographics = [
          'age_group'        => ['20 & under','21–30','31–40','41–50','51 & above'],
          'gender'           => ['Male','Female','Other'],
          'religion'         => ['Islam','Hindu','Buddha','Christian','Other'],
          'ethnicity'        => ['Melayu','Chinese','Indian','Other'],
          'marital_status'   => ['Single','Married','Divorced (living)','Widowed'],
          'children_count'   => ['None','1–2','3–5','6 & above'],
          'education_level'  => ['PMR','SPM/STPM','Diploma','Bachelors','Master','PhD'],
          'job_band'         => [
            'Support (Gred 11/14/16)',
            'Support (Gred 19-28)',
            'Support (Gred 29-38)',
            'Professional (Gred 41-54)',
            'Academic DV41','Academic DG41-48',
            'Academic DS45','Academic DS52-54',
            'Academic VK7/6/5'
          ],
          'service_duration' => ['< 5 years','6–10 years','11–20 years','21–30 years','> 30 years'],
          'salary_range'     => [
            '< RM2,500','RM2,500–3,170','3,171–3,970','3,971–4,850',
            '4,851–5,880','5,881–7,100','7,101–8,700','8,701–10,970',
            '10,971–15,040','> RM15,041'
          ],
          'household_income'=> [
            '< RM2,500','RM2,500–3,170','3,171–3,970','3,971–4,850',
            '4,851–5,880','5,881–7,100','7,101–8,700','8,701–10,970',
            '10,971–15,040','> RM15,041'
          ],
        ];
      ?>

      <?php foreach ($demographics as $field => $options): ?>
      <div class="card mb-3">
        <div class="card-header text-uppercase small">
          <?= ucwords(str_replace('_',' ', $field)) ?>
        </div>
        <div class="card-body">
          <?php foreach ($options as $opt): ?>
          <div class="form-check form-check-inline">
            <input class="form-check-input"
                   type="radio"
                   name="<?= $field ?>"
                   id="<?= $field . '_' . md5($opt) ?>"
                   value="<?= esc($opt) ?>"
                   <?= old($field, $user[strtoupper($field)]) === $opt ? 'checked' : '' ?>
            >
            <label class="form-check-label" for="<?= $field . '_' . md5($opt) ?>">
              <?= esc($opt) ?>
            </label>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <button type="submit" class="btn btn-success">Save Changes</button>
    </form>
  </div>
</div>

<?= view('templates/footer') ?>
