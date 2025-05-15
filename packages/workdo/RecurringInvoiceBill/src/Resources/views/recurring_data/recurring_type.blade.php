@if($recuuring_show->recurring_type == "invoice")
<p class="text-muted text-sm mb-2">
{{__(' Recurring Invoice')}}
</p>
@else
<p class="text-muted text-sm mb-2">
    {{__(' Recurring Bill')}}
</p>
@endif
