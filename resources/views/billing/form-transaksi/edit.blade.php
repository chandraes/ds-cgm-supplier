<div class="modal fade" id="editTransaksi" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="editTransaksiTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransaksiTitle">Edit Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="post">
                @csrf
                @method('patch')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="text" class="form-control @if ($errors->has('tanggal'))
                            is-invalid
                        @endif" name="tanggal" id="edit_tanggal" required @if (session('tgl'))
                                value="{{session('tgl')}}" @endif>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select class="form-select" name="supplier_id" id="edit_supplier_id" required>
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach ($project as $s)
                                    <option value="{{$s->id}}" {{session('supplier')==$s->id ? 'selected' :
                                        ''}}>{{$s->nama}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nota_timbangan" class="form-label">Nota Timbang</label>
                            <input type="text" class="form-control @if ($errors->has('nota_timbangan'))
                            is-invalid
                        @endif" name="nota_timbangan" id="edit_nota_timbangan" required>
                            @if ($errors->has('nota_timbangan'))
                            <div class="invalid-feedback">
                                {{$errors->first('nota_timbangan')}}
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="berat" class="form-label">Berat Bersih (Netto)</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control @if ($errors->has('berat'))
                            is-invalid
                        @endif" name="berat" id="edit_berat" required data-thousands=".">
                                <span class="input-group-text" id="basic-addon1">Kg</span>
                            </div>
                            @if ($errors->has('berat'))
                            <div class="invalid-feedback">
                                {{$errors->first('berat')}}
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
