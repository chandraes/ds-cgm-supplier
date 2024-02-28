<div class="modal fade" id="editHargaCustomer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="editCustomerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerTitle">Edit Harga Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="editHargaForm">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row mt-3">
                        <div class="col-12 mb-3">
                          <label for="nama" class="form-label">Nama</label>
                          <input type="text"
                            class="form-control" name="nama" id="edit_harga_nama" aria-describedby="helpId" placeholder="" disabled>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="harga" class="form-label">Harga</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                <input type="text" class="form-control @if ($errors->has('harga'))
                            is-invalid
                        @endif" name="harga" id="editHarga2" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
