<div class="modal fade" id="createCustomer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="createCustomerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCustomerTitle">Tambah Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('db.customer.store')}}" method="post" id="createForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" name="nama" id="nama" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="singkatan" class="form-label">Singkatan</label>
                            <input type="text" class="form-control" name="singkatan" id="singkatan"
                                aria-describedby="helpId" placeholder="" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cp" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" name="cp" id="cp" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="no_wa" class="form-label">Nomor WA</label>
                            <input type="text" class="form-control" name="no_wa" id="no_wa" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="npwp" class="form-label">NPWP</label>
                            <input type="text" class="form-control" name="npwp" id="npwp" aria-describedby="helpId"
                                placeholder="" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" name="alamat" id="alamat" rows="3" required></textarea>
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
