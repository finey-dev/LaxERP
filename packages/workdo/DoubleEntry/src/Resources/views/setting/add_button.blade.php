<div class="action-btn me-2">
    <a href="{{ route('report.ledger', $account_id) }}?account={{ $account_id }}"
       class="bg-warning btn btn-sm align-items-center " data-bs-toggle="tooltip"
       title="{{ __('Transaction Summary') }}"
       data-original-title="{{ __('Detail') }}">
        <i class="ti ti-wave-sine text-white"></i>
    </a>
</div>
