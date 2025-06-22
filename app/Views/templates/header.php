<?php
//────────────────────────────────────────────────────────────────────────────
// Ensure the layout flags always exist
//────────────────────────────────────────────────────────────────────────────
$hideNavbar  = $hideNavbar  ?? false;
$hideSidebar = $hideSidebar ?? false;

// Build the <body> class list
$bodyClasses = ['hold-transition', 'layout-fixed'];
if ($hideSidebar) {
    $bodyClasses[] = 'sidebar-hidden';
} else {
    $bodyClasses[] = 'sidebar-mini';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Happiness Index System</title>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('assets/plugins/fontawesome-free/css/all.min.css') ?>">

  <!-- All Plugin CSS -->
  <?php
    $pluginDir = FCPATH . 'assets/plugins';
    if (is_dir($pluginDir)) {
      $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pluginDir));
      foreach ($it as $file) {
        if ($file->isFile() && strtolower($file->getExtension()) === 'css') {
          $path = str_replace([FCPATH, '\\'], ['', '/'], $file->getPathname());
          echo '<link rel="stylesheet" href="' . base_url($path) . '">' . "\n";
        }
      }
    }
  ?>

  <!-- AdminLTE & Custom CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/dist/css/adminlte.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/dist/css/styles.css') ?>">
</head>

<body class="<?= implode(' ', $bodyClasses) ?>">
<div class="wrapper">

  <?php
    // Ensure $notifications is defined as an array
  if (! isset($notifications) || ! is_array($notifications)) {
      $notifications = [];
  }

  // Convert any OCILob MESSAGE fields into plain strings
  foreach ($notifications as &$note) {
      if (is_object($note['MESSAGE']) && method_exists($note['MESSAGE'], 'read')) {
          $length = $note['MESSAGE']->size();
          $note['MESSAGE'] = $length > 0
              ? $note['MESSAGE']->read($length)
              : '';
      }
  }
  unset($note);
  ?>

  <!-- NAVBAR (only if not hidden) -->
  <?php if (! $hideNavbar): ?>
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
      <!-- Left links -->
      <ul class="navbar-nav">
        <?php if (! $hideSidebar): ?>
          <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
          </li>
        <?php endif; ?>
        <li class="nav-item d-none d-md-inline"><a href="<?= site_url() ?>" class="nav-link">Home</a></li>
        <li class="nav-item d-none d-md-inline"><a href="<?= site_url('survey') ?>" class="nav-link">Surveys</a></li>
      </ul>

     <!-- Right navbar links -->
<ul class="navbar-nav ml-auto">
  <?php if (session()->get('isLoggedIn')): ?>

<!-- Notifications Dropdown -->
<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-bell"></i>
    <?php if (count($notifications) > 0): ?>
      <span class="badge badge-warning navbar-badge"><?= count($notifications) ?></span>
    <?php endif; ?>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right dropdown-menu-notifs">
    <span class="dropdown-header"><?= count($notifications) ?> Notifications</span>
    <div class="dropdown-divider"></div>
    <?php if (! empty($notifications)): ?>
      <?php foreach ($notifications as $note): ?>
        <a href="#" class="dropdown-item">
          <?= esc($note['MESSAGE']) ?>
          <span class="float-right text-muted text-sm">
            <?= date('H:i', strtotime($note['DATE_CREATED'])) ?>
          </span>
        </a>
        <div class="dropdown-divider"></div>
      <?php endforeach; ?>
    <?php else: ?>
      <span class="dropdown-item text-center text-muted">No new notifications</span>
      <div class="dropdown-divider"></div>
    <?php endif; ?>
    <a href="<?= site_url('notifications') ?>" class="dropdown-item dropdown-footer">
      See All Notifications
    </a>
  </div>
</li>

              <!-- User Dropdown Menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-user"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <!-- Greeting at the top -->
        <div class="dropdown-header text-center font-weight-bold">
          Hello, <?= esc(session('name')) ?>
        </div>
        <div class="dropdown-divider"></div>

        <a href="<?= site_url('profile') ?>" class="dropdown-item">
          <i class="fas fa-id-badge mr-2"></i> Profile
        </a>
        <div class="dropdown-divider"></div>
        <a href="<?= site_url('logout') ?>" class="dropdown-item text-danger">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </li>
  <?php endif; ?>
</ul>
    </nav>
  <?php endif; ?>
  <!-- /.navbar -->

  <!-- SIDEBAR (only if not hidden) -->
  <?php if (! $hideSidebar): ?>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
      <a href="<?= site_url() ?>" class="brand-link">
        <span class="brand-text font-weight-light">Happiness Index</span>
      </a>
      <div class="sidebar">
        <nav class="mt-2">
          <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
            <?php if (session('role') === 'admin'): ?>
  <!-- Admin menu -->
  <li class="nav-item"><a href="<?= site_url('user') ?>"      class="nav-link"><i class="nav-icon fas fa-user"></i><p>Users</p></a></li>
  <li class="nav-item"><a href="<?= site_url('survey') ?>"    class="nav-link"><i class="nav-icon fas fa-list-alt"></i><p>Surveys</p></a></li>
  <li class="nav-item"><a href="<?= site_url('question') ?>"  class="nav-link"><i class="nav-icon fas fa-question"></i><p>Questions</p></a></li>
  <li class="nav-item"><a href="<?= site_url('answer') ?>"    class="nav-link"><i class="nav-icon fas fa-reply"></i><p>Answers</p></a></li>
<?php else: ?>
  <!-- Regular user menu -->
   <li class="nav-item">
    <a href="<?= site_url('profile') ?>" class="nav-link">
      <i class="nav-icon fas fa-id-badge"></i>
      <p>My Profile</p>
    </a>
  </li>
  <li class="nav-item">
    <a href="<?= site_url('survey') ?>" class="nav-link">
      <i class="nav-icon fas fa-list-alt"></i>
      <p>My Surveys</p>
    </a>
  </li>
<?php endif; ?>

<!-- Always include logout -->
<li class="nav-item">
  <a href="<?= site_url('logout') ?>" class="nav-link">
    <i class="nav-icon fas fa-sign-out-alt"></i>
    <p>Logout</p>
  </a>
</li>
          </ul>
        </nav>
      </div>
    </aside>
  <?php endif; ?>
  <!-- /.sidebar -->

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        <?php endif; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
