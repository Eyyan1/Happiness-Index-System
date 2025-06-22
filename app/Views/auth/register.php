<?= view('templates/header', ['hideNavbar' => true]) ?>

<div class="container mt-5" style="max-width: 700px;">
  <h3 class="mb-4">Register New Account</h3>

  <?php if ($errors = session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
          <li><?= esc($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?= site_url('register') ?>" method="post">
    <?= csrf_field() ?>

    <!-- Name & Credentials -->
    <div class="row mb-4">
      <div class="col-md-6 mb-3">
        <label for="firstname" class="form-label">First Name</label>
        <input
          type="text"
          id="firstname"
          name="firstname"
          class="form-control"
          value="<?= old('firstname') ?>"
          required
        >
      </div>
      <div class="col-md-6 mb-3">
        <label for="lastname" class="form-label">Last Name</label>
        <input
          type="text"
          id="lastname"
          name="lastname"
          class="form-control"
          value="<?= old('lastname') ?>"
          required
        >
      </div>
      <div class="col-md-6 mb-3">
        <label for="email" class="form-label">Email (username)</label>
        <input
          type="email"
          id="email"
          name="email"
          class="form-control"
          value="<?= old('email') ?>"
          required
        >
      </div>
      <div class="col-md-3 mb-3">
        <label for="password" class="form-label">Password</label>
        <input
          type="password"
          id="password"
          name="password"
          class="form-control"
          required
        >
      </div>
      <div class="col-md-3 mb-3">
        <label for="pass_confirm" class="form-label">Confirm</label>
        <input
          type="password"
          id="pass_confirm"
          name="pass_confirm"
          class="form-control"
          required
        >
      </div>
    </div>

    <!-- Optional Contact Info -->
    <div class="row mb-4">
      <div class="col-md-6 mb-3">
        <label for="middlename" class="form-label">Middle Name <small class="text-muted">(optional)</small></label>
        <input
          type="text"
          id="middlename"
          name="middlename"
          class="form-control"
          value="<?= old('middlename') ?>"
          placeholder="e.g. Binti / Bin"
        >
      </div>
      <div class="col-md-6 mb-3">
        <label for="contact" class="form-label">Contact Number <small class="text-muted">(optional)</small></label>
        <input
          type="text"
          id="contact"
          name="contact"
          class="form-control"
          value="<?= old('contact') ?>"
          placeholder="e.g. 010-1234567"
        >
      </div>
      <div class="col-12 mb-3">
        <label for="address" class="form-label">Address <small class="text-muted">(optional)</small></label>
        <textarea
          name="address"
          id="address"
          class="form-control"
          rows="3"
          placeholder="Your address…"
        ><?= old('address') ?></textarea>
      </div>
    </div>

    <!-- Demographics -->
    <?php
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
          'Support (Gred 19–28)',
          'Support (Gred 29–38)',
          'Professional (Gred 41–54)',
          'Academic DV41','Academic DG41–48',
          'Academic DS45','Academic DS52–54',
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
      <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
          <?= ucwords(str_replace('_',' ', $field)) ?>
        </div>
        <div class="card-body">
          <?php foreach ($options as $opt): ?>
            <div class="form-check">
              <input
                class="form-check-input"
                type="radio"
                name="<?= $field ?>"
                id="<?= $field . '_' . md5($opt) ?>"
                value="<?= esc($opt) ?>"
                <?= old($field)==$opt ? 'checked' : '' ?>
                required
              >
              <label
                class="form-check-label"
                for="<?= $field . '_' . md5($opt) ?>"
              ><?= esc($opt) ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-success w-100">Register</button>
    <p class="mt-3 text-center">
      Already registered? <a href="<?= site_url('login') ?>">Login here</a>
    </p>
  </form>
</div>

<?= view('templates/footer') ?>
