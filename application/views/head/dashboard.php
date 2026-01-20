<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?php echo $data['not_submitted_today']; ?></h3>
                <p>Belum Mengirim Hari Ini</p>
            </div>
            <div class="icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="#" class="small-box-footer">&nbsp;</a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $data['pending_validation']; ?></h3>
                <p>Menunggu Validasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="<?php echo base_url(); ?>head/validation" class="small-box-footer">Lihat Daftar <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
