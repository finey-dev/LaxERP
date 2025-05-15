{{ Form::model($Requests, ['route' => ['requests.update', $Requests->id], 'method' => 'PUT','class'=>'needs-validation','novalidate']) }}

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
                {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Category Name'), 'required' => 'required']) }}
                @error('name')
                    <small class="invalid-name" role="alert">
                        <strong class="text-danger">{{ $message }}</strong>
                    </small>
                @enderror
            </div>
        </div>
            @if(Module_is_active('Lead'))
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('module_type', __('Type'), ['class' => 'form-label']) }}
                    {{ Form::select('module_type', $module_type, null, ['class' => 'form-control']) }}
                </div>
            </div>
            @endif
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('category', __('Category'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('category', $requestscategory, $Requests->category_id, ['class' => 'form-control category', 'placeholder' => __('Select Category '), 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('subcategory', __('SubCategory'), ['class' => 'form-label']) }}<x-required></x-required>
                    {{ Form::select('subcategory', $requestssubcategory, $Requests->subcategory_id, ['class' => 'form-control subcategory', 'placeholder' => __('Select SubCategory '), 'required' => 'required']) }}
                </div>
            </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mt-4 mb-3">
            {{ Form::label('active', __('Active'), ['class' => 'form-label']) }}
            <div class="form-check form1  form-switch custom-switch-v1">
                <input type="hidden" name="active" value="off">
                <input type="checkbox" class="form-check-input input-primary" name="active" id="active"
                    {{ !empty($Requests->active) && $Requests->active == 'on' ? 'checked="checked"' : '' }} >
                <label class="form-check-label" for="customswitchv1-1"></label>
            </div>
        </div>
    </div>
    <div class="row modal-body-card">
        @php
            $i = 1;
        @endphp
        @foreach ($themeOne as $key => $v)
        <div class=" col-lg-4  col-sm-6 business-view-card @if($Requests->layouts == $key) selected-theme @endif">
            <label for="Formlayout1">
                <input type="hidden" value="" name="layouts" class="themefile1" >

                <div class="business-view-inner">
                    <div class="buisness-img">
                        <img id="{{ $key }}" class="color_theme1 Formlayout1_img" data-id="{{$key}}"
                            src="{{asset('packages/workdo/Requests/src/Resources/assets/form/'. $key .'/images/form.png')}}"
                            alt="" style="height: 100%;width: 100%;">
                    </div>
                    <div class=" mt-3">
                        <h6>{{ __('Form Layout  ' . $i++)}}</h6>
                        <span class="mb-1 d-block">{{__('Select Sub-Color:')}}</span>
                        <div class="d-flex align-items-center business-color-input" id="{{ $key }}">
                            @foreach ($v as $css => $val)
                            <label class="colorinput">
                                <input type="radio" name="theme_color" id="color1-{{ $key }}"
                                    value="{{ $css }}" data-theme="{{ $key }}"
                                    data-imgpath="{{ $val['img_path'] }} " data-id={{ $key }}
                                    class="colorinput-input" @if($Requests->theme_color == $css && $Requests->layouts == $key ) checked @endif>
                                <span class="border-box">
                                    <span class="colorinput-color" style="background:{{ $val['color'] }}"></span>
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </label>
        </div>
        @endforeach
    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).on('click', 'input[name="theme_color"]', function() {
        var eleParent = $(this).attr('data-theme');
        $('.themefile1').val(eleParent);
        var imagePath = $(this).data('imgpath');

        var imgId = $(this).data('id');


        $('#' + imgId).attr('src', imagePath)
        //$('.' + eleParent + '_img').attr('src', imgpath);
        $(".business-view-card").removeClass('selected-theme')

        $(this).closest('.business-view-card').addClass('selected-theme');
    });

    $(document).on("click", ".color_theme1", function() {
        var id = $(this).attr('data-id');
        $(".business-view-card").removeClass('selected-theme')
        $(this).closest('.business-view-card').addClass('selected-theme');
        var dataId = $(this).attr("data-id");
        $('#color1-' + dataId).trigger('click');
    });

    $(document).ready(function() {
        var checked = $("input[type=radio][name='theme_color']:checked");
        $('.themefile1').val(checked.attr('data-theme'));
        $(checked).closest('.business-view-card').addClass('selected-theme');
    });
</script>
<script>
    $(document).on('change', '.category', function() {
        var category = $(this).val();
        $.ajax({
            url: '{{ route('request.category') }}',
            type: 'POST',
            data: {
                "category": category
            },
            success: function(response) {
                $('.subcategory').empty();
                $('.subcategory').append('<option value="0">Select SubCategory </option>');
                $.each(response, function(key, value) {
                    $('.subcategory').append('<option value="' + value.id + '">' +
                        value.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error); // Debugging line
            }
        });
    });
    $(document).on('click', 'input[name="theme_color"]', function() {
        var imagePath = $(this).data('imgpath');
        var imgId = $(this).data('id');
        $('#' + imgId).attr('src', imagePath);
    });
</script>
