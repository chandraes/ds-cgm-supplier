{{-- <div class="modal fade" id="modalNotaBayar" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="notaBayarTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notaBayarTitle">Nota Bayar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('nota-bayar.index')}}" method="get">

                <div class="modal-body">
                    <div class="col-md-12 mb-3">
                        <select class="form-select" name="supplier_id" id="supplier_id">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach ($supplier as $s)
                            <option value="{{$s->id}}">{{$s->nama}} ({{$s->nickname}}) @if($t->where('supplier_id', $s->id)->where('bayar', 0)->count() != 0) <span class="text-danger">({{$t->where('supplier_id', $s->id)->where('bayar', 0)->count()}}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Lanjutkan</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
