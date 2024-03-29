<div class="modal fade" id="editInvestor" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="editInvestorTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInvestorTitle">Edit Persentase Deviden @isset($d) {{$d->nama}} @endisset</h5>
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
                                placeholder="" required disabled>
                        </div>
                        <div class="col-4 mb-3">
                            <label for="persentase" class="form-label">Persentase</label>
                            <input type="number" class="form-control" name="persentase" id="edit_persentase"
                                aria-describedby="helpId" placeholder="" required>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
