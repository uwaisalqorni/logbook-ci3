<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo base_url(); ?>employee/report">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        <a href="<?php echo base_url(); ?>employee/export?start_date=<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>&end_date=<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Logbook</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th>Kegiatan</th>
                    <th>Output</th>
                    <th>Kendala</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['logbooks'])): ?>
                    <tr><td colspan="6" class="text-center">Tidak ada data logbook pada periode ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['logbooks'] as $logbook): ?>
                        <?php if (empty($logbook['activities'])): ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($logbook['date'])); ?></td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td><span class="badge badge-secondary"><?php echo ucfirst($logbook['status']); ?></span></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($logbook['activities'] as $index => $activity): ?>
                            <tr>
                                <?php if ($index === 0): ?>
                                    <td rowspan="<?php echo count($logbook['activities']); ?>"><?php echo date('d M Y', strtotime($logbook['date'])); ?></td>
                                <?php endif; ?>
                                <td><?php echo date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])); ?></td>
                                <td><?php echo htmlspecialchars($activity['activity_name']); ?><br><small><?php echo nl2br(htmlspecialchars($activity['description'])); ?></small></td>
                                <td><?php echo htmlspecialchars($activity['output']); ?></td>
                                <td><?php echo htmlspecialchars($activity['kendala']); ?></td>
                                <?php if ($index === 0): ?>
                                    <td rowspan="<?php echo count($logbook['activities']); ?>">
                                        <?php 
                                        $status = $logbook['status'];
                                        $badgeClass = 'secondary';
                                        if ($status == 'submitted') $badgeClass = 'info';
                                        if ($status == 'approved') $badgeClass = 'success';
                                        if ($status == 'rejected') $badgeClass = 'danger';
                                        if ($status == 'revision') $badgeClass = 'warning';
                                        ?>
                                        <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
