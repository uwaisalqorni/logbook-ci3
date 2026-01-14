<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo htmlspecialchars($data['total_users'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p>Total Pegawai</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="<?php echo base_url(); ?>admin/users" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo htmlspecialchars($data['total_units'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p>Total Unit</p>
            </div>
            <div class="icon">
                <i class="fas fa-hospital"></i>
            </div>
            <a href="<?php echo base_url(); ?>admin/units" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $data['today_logbooks']; ?></h3>
                <p>Logbook Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-book"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
