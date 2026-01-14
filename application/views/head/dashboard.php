<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>Validasi</h3>
                <p>Logbook Pending</p>
            </div>
            <div class="icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="<?php echo base_url(); ?>head/validation" class="small-box-footer">Lihat Daftar <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
