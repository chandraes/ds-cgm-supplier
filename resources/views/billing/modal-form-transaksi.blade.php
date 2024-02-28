<div class="modal fade" id="modalTransaksi" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="transaksiTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transaksiTitle">Form Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach ($customer as $c)
                    <div class="col-md-3 m-3">
                        <a href="{{route('form-transaksi.tambah', ['customer' => $c->id])}}"
                            class="text-decoration-none">
                            <img src="{{asset('images/palm.svg')}}" alt="" width="100">
                            <h3 class="mt-2">{{$c->singkatan}}</h3>
                        </a>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
