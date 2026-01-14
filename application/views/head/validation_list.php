<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card card-warning">
    <div class="card-header">
        <h3 class="card-title">Daftar Logbook Menunggu Validasi</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Pegawai</th>
                    <th>NIK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['pending_logbooks'])): ?>
                    <tr><td colspan="4" class="text-center">Tidak ada logbook pending.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['pending_logbooks'] as $logbook): ?>
                    <tr>
                        <td><?php echo date('d F Y', strtotime($logbook['date'])); ?></td>
                        <td><?php echo htmlspecialchars($logbook['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($logbook['nik']); ?></td>
                        <td>
                            <a href="<?php echo base_url(); ?>head/detail/<?php echo $logbook['id']; ?>" class="btn btn-sm btn-primary">Validasi</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card card-secondary">
    <div class="card-header">
        <h3 class="card-title">Riwayat Validasi</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Pegawai</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['history_logbooks'])): ?>
                    <tr><td colspan="4" class="text-center">Belum ada riwayat validasi.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['history_logbooks'] as $logbook): ?>
                    <tr>
                        <td><?php echo date('d F Y', strtotime($logbook['date'])); ?></td>
                        <td><?php echo htmlspecialchars($logbook['user_name']); ?></td>
                        <td>
                            <?php 
                            $status = $logbook['status'];
                            $badgeClass = 'secondary';
                            if ($status == 'approved') $badgeClass = 'success';
                            if ($status == 'rejected') $badgeClass = 'danger';
                            if ($status == 'revision') $badgeClass = 'warning';
                            ?>
                            <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo base_url(); ?>head/detail/<?php echo $logbook['id']; ?>" class="btn btn-sm btn-info">Lihat</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
