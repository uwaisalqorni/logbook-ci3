<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<?php if ($data['message']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $data['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row mb-3">
    <div class="col-md-6">
        <form method="GET" action="<?php echo base_url(); ?>head/logbook" class="form-inline">
            <label class="mr-2">Pilih Tanggal:</label>
            <input type="date" name="date" class="form-control mr-2" value="<?php echo htmlspecialchars($data['date'], ENT_QUOTES, 'UTF-8'); ?>" onchange="this.form.submit()">
        </form>
    </div>
    <div class="col-md-6 text-right">
        <h5>Status: 
            <?php 
            $status = $data['logbook']['status'] ?? 'draft';
            $badgeClass = 'secondary';
            if ($status == 'submitted') $badgeClass = 'info';
            if ($status == 'approved') $badgeClass = 'success';
            if ($status == 'rejected') $badgeClass = 'danger';
            if ($status == 'revision') $badgeClass = 'warning';
            ?>
            <span class="badge badge-<?php echo $badgeClass; ?>"><?php echo ucfirst($status); ?></span>
        </h5>
    </div>
</div>

<!-- Add/Edit Activity Form -->
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title"><?php echo !empty($data['edit_data']) ? 'Edit Kegiatan' : 'Tambah Kegiatan'; ?></h3>
    </div>
    <div class="card-body">
        <?php 
        $is_final = ($data['logbook']['status'] ?? 'draft') == 'approved' || ($data['logbook']['status'] ?? 'draft') == 'rejected';
        ?>
        <?php if ($is_final): ?>
            <div class="alert alert-warning">Logbook ini sudah difinalisasi (<?php echo $data['logbook']['status']; ?>) dan tidak dapat diedit.</div>
        <?php endif; ?>

        <?php if (($data['logbook']['status'] ?? '') == 'revision'): ?>
            <div class="alert alert-warning">
                <h5><i class="icon fas fa-exclamation-triangle"></i> Perlu Revisi!</h5>
                Catatan: <?php echo htmlspecialchars($data['validation']['notes'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php echo form_open(''); ?>
            <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
            <?php if (!empty($data['edit_data'])): ?>
                <input type="hidden" name="action" value="update_activity">
                <input type="hidden" name="detail_id" value="<?php echo $data['edit_data']['id']; ?>">
            <?php else: ?>
                <input type="hidden" name="action" value="add_activity">
            <?php endif; ?>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Jam Mulai</label>
                        <input type="time" name="start_time" class="form-control" required value="<?php echo $data['edit_data']['start_time'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Jam Selesai</label>
                        <input type="time" name="end_time" class="form-control" required value="<?php echo $data['edit_data']['end_time'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Kegiatan</label>
                        <select name="activity_type_id" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($data['activity_types'] as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo (isset($data['edit_data']['activity_type_id']) && $data['edit_data']['activity_type_id'] == $type['id']) ? 'selected' : ''; ?>><?php echo $type['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Uraian Kegiatan</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($data['edit_data']['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Output / Jumlah Pasien</label>
                        <input type="text" name="output" class="form-control" value="<?php echo htmlspecialchars($data['edit_data']['output'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kendala (Jika ada)</label>
                        <input type="text" name="kendala" class="form-control" value="<?php echo htmlspecialchars($data['edit_data']['kendala'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" <?php echo $is_final ? 'disabled' : ''; ?>><?php echo !empty($data['edit_data']) ? 'Update Kegiatan' : 'Tambah Kegiatan'; ?></button>
            <?php if (!empty($data['edit_data'])): ?>
                <a href="<?php echo base_url(); ?>head/logbook?date=<?php echo $data['date']; ?>" class="btn btn-secondary">Batal</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Activities Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kegiatan Hari Ini</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Jenis</th>
                    <th>Uraian</th>
                    <th>Output</th>
                    <th>Kendala</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['activities'])): ?>
                    <tr><td colspan="6" class="text-center">Belum ada kegiatan.</td></tr>
                <?php else: ?>
                    <?php foreach ($data['activities'] as $activity): ?>
                    <tr>
                        <td><?php echo date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])); ?></td>
                        <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($activity['description'])); ?></td>
                        <td><?php echo htmlspecialchars($activity['output']); ?></td>
                        <td><?php echo htmlspecialchars($activity['kendala']); ?></td>
                        <td>
                            <?php if (!$is_final): ?>
                            <a href="<?php echo base_url(); ?>head/logbook?date=<?php echo $data['date']; ?>&edit_id=<?php echo $activity['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <?php echo form_open('', ['style' => 'display:inline;', 'onsubmit' => "return confirm('Hapus kegiatan ini?');"]); ?>
                                <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
                                <input type="hidden" name="action" value="delete_activity">
                                <input type="hidden" name="detail_id" value="<?php echo $activity['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted">Locked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <?php if (($data['logbook']['status'] ?? 'draft') != 'submitted' && ($data['logbook']['status'] ?? 'draft') != 'approved'): ?>
            <?php echo form_open('', ['onsubmit' => "return confirm('Kirim logbook ke Kabid? Data tidak bisa diubah setelah dikirim.');"]); ?>
                <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
                <input type="hidden" name="action" value="submit_logbook">
                <button type="submit" class="btn btn-success btn-block">Kirim Logbook</button>
            </form>
        <?php else: ?>
            <?php if (($data['logbook']['status'] ?? '') == 'revision'): ?>
                <?php echo form_open('', ['onsubmit' => "return confirm('Kirim ulang logbook ke Kabid?');"]); ?>
                    <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
                    <input type="hidden" name="action" value="submit_logbook">
                    <button type="submit" class="btn btn-warning btn-block">Kirim Ulang Logbook</button>
                </form>
            <?php else: ?>
                <div class="alert alert-info text-center m-0">Logbook sudah dikirim / disetujui.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
