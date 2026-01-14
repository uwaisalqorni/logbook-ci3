<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<?php if ($data['message']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $data['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row mb-2">
    <div class="col-sm-12 text-right">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModal">
            <i class="fas fa-plus"></i> Tambah Pegawai
        </button>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <?php echo form_open(''); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pegawai Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>NIK</label>
                        <input type="text" name="nik" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="role" class="form-control" required>
                            <option value="employee">Employee (Perawat/Bidan)</option>
                            <option value="head">Head of Unit (Kepala Ruangan)</option>
                            <option value="management">Management</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <select name="unit_id" class="form-control">
                            <option value="">-- Pilih Unit --</option>
                            <?php foreach ($data['units'] as $unit): ?>
                                <option value="<?php echo $unit['id']; ?>"><?php echo $unit['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan</label>
                        <input type="text" name="position" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Golongan</label>
                        <select name="golongan" class="form-control">
                            <option value="">-- Pilih Golongan --</option>
                            <option value="Medis">Medis</option>
                            <option value="Non Medis">Non Medis</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Unit</th>
                    <th>Jabatan</th>
                    <th>Golongan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['users'] as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['nik']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span></td>
                    <td><?php echo htmlspecialchars($user['unit_name'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($user['position']); ?></td>
                    <td><?php echo htmlspecialchars($user['golongan']); ?></td>
                    <td>
                        <?php if ($user['status'] == 'active'): ?>
                            <span class="badge badge-success">Active</span>
                        <?php else: ?>
                            <span class="badge badge-secondary">Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?php echo $user['id']; ?>">Edit</button>
                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $user['id']; ?>">Hapus</button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $user['id']; ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pegawai</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <div class="form-group">
                                                <label>NIK</label>
                                                <input type="text" name="nik" class="form-control" value="<?php echo htmlspecialchars($user['nik']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Password (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label>Role</label>
                                                <select name="role" class="form-control" required>
                                                    <option value="employee" <?php echo $user['role'] == 'employee' ? 'selected' : ''; ?>>Employee</option>
                                                    <option value="head" <?php echo $user['role'] == 'head' ? 'selected' : ''; ?>>Head of Unit</option>
                                                    <option value="management" <?php echo $user['role'] == 'management' ? 'selected' : ''; ?>>Management</option>
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Unit</label>
                                                <select name="unit_id" class="form-control">
                                                    <option value="">-- Pilih Unit --</option>
                                                    <?php foreach ($data['units'] as $unit): ?>
                                                        <option value="<?php echo $unit['id']; ?>" <?php echo $user['unit_id'] == $unit['id'] ? 'selected' : ''; ?>><?php echo $unit['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Jabatan</label>
                                                <input type="text" name="position" class="form-control" value="<?php echo htmlspecialchars($user['position']); ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Golongan</label>
                                                <select name="golongan" class="form-control">
                                                    <option value="">-- Pilih Golongan --</option>
                                                    <?php 
                                                    $golongans = ['PK I', 'PK II', 'PK III', 'PK IV', 'PK V', 'Non PK'];
                                                    foreach ($golongans as $gol) {
                                                        $selected = ($user['golongan'] == $gol) ? 'selected' : '';
                                                        echo "<option value='$gol' $selected>$gol</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Status</label>
                                                <select name="status" class="form-control">
                                                    <option value="active" <?php echo $user['status'] == 'active' ? 'selected' : ''; ?>>Active</option>
                                                    <option value="inactive" <?php echo $user['status'] == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Pegawai</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                            <p>Apakah Anda yakin ingin menghapus pegawai <strong><?php echo htmlspecialchars($user['name']); ?></strong>?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-danger">Hapus</button>
                                        </div>
                                    </form>
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
