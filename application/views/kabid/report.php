<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Filter Laporan</h3>
    </div>
    <div class="card-body">
        <form action="" method="get">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo $data['start_date']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="<?php echo $data['end_date']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control">
                            <option value="">Semua Unit</option>
                            <?php foreach ($data['units'] as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>" <?php echo ($data['selected_unit'] == $unit['id']) ? 'selected' : ''; ?>>
                                    <?php echo $unit['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control">
                            <option value="">Semua Role</option>
                            <option value="head" <?php echo ($data['selected_role'] == 'head') ? 'selected' : ''; ?>>Head</option>
                            <option value="employee" <?php echo ($data['selected_role'] == 'employee') ? 'selected' : ''; ?>>Employee</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <button type="submit" name="filter" value="true" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($data['logbooks'])): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Hasil Laporan</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-bordered text-nowrap">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Unit</th>
                    <th>Uraian Kegiatan</th>
                    <th>Output</th>
                    <th>Kendala</th>
                    <th>Status Logbook</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['logbooks'] as $logbook): ?>
                <tr>
                    <td><?php echo date('d-m-Y', strtotime($logbook['date'])); ?></td>
                    <td>
                        <?php echo htmlspecialchars($logbook['user_name']); ?><br>
                        <small class="text-muted"><?php echo htmlspecialchars($logbook['nik']); ?></small>
                    </td>
                    <td><?php echo ucfirst($logbook['role']); ?></td>
                    <td><?php echo htmlspecialchars($logbook['unit_name']); ?></td>
                    <td>
                        <?php if ($logbook['description']): ?>
                            <strong><?php echo htmlspecialchars($logbook['activity_name']); ?></strong><br>
                            <?php echo nl2br(htmlspecialchars($logbook['description'])); ?>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($logbook['output'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($logbook['kendala'] ?: '-'); ?></td>
                    <td>
                        <?php 
                        $badges = [
                            'draft' => 'secondary',
                            'submitted' => 'warning',
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'revision' => 'warning'
                        ];
                        $badge = isset($badges[$logbook['logbook_status']]) ? $badges[$logbook['logbook_status']] : 'secondary';
                        ?>
                        <span class="badge badge-<?php echo $badge; ?>"><?php echo ucfirst($logbook['logbook_status']); ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php elseif ($this->input->get('filter')): ?>
<div class="alert alert-info">
    Tidak ada data logbook ditemukan untuk filter yang dipilih.
</div>
<?php endif; ?>

<?php $this->load->view('layouts/footer'); ?>
