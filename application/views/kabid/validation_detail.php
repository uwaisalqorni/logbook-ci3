<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<div class="row mb-3">
    <div class="col-md-6">
        <a href="<?php echo base_url(); ?>kabid/validation" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
    <div class="col-md-6 text-right">
        <h5>Status: <span class="badge badge-info"><?php echo ucfirst($data['logbook']['status']); ?></span></h5>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Logbook Head</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Jenis</th>
                    <th>Uraian</th>
                    <th>Output</th>
                    <th>Kendala</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['activities'] as $activity): ?>
                <tr>
                    <td><?php echo date('H:i', strtotime($activity['start_time'])) . ' - ' . date('H:i', strtotime($activity['end_time'])); ?></td>
                    <td><?php echo htmlspecialchars($activity['activity_name']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($activity['description'])); ?></td>
                    <td><?php echo htmlspecialchars($activity['output']); ?></td>
                    <td><?php echo htmlspecialchars($activity['kendala']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($data['logbook']['status'] == 'submitted'): ?>
    <div class="card-footer">
        <?php echo form_open(''); ?>
            <div class="form-group">
                <label>Catatan Validasi (Opsional untuk Setuju, Wajib untuk Tolak/Revisi)</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Masukkan catatan di sini..."><?php echo htmlspecialchars($data['validation']['notes'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <button type="submit" name="status" value="approved" class="btn btn-success btn-block" onclick="return confirm('Setujui logbook ini?');">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="status" value="revision" class="btn btn-warning btn-block" onclick="return confirm('Minta revisi untuk logbook ini?');">
                        <i class="fas fa-edit"></i> Revisi
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-block" onclick="return confirm('Tolak logbook ini?');">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                </div>
            </div>
        </form>
    </div>
    <?php else: ?>
        <?php if (!empty($data['validation'])): ?>
        <div class="card-footer">
            <h5>Riwayat Validasi</h5>
            <div class="callout callout-<?php echo ($data['validation']['status'] == 'approved') ? 'success' : (($data['validation']['status'] == 'revision') ? 'warning' : 'danger'); ?>">
                <h5><?php echo ucfirst($data['validation']['status']); ?></h5>
                <p><?php echo nl2br(htmlspecialchars($data['validation']['notes'])); ?></p>
                <small class="text-muted">Divalidasi pada: <?php echo date('d F Y H:i', strtotime($data['validation']['validated_at'])); ?></small>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php $this->load->view('layouts/footer'); ?>
