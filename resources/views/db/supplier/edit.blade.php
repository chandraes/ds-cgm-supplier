<div class="modal fade" id="editSupplier" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="editInvestorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInvestorTitle">Edit Customer @isset($d) {{$d->nama}} @endisset</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" id="editForm">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="edit_nama" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="nickname" class="form-label">Nickname</label>
                            <input type="text" class="form-control" name="nickname" id="edit_nickname"
                                aria-describedby="helpId" placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="no_wa" class="form-label">No WA</label>
                            <input type="text" class="form-control" name="no_wa" id="edit_no_wa" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="no_ktp" class="form-label">No KTP</label>
                            <input type="text" class="form-control" name="no_ktp" id="edit_no_ktp" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" name="npwp" id="edit_npwp" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="harga" class="form-label">Persentase Profit</label>
                            <div class="input-group">
                                <input type="text" class="form-control @if ($errors->has('persen_profit'))
                                    is-invalid
                                @endif" name="persen_profit" id="edit_persen_profit" required>
                                <span class="input-group-text" id="basic-addon1">%</span>
                            </div>
                            <small id="helpId" class="form-text text-muted"><span class="text-danger">(Gunakan '.' untuk desimal)</span></small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" id="edit_alamat" rows="3" required></textarea>
                        </div>
                    </div>
                    <hr>
                    <h2>Informasi BANK</h2>
                    <hr>
                    <div class="row">
                        <div class="col-4 mb-3">
                            <label for="nama_rek" class="form-label">Atas Nama</label>
                            <input type="text" class="form-control" name="nama_rek" id="edit_nama_rek"
                                aria-describedby="helpId" placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="no_rek" class="form-label">Nomor Rekening</label>
                            <input type="text" class="form-control" name="no_rek" id="edit_no_rek" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="bank" class="form-label">BANK</label>
                            <input type="text" class="form-control" name="bank" id="edit_bank" aria-describedby="helpId"
                                placeholder="" required>
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
