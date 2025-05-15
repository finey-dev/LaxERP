@extends('layouts.main')
@section('page-title')
    {{ __('Manage Time Tracker') }}
@endsection
@section('page-breadcrumb')
    {{ __('Time Tracker') }}
@endsection
@section('page-action')
@endsection
@push('css')
    @include('layouts.includes.datatable-css')
    <link rel="stylesheet" href="{{ url('js/swiper/dist/css/swiper.min.css') }}">
    <style>
        .product-thumbs .swiper-slide img {
            border: 2px solid transparent;
            object-fit: cover;
            cursor: pointer;
        }

        .product-thumbs .swiper-slide-active img {
            border-color: #bc4f38;
        }

        .product-slider .swiper-button-next:after,
        .product-slider .swiper-button-prev:after {
            font-size: 20px;
            color: #000;
            font-weight: bold;
        }

        .modal-dialog.modal-md {
            background-color: #fff !important;
        }

        /* .modal-backdrop {
            background:transparent !important;
        } */
        .no-image {
            min-height: 300px;
            align-items: center;
            display: flex;
            justify-content: center;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <h5></h5>
                    <div class="table-responsive">
                        {{ $dataTable->table(['width' => '100%']) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg ss_modale" role="document">
            <div class="modal-content image_sider_div">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.includes.datatable-js')
    {{ $dataTable->scripts() }}
    <script src="{{ url('js/swiper/dist/js/swiper.min.js') }}"></script>

    <script type="text/javascript">
        function init_slider() {
            if ($(".product-left").length) {
                var productSlider = new Swiper('.product-slider', {
                    spaceBetween: 0,
                    centeredSlides: false,
                    loop: false,
                    direction: 'horizontal',
                    loopedSlides: 5,
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev",
                    },
                    resizeObserver: true,
                });
                var productThumbs = new Swiper('.product-thumbs', {
                    spaceBetween: 0,
                    centeredSlides: true,
                    loop: false,
                    slideToClickedSlide: true,
                    direction: 'horizontal',
                    slidesPerView: 7,
                    loopedSlides: 5,
                });
                productSlider.controller.control = productThumbs;
                productThumbs.controller.control = productSlider;
            }
        }


        $(document).on('click', '.view-images', function() {

            var p_url = "{{ route('tracker.image.view') }}";
            var data = {
                'id': $(this).attr('data-id')
            };

            postAjax(p_url, data, function(res) {
                $('.image_sider_div').html(res);
                $('#exampleModalCenter').modal('show');
                setTimeout(function() {
                    var total = $('.product-left').find('.product-slider').length
                    if (total > 0) {
                        init_slider();
                    }

                }, 200);

            });
        });


        // ============================ Remove Track Image ===============================//
        $(document).on("click", '.track-image-remove', function() {
            var rid = $(this).attr('data-pid');
            $('.confirm_yes').addClass('image_remove');
            $('.confirm_yes').attr('image_id', rid);
            $('#cModal').modal('show');
            var total = $('.product-left').find('.swiper-slide').length
        });



        function removeImage(id) {
            var p_url = "{{ route('tracker.image.remove') }}";
            var data = {
                id: id
            };
            deleteAjax(p_url, data, function(res) {
                if (res.flag) {
                    $('#slide-thum-' + id).remove();
                    $('#slide-' + id).remove();
                    setTimeout(function() {
                        var total = $('.product-left').find('.swiper-slide').length
                        if (total > 0) {
                            init_slider();
                        } else {
                            $('.product-left').html(
                                '<div class="no-image"><h5 class="text-muted">Images Not Available .</h5></div>'
                                );
                        }
                    }, 200);
                }
                $('#cModal').modal('hide');
                show_toastr('success', res.msg, "success");
            });
        }
        // $(document).on("click", '.remove-track', function () {

        // var rid = $(this).attr('data-id');
        // $('.confirm_yes').addClass('t_remove');
        // $('.confirm_yes').attr('uid', rid);
        // $('#cModal').modal('show');
        // });
    </script>
@endpush
