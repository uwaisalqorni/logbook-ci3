<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Efektifitas Jam Kerja</h3>
    </div>
    <div class="card-body">
        <form action="" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mb-3">
            <a href="<?php echo base_url(); ?>head/export_effectiveness?type=excel&start_date=<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>&end_date=<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success" target="_blank">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?php echo base_url(); ?>head/export_effectiveness?type=pdf&start_date=<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>&end_date=<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pegawai</th>
                        <th>NIK</th>
                        <th>Total Jam Kerja (Menit)</th>
                        <th>Total Jam Kerja (Jam)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['effectiveness_data'])): ?>
                        <tr><td colspan="6" class="text-center">Tidak ada data.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data['effectiveness_data'] as $item): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['date'])); ?></td>
                                <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($item['nik']); ?></td>
                                <td><?php echo $item['total_minutes']; ?> Menit</td>
                                <td><?php echo round($item['total_minutes'] / 60, 2); ?> Jam</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
