@extends('layouts.main')
@section('page-title')
    {{ __('Journal Detail') }}
@endsection

@section('page-breadcrumb')
    {{ __('Journal Entry') }}
    {{ \Workdo\DoubleEntry\Entities\JournalEntry::journalNumberFormat($journalEntry->journal_id) }}
@endsection


@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row row-gap invoice-title border-1 border-bottom  pb-3 mb-3">
                                <div class="col-sm-4  col-12">
                                    <h2 class="h3 mb-0">{{ __('Journal') }}</h2>
                                </div>
                                <div class="col-sm-8  col-12">
                                    <div
                                        class="d-flex invoice-wrp flex-wrap align-items-center gap-md-2 gap-1 justify-content-end">
                                        <div
                                            class="d-flex invoice-date flex-wrap align-items-center justify-content-end gap-md-3 gap-1">
                                            <p class="mb-0"><strong>{{ __('Journal Date') }} :</strong>
                                                {{ company_date_formate($journalEntry->date) }}</p>
                                        </div>
                                        <h3 class="invoice-number mb-0">
                                            {{ \Workdo\DoubleEntry\Entities\JournalEntry::journalNumberFormat($journalEntry->journal_id) }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <div class="p-sm-4 p-3 invoice-billed">
                                <div class="row row-gap">
                                    <div class="col-lg-5 col-sm-6">
                                        <p class="mb-2"><strong class="h5 mb-1 d-block">{{ __('To') }}
                                                :</strong>
                                            <span class="d-block" style="max-width:80%">
                                                {{ !empty($settings['company_name']) ? $settings['company_name'] : '' }}<br>
                                                {{ !empty($settings['company_telephone']) ? $settings['company_telephone'] : '' }}<br>
                                                {{ !empty($settings['company_address']) ? $settings['company_address'] : '' }}<br>
                                                {{ !empty($settings['company_city']) ? $settings['company_city'] . ',' : '' }}
                                                {{ !empty($settings['company_state']) ? $settings['company_state'] . ',' : '' }}
                                                {{ !empty($settings['company_country']) ? $settings['company_country'] : '' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <p class="mb-2">
                                            <strong class="h5 mb-1 d-block">{{ __('Description') }}
                                                :</strong>
                                            <span class="text-muted d-block" style="max-width:80%">
                                                {{ $journalEntry->description }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-lg-4 col-sm-6 text-end">
                                        <p class="mb-1 text-dark">
                                            <strong>{{ __('Journal No ') }} :
                                            </strong>
                                            {{ \Workdo\DoubleEntry\Entities\JournalEntry::journalNumberFormat($journalEntry->journal_id) }}
                                        </p>
                                        <p class="mb-1 text-dark">
                                            <strong>{{ __('Journal Ref ') }} :
                                            </strong> {{ $journalEntry->reference }}
                                        </p>
                                        <p class="mb-1 text-dark">
                                            <strong>{{ __('Journal Date ') }} :
                                            </strong> {{ company_date_formate($journalEntry->date) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if (!empty($customFields) && count($journalEntry->customField) > 0)
                                <div class="px-4 mt-3">
                                    <div class="row row-gap">
                                        @foreach ($customFields as $field)
                                            <div class="col-xxl-3 col-sm-6">
                                                <strong class="d-block mb-1">{{ $field->name }} </strong>
                                                @if ($field->type == 'attachment')
                                                    <a href="{{ get_file($journalEntry->customField[$field->id]) }}"
                                                        target="_blank">
                                                        <img src="{{ get_file($journalEntry->customField[$field->id]) }}"
                                                            class="wid-120 rounded">
                                                    </a>
                                                @else
                                                    <p class="mb-0">
                                                        {{ !empty($journalEntry->customField[$field->id]) ? $journalEntry->customField[$field->id] : '-' }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="invoice-summary mt-3">
                                <div class="invoice-title border-1 border-bottom mb-3 pb-2">
                                    <h3 class="h4 mb-0">{{ __('Journal Account Summary') }}</h3>
                                </div>
                                <div class="table-responsive mt-2">
                                    <table class="table mb-0 table-striped">
                                        <tr>
                                            <th data-width="40" class="text-white bg-primary text-uppercase">#</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Account') }}</th>
                                            <th class="text-white bg-primary text-uppercase" width="25%">
                                                {{ __('Description') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Debit') }}</th>
                                            <th class="text-white bg-primary text-uppercase">{{ __('Credit') }}</th>
                                            <th class="text-right text-white bg-primary text-uppercase">{{ __('Amount') }}
                                            </th>
                                            <th class="text-right text-white bg-primary text-uppercase"></th>
                                        </tr>

                                        @foreach ($accounts as $key => $account)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ !empty($account->accounts) ? $account->accounts->code . ' - ' . $account->accounts->name : '' }}
                                                </td>
                                                <td>{{ !empty($account->description) ? $account->description : '-' }}
                                                </td>
                                                <td>{{ currency_format_with_sym($account->debit) }}</td>
                                                <td>{{ currency_format_with_sym($account->credit) }}</td>
                                                <td>
                                                    @if ($account->debit != 0)
                                                        {{ currency_format_with_sym($account->debit) }}
                                                    @else
                                                        {{ currency_format_with_sym($account->credit) }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="action-btn">
                                                        {!! Form::open(['method' => 'DELETE', 'route' => ['journal.destroy', $account->id]]) !!}
                                                        <a href="#!"
                                                            class="bg-danger btn btn-sm  align-items-center text-white show_confirm"
                                                            data-bs-toggle="tooltip" title='Delete'>
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tfoot>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td class="text-right"><b>{{ __('Total Credit') }}</b></td>
                                                <td class="text-right">{{ currency_format_with_sym($journalEntry->totalCredit()) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4"></td>
                                                <td class="text-right"><b>{{ __('Total Debit') }}</b></td>
                                                <td class="text-right">{{ currency_format_with_sym($journalEntry->totalDebit()) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
