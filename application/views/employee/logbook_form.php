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
        <form method="GET" action="<?php echo base_url(); ?>employee/logbook" class="form-inline">
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
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="small text-muted">Jam Mulai</label>
                        <div class="input-group date" id="start_time_picker" data-target-input="nearest">
                            <input type="text" name="start_time" class="form-control form-control-sm datetimepicker-input" data-target="#start_time_picker" required value="<?php echo $data['edit_data']['start_time'] ?? ''; ?>"/>
                            <div class="input-group-append" data-target="#start_time_picker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="small text-muted">Jam Selesai</label>
                        <div class="input-group date" id="end_time_picker" data-target-input="nearest">
                            <input type="text" name="end_time" class="form-control form-control-sm datetimepicker-input" data-target="#end_time_picker" required value="<?php echo $data['edit_data']['end_time'] ?? ''; ?>"/>
                            <div class="input-group-append" data-target="#end_time_picker" data-toggle="datetimepicker">
                                <div class="input-group-text"><i class="far fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small text-muted">Jenis Kegiatan</label>
                        <select name="activity_type_id" class="form-control" style="height: 45px;" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach ($data['activity_types'] as $type): ?>
                                <option value="<?php echo $type['id']; ?>" <?php echo (isset($data['edit_data']['activity_type_id']) && $data['edit_data']['activity_type_id'] == $type['id']) ? 'selected' : ''; ?>><?php echo $type['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small text-muted">Output / Jumlah Pasien</label>
                        <input type="text" name="output" class="form-control" style="height: 45px;" value="<?php echo htmlspecialchars($data['edit_data']['output'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Contoh: 1 Pasien">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label class="small text-muted">Uraian Kegiatan</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2" required placeholder="Deskripsikan kegiatan secara detail..."><?php echo htmlspecialchars($data['edit_data']['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="small text-muted">Kendala (Opsional)</label>
                        <textarea name="kendala" class="form-control form-control-sm" rows="2" placeholder="Jika ada kendala..."><?php echo htmlspecialchars($data['edit_data']['kendala'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
            </div>
            
            <div class="text-right">
                <?php if (!empty($data['edit_data'])): ?>
                    <a href="<?php echo base_url(); ?>employee/logbook?date=<?php echo $data['date']; ?>" class="btn btn-secondary btn-sm">Batal</a>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary btn-sm px-4" <?php echo $is_final ? 'disabled' : ''; ?>>
                    <i class="fas fa-save"></i> <?php echo !empty($data['edit_data']) ? 'Update' : 'Simpan'; ?>
                </button>
            </div>
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
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Waktu</th>
                    <th style="width: 15%;">Jenis</th>
                    <th>Uraian</th>
                    <th style="width: 15%;">Output</th>
                    <th style="width: 15%;">Kendala</th>
                    <th >Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data['activities'])): ?>
                    <tr><td colspan="7" class="text-center">Belum ada kegiatan.</td></tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($data['activities'] as $activity): ?>
                    <tr>
                        <td class="align-top"><?php echo $no++; ?></td>
                        <td class="align-top"><?php echo date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])); ?></td>
                        <td class="align-top"><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                        <td class="align-top" style="word-wrap: break-word; word-break: break-word; white-space: normal; text-align: justify;"><?php echo nl2br(htmlspecialchars($activity['description'])); ?></td>
                        <td class="align-top"><?php echo htmlspecialchars($activity['output']); ?></td>
                        <td class="align-top"><?php echo htmlspecialchars($activity['kendala']); ?></td>
                        <td class="align-top">
                            <?php if (!$is_final): ?>
                            <a href="<?php echo base_url(); ?>employee/logbook?date=<?php echo $data['date']; ?>&edit_id=<?php echo $activity['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
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
            <?php echo form_open('', ['onsubmit' => "return confirm('Kirim logbook ke Kepala Unit? Data tidak bisa diubah setelah dikirim.');"]); ?>
                <input type="hidden" name="date" value="<?php echo $data['date']; ?>">
                <input type="hidden" name="action" value="submit_logbook">
                <button type="submit" class="btn btn-success btn-block">Kirim Logbook</button>
            </form>
        <?php else: ?>
            <?php if (($data['logbook']['status'] ?? '') == 'revision'): ?>
                <?php echo form_open('', ['onsubmit' => "return confirm('Kirim ulang logbook ke Kepala Unit?');"]); ?>
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

<script>
    $(function () {
        $('#start_time_picker').datetimepicker({
            format: 'HH:mm',
            use24hours: true
        });
        $('#end_time_picker').datetimepicker({
            format: 'HH:mm',
            use24hours: true
        });
    });
</script>
