<div class="modal-header pb-2 pt-2">
    <h5 class="modal-title" id="exampleModalLongTitle">{{ $tracker->project_task->title}} <small>( {{$tracker->total}} {{date('H:i:s, d M ',strtotime($tracker->start_time))}} )</small></h5>
<!--    <button type="button" class="btn  btn-light text-end" data-bs-dismiss="modal">{{ __('Cancel')}}</button> -->
   <button type="button" class="btn-close mt-1" data-bs-dismiss="modal" aria-label="Close"></button>

    </button>
  </div>
  <div class="modal-body p-1">
      <div class="row ">
        <div class="col-lg-12 product-left mb-5 mb-lg-0">
            @if( $images->count() > 0)
            <div class="swiper-container product-slider m-2 p-2" style="border-bottom:solid 2px #f2f3f5">
                <div class="swiper-wrapper">
                    @foreach ($images as $image)
                        <div class="swiper-slide" id="slide-{{$image->id}}">
                            <img src="{{ get_file('uploads/traker_images/'.$tracker->id.'/'.$image->img_path)}}" alt="..."  class="img-fluid">

                               <div class="time_in_slider mt-2"> {{date('H:i:s, d M ',strtotime($image->time))}}
                                    @permission('timetracker img delete')
                                    <a href="#" class=" action-btn btn-danger btn btn-sm d-inline-flex align-items-center"  onclick="delete_image({{$image->id}})" data-confirm-yes="removeImage({{$image->id}})">
                                        <i class="ti ti-trash"></i>
                                    </a>
                                    @endpermission
                          </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>

            <div class="swiper-container product-thumbs p-2">
                <div class="swiper-wrapper">
                    @foreach ($images as $image)
                    <div class="swiper-slide" id="slide-thum-{{$image->id}}">
                        <img src="{{ get_file('uploads/traker_images/'.$tracker->id.'/'.$image->img_path)}}" alt="..." class="img-fluid">
                    </div>
                    @endforeach

                </div>
            </div>
            @else
            <div class="no-image">
                <h5 class="text-muted">{{__('Images Not Available.')}}</h5>
            </div>
            @endif
        </div>
      </div>
  </div>
  <script type="text/javascript">

    function delete_image(id){
        const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })
            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "This action can not be undone. Do you want to continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    removeImage(id);
                }
            })
    }
</script>
