<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row">
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?php echo $data['total_units']; ?></h3>
                <p>Unit Binaan</p>
            </div>
            <div class="icon">
                <i class="fas fa-hospital"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?php echo $data['pending_count']; ?></h3>
                <p>Menunggu Validasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="<?php echo base_url('kabid/validation'); ?>" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?php echo $data['history_count']; ?></h3>
                <p>Sudah Divalidasi</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="<?php echo base_url('kabid/validation'); ?>" class="small-box-footer">Lihat Riwayat <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
