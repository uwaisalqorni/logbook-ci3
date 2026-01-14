<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Riwayat Logbook</h3>
        <div class="card-tools">
            <form action="" method="GET" class="form-inline">
                <div class="form-group mr-2">
                    <label for="start_date" class="mr-2">Dari:</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($data['start_date'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group mr-2">
                    <label for="end_date" class="mr-2">Sampai:</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($data['end_date'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['logbooks'] as $logbook): ?>
                <tr>
                    <td><?php echo date('d F Y', strtotime($logbook['date'])); ?></td>
                    <td>
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
                    <td>
                        <a href="<?php echo base_url(); ?>employee/logbook?date=<?php echo $logbook['date']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i> <?php echo ($logbook['status'] == 'approved' || $logbook['status'] == 'rejected') ? 'Lihat' : 'Lihat / Edit'; ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    </div>
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <?php if ($data['page'] > 1): ?>
                <li class="page-item"><a class="page-link" href="<?php echo base_url(); ?>employee/history?page=<?php echo $data['page'] - 1; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>">&laquo;</a></li>
            <?php endif; ?>
            
            <?php for($i = 1; $i <= $data['total_pages']; $i++): ?>
                <li class="page-item <?php echo ($data['page'] == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo base_url(); ?>employee/history?page=<?php echo $i; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($data['page'] < $data['total_pages']): ?>
                <li class="page-item"><a class="page-link" href="<?php echo base_url(); ?>employee/history?page=<?php echo $data['page'] + 1; ?>&start_date=<?php echo $data['start_date']; ?>&end_date=<?php echo $data['end_date']; ?>">&raquo;</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
