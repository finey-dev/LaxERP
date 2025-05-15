    <div class="modal-body">
    @foreach($response as $que => $ans)
        <div class="col-12 text-xs">
            <h6>{{$que}}</h6>
            <p>{{$ans}}</p>
        </div>
    @endforeach
</div>
