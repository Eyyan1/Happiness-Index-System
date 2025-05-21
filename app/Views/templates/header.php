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

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <?php if (!isset($hideNavbar)): ?>
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li class="nav-item d-none d-md-inline">
        <a href="<?= site_url() ?>" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-md-inline">
        <a href="<?= site_url('survey') ?>" class="nav-link">Surveys</a>
      </li>
    </ul>
  </nav>
  <?php endif; ?>

  <!-- Sidebar -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= site_url() ?>" class="brand-link">
      <span class="brand-text font-weight-light">Happiness Index</span>
    </a>
    <div class="sidebar">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
          <li class="nav-item"><a href="<?= site_url('user') ?>" class="nav-link"><i class="nav-icon fas fa-user"></i><p>Users</p></a></li>
          <li class="nav-item"><a href="<?= site_url('survey') ?>" class="nav-link"><i class="nav-icon fas fa-list-alt"></i><p>Surveys</p></a></li>
          <li class="nav-item"><a href="<?= site_url('question') ?>" class="nav-link"><i class="nav-icon fas fa-question"></i><p>Questions</p></a></li>
          <li class="nav-item"><a href="<?= site_url('answer') ?>" class="nav-link"><i class="nav-icon fas fa-reply"></i><p>Answers</p></a></li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Main Content Wrapper -->
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
