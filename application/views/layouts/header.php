<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logbook RSIG | <?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Google Font: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Modern CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/modern.css?v=<?php echo time(); ?>">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="<?php echo base_url(); ?>auth/logout" role="button">
          <i class="fas fa-sign-out-alt"></i> Logout
        </a>
      </li>
    </ul>
  </nav>

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
      <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="RSIG Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Logbook RSIG</span>
    </a>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo base_url(); ?>assets/adminLTE/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['user_name']; ?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <?php if ($_SESSION['role'] == 'admin'): ?>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>admin/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>admin/units" class="nav-link">
              <i class="nav-icon fas fa-hospital"></i>
              <p>Master Unit</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>admin/activity_types" class="nav-link">
              <i class="nav-icon fas fa-list"></i>
              <p>Jenis Kegiatan</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>admin/users" class="nav-link">
              <i class="nav-icon fas fa-users"></i>
              <p>Master Pegawai</p>
            </a>
          </li>
          <?php endif; ?>

          <?php if ($_SESSION['role'] == 'management'): ?>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>management/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>management/report" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Laporan Logbook</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>management/charts" class="nav-link">
              <i class="nav-icon fas fa-chart-pie"></i>
              <p>Laporan Grafik</p>
            </a>
          </li>
          <?php endif; ?>

          <?php if ($_SESSION['role'] == 'employee'): ?>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>employee/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>employee/logbook" class="nav-link">
              <i class="nav-icon fas fa-edit"></i>
              <p>Input Logbook</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>employee/history" class="nav-link">
              <i class="nav-icon fas fa-history"></i>
              <p>Riwayat Logbook</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>employee/report" class="nav-link">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Laporan</p>
            </a>
          </li>
          <?php endif; ?>

          <?php if ($_SESSION['role'] == 'head'): ?>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>head/dashboard" class="nav-link">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo base_url(); ?>head/validation" class="nav-link">
              <i class="nav-icon fas fa-check-circle"></i>
              <p>Validasi Logbook</p>
            </a>
          </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1><?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
          </div>
        </div>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
