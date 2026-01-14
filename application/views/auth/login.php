<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logbook RSIG | Login</title>

  <!-- Google Font: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/adminLTE/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
  <!-- Modern CSS -->
  <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/modern.css?v=<?php echo time(); ?>">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <img src="<?php echo base_url(); ?>assets/img/logo.png" alt="RSIG Logo">
    <a href="#"><b>Logbook</b>RSIG</a>
  </div>
  
  <p class="login-box-msg">Sign in to start your session</p>

  <?php echo form_open('auth/login'); ?>
    <div class="input-group mb-3">
      <input type="text" name="username" class="form-control <?php echo (!empty($data['username_err'])) ? 'is-invalid' : ''; ?>" placeholder="Username" value="<?php echo htmlspecialchars($data['username'], ENT_QUOTES, 'UTF-8'); ?>">
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-user"></span>
        </div>
      </div>
      <span class="invalid-feedback"><?php echo $data['username_err']; ?></span>
    </div>
    <div class="input-group mb-3">
      <input type="password" name="password" class="form-control <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''; ?>" placeholder="Password" value="<?php echo htmlspecialchars($data['password'], ENT_QUOTES, 'UTF-8'); ?>">
      <div class="input-group-append">
        <div class="input-group-text">
          <span class="fas fa-lock"></span>
        </div>
      </div>
      <span class="invalid-feedback"><?php echo $data['password_err']; ?></span>
    </div>
    <div class="row align-items-center">
      <div class="col-8">
        <div class="icheck-primary">
          <input type="checkbox" id="remember">
          <label for="remember">
            Remember Me
          </label>
        </div>
      </div>
      <!-- /.col -->
      <div class="col-4">
        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
      </div>
      <!-- /.col -->
    </div>
  </form>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo base_url(); ?>assets/adminLTE/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url(); ?>assets/adminLTE/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url(); ?>assets/adminLTE/dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="<?php echo base_url(); ?>assets/adminLTE/plugins/sweetalert2/sweetalert2.min.js"></script>
</body>
</html>
