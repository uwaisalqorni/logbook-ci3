<?php $this->load->view('layouts/header', ['data' => $data]); ?>

<?php if ($data['message']): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo $data['message']; ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <?php echo form_open('', ['class' => 'form-inline']); ?>
            <input type="hidden" name="action" value="add">
            <div class="form-group mr-2">
                <input type="text" name="name" class="form-control" placeholder="Nama Jenis Kegiatan Baru" required>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Jenis Kegiatan</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">ID</th>
                    <th>Nama Jenis Kegiatan</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['activities'] as $activity): ?>
                <tr>
                    <td><?php echo $activity['id']; ?></td>
                    <td><?php echo htmlspecialchars($activity['name']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editModal<?php echo $activity['id']; ?>">Edit</button>
                        <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal<?php echo $activity['id']; ?>">Hapus</button>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?php echo $activity['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <?php echo form_open(''); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Jenis Kegiatan</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                                            <div class="form-group">
                                                <label>Nama Jenis Kegiatan</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($activity['name']); ?>" required>
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
                        <div class="modal fade" id="deleteModal<?php echo $activity['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <?php echo form_open(''); ?>
                                        <div class="modal-header">
                                            <h5 class="modal-title">Hapus Jenis Kegiatan</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $activity['id']; ?>">
                                            <p>Apakah Anda yakin ingin menghapus jenis kegiatan <strong><?php echo htmlspecialchars($activity['name']); ?></strong>?</p>
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
