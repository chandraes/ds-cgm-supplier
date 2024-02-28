<div class="modal fade" id="editCustomer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="editCustomerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerTitle">Edit Project @isset($d) {{$d->nama}} @endisset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="editForm">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama" class="form-label">Nama Project</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nilai" class="form-label">Nilai Project</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Rp</span>
                                <input type="text" class="form-control" name="edit_nilai" id="nilai" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="tanggal_mulai" id="edit_tanggal_mulai" aria-describedby="helpId"
                                    placeholder="" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jatuh_tempo" class="form-label">Tanggal Jatuh Tempo</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-calendar"></i></span>
                                <input type="text" class="form-control" name="jatuh_tempo" id="edit_jatuh_tempo" aria-describedby="helpId"
                                    placeholder="" required readonly>
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
