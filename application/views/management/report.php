<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Logbook Pegawai</h3>
    </div>
    <div class="card-body">
        <form action="" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control">
                            <option value="">Semua Unit</option>
                            <?php foreach ($data['units'] as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>" <?php echo ($data['unit_id'] == $unit['id']) ? 'selected' : ''; ?>>
                                    <?php echo $unit['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
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
            <a href="<?php echo base_url(); ?>management/export?type=excel&start_date=<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>&end_date=<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>&unit_id=<?php echo htmlspecialchars($data['unit_id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-success" target="_blank">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="<?php echo base_url(); ?>management/export?type=pdf&start_date=<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>&end_date=<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>&unit_id=<?php echo htmlspecialchars($data['unit_id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger" target="_blank">
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
                        <th>Unit</th>
                        <th>Waktu</th>
                        <th>Kegiatan</th>
                        <th>Output</th>
                        <th>Kendala</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data['logbooks'])): ?>
                        <tr><td colspan="9" class="text-center">Tidak ada data logbook.</td></tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($data['logbooks'] as $logbook): ?>
                            <?php if (empty($logbook['activities'])): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($logbook['date'])); ?></td>
                                    <td><?php echo htmlspecialchars($logbook['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($logbook['unit_name']); ?></td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td>-</td>
                                    <td><span class="badge badge-<?php echo ($logbook['status'] == 'approved') ? 'success' : (($logbook['status'] == 'rejected') ? 'danger' : 'secondary'); ?>"><?php echo ucfirst($logbook['status']); ?></span></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logbook['activities'] as $activity): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($logbook['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($logbook['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($logbook['unit_name']); ?></td>
                                        <td><?php echo date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])); ?></td>
                                        <td><?php echo htmlspecialchars($activity['description']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['output']); ?></td>
                                        <td><?php echo htmlspecialchars($activity['kendala']); ?></td>
                                        <td><span class="badge badge-<?php echo ($logbook['status'] == 'approved') ? 'success' : (($logbook['status'] == 'rejected') ? 'danger' : 'secondary'); ?>"><?php echo ucfirst($logbook['status']); ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <?php if ($data['page'] > 1): ?>
                <li class="page-item"><a class="page-link" href="<?php echo base_url(); ?>management/report?page=<?php echo $data['page'] - 1; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>&unit_id=<?php echo $data['unit_id']; ?>">&laquo;</a></li>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                <li class="page-item <?php echo ($data['page'] == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo base_url(); ?>management/report?page=<?php echo $i; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>&unit_id=<?php echo $data['unit_id']; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($data['page'] < $data['total_pages']): ?>
                <li class="page-item"><a class="page-link" href="<?php echo base_url(); ?>management/report?page=<?php echo $data['page'] + 1; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>&unit_id=<?php echo $data['unit_id']; ?>">&raquo;</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
