<div class="modal fade" id="notaTagihan" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="notaTagihanTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notaTagihanTitle">Nota Tagihan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @foreach ($customer as $c)
                    <div class="col-md-3 m-2">
                        <a href="{{route('nota-tagihan.index', ['customer' => $c->id])}}" class="text-decoration-none">
                            <img src="{{asset('images/palm.svg')}}" alt="" width="100">
                            <h3 class="mt-2">{{$c->singkatan}} @if($t->where('customer_id', $c->id)->where('tagihan', 0)->count() != 0) <span class="text-danger">({{$t->where('customer_id', $c->id)->where('tagihan', 0)->count()}})</span> @endif</h3>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
