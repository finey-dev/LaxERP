<div class="modal-body">
    @foreach($response as $que => $ans)
        <div class="col-12">
            <b>{{$que}}</b> <br>
            <p>{{$ans}}</p>
        </div>
    @endforeach
</div>
