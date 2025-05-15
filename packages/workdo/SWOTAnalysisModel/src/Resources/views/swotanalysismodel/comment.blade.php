<div class="media mt-2 border-top border-1 border-primary pt-3">
    <a class="pr-2" href="#">
        <img src="{{ check_file($subComment->commentUser->avatar) ? get_file($subComment->commentUser->avatar) : get_file('uploads/users-avatar/avatar.png') }}"
            class="rounded border-2 border border-primary" width="40" height="40">
    </a>
    <div class="media-body">
        <h6 class="mt-0 ms-2">
            {{ !empty($subComment->commentUser->name) ? $subComment->commentUser->name : '' }}
            <small class="float-right text-muted">{{ $subComment->created_at->diffForHumans() }}</small>
        </h6>
        <div class="d-flex gap-2 align-items-center">
            <p class="text-sm mb-0 ms-2">
                {{ $subComment->comment }}
            </p>
            <div class="d-flex align-items-center">
                @if (!empty($subComment->file))
                    <div class="d-flex ms-2">
                        <a href="#" class="like active" style="margin-bottom: -13px;">
                            <i class="ni ni-cloud-download-95"></i>
                            <a href="{{ get_file('uploads/SWOTAnalysisModel') . '/' . $subComment->file }}"
                                download="" class="p-1 m-0 btn btn-sm d-inline-flex align-items-center"
                                data-bs-toggle="tooltip" title="{{ __('Download') }}">
                                <i class="ti ti-download text-primary"></i> </a>
                        </a>
                    </div>
                    <div class="d-flex ms-0">
                        <a href="{{ get_file('uploads/SWOTAnalysisModel') . '/' . $subComment->file }}" target=_blank
                            class="p-1 m-0 text-white btn btn-sm d-inline-flex align-items-center "
                            data-bs-toggle="tooltip" title="{{ __('Preview') }}">
                            <i class="ti ti-crosshair text-primary"></i>
                        </a>
                    </div>
                @endif
                @permission('SWOTAnalysisModel comment replay')
                    <div class="d-flex ms-0">
                        <a href="#"
                            data-url="{{ route('swotanalysismodel.comment.reply', [$Charters->id, $subComment->id]) }}"
                            class="p-1 m-0 text-white btn btn-sm d-inline-flex align-items-center " data-ajax-popup="true"
                            data-bs-toggle="tooltip" data-title="{{ __('Create Comment Reply') }}"
                            title="{{ __('Reply') }}">
                            <i class="ti ti-send text-primary"></i>
                        </a>
                    </div>
                @endpermission
            </div>
        </div>
        @foreach ($subComment->subComment as $subcom)
            @include('swotanalysis-model::swotanalysismodel.comment', ['subComment' => $subcom])
        @endforeach
    </div>
</div>
