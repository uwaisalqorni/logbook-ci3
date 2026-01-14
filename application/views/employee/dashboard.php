<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo htmlspecialchars($data['total_logbooks'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p>Total Logbook Diinput</p>
            </div>
            <div class="icon">
                <i class="fas fa-edit"></i>
            </div>
            <a href="<?php echo base_url(); ?>employee/logbook" class="small-box-footer">Isi Logbook <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Riwayat</h3>
                <p>Logbook Saya</p>
            </div>
            <div class="icon">
                <i class="fas fa-history"></i>
            </div>
            <a href="<?php echo base_url(); ?>employee/history" class="small-box-footer">Lihat Riwayat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
