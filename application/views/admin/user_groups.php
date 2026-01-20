<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<?php if ($data['message']): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $data['message']; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<?php endif; ?>

<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Daftar Kabid & Unit Binaan</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama Kabid</th>
                    <th>Unit Asal</th>
                    <th>Unit Binaan (Validasi)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['kabids'] as $kabid): ?>
                <tr>
                    <td><?php echo htmlspecialchars($kabid['name']); ?></td>
                    <td><?php echo htmlspecialchars($kabid['unit_name']); ?></td>
                    <td>
                        <?php 
                        if (empty($kabid['assigned_units'])) {
                            echo '<span class="badge badge-secondary">Default (Unit Asal)</span>';
                        } else {
                            $unit_names = [];
                            foreach ($data['units'] as $unit) {
                                if (in_array($unit['id'], $kabid['assigned_units'])) {
                                    $unit_names[] = $unit['name'];
                                }
                            }
                            echo implode(', ', $unit_names);
                        }
                        ?>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editModal<?php echo $kabid['id']; ?>">
                            <i class="fas fa-edit"></i> Atur Unit
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="editModal<?php echo $kabid['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?php echo $kabid['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $kabid['id']; ?>">Atur Unit Binaan - <?php echo htmlspecialchars($kabid['name']); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <?php echo form_open(''); ?>
                                        <div class="modal-body">
                                            <input type="hidden" name="user_id" value="<?php echo $kabid['id']; ?>">
                                            <div class="form-group">
                                                <label>Pilih Unit yang Divalidasi:</label>
                                                <div class="row">
                                                    <?php foreach ($data['units'] as $unit): ?>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="unitCheck<?php echo $kabid['id'] . '_' . $unit['id']; ?>" name="unit_ids[]" value="<?php echo $unit['id']; ?>" 
                                                                <?php echo in_array($unit['id'], $kabid['assigned_units']) ? 'checked' : ''; ?>>
                                                            <label class="custom-control-label" for="unitCheck<?php echo $kabid['id'] . '_' . $unit['id']; ?>"><?php echo htmlspecialchars($unit['name']); ?></label>
                                                        </div>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    <?php echo form_close(); ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $this->load->view('layouts/footer'); ?>
