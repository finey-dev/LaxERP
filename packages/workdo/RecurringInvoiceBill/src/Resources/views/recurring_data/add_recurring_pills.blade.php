<div class="tab-pane fade" id="invoice-recurring" role="tabpanel" aria-labelledby="pills-user-tab-4">
    <div class="row">
        @if ($recuuring_show->recurring_type == 'invoice')
            <div class="col-12">
                <div class="card border">
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table mb-0 pc-dt-simple" id="assets">
                                <thead>
                                    <tr>
                                        <th> {{ __('Invoice') }}</th>
                                        @if (\Auth::user()->type != 'client')
                                            <th>{{ __('Customer') }}</th>
                                        @endif
                                        <th>{{ __('Account Type') }}</th>
                                        <th>{{ __('Issue Date') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Total Amount') }}</th>
                                        <th>{{ __('Due Amount') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="Id">
                                            @if (Laratrust::hasPermission('invoice show'))
                                                <a href="{{ route('invoice.show', \Crypt::encrypt($invoice->id)) }}"
                                                    class="btn btn-outline-primary">{{ App\Models\Invoice::invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @else
                                                <a href="#"
                                                    class="btn btn-outline-primary">{{ App\Models\Invoice::invoiceNumberFormat($invoice->invoice_id) }}</a>
                                            @endif
                                        </td>
                                        @if ($invoice->account_type == 'lms')
                                            <td>{{ !empty($invoice->student) ? $invoice->student->name : '' }}</td>
                                        @elseif (\Auth::user()->type != 'client')
                                            <td>{{ !empty($invoice->customers) ? $invoice->customers->name : '' }}</td>
                                        @endif
                                        <td>{{ $invoice->account_type }}</td>
                                        <td>{{ company_date_formate($invoice->issue_date) }}</td>
                                        <td>
                                            @if ($invoice->due_date < date('Y-m-d'))
                                                <p class="text-danger">
                                                    {{ company_date_formate($invoice->due_date) }}</p>
                                            @else
                                                {{ company_date_formate($invoice->due_date) }}
                                            @endif
                                        </td>
                                        @if ($invoice->invoice_module == 'childcare')
                                            <td>{{ currency_format_with_sym($invoice->getChildTotal()) }}</td>
                                        @else
                                            <td>{{ currency_format_with_sym($invoice->getTotal()) }}</td>
                                        @endif

                                        @if ($invoice->invoice_module == 'childcare')
                                            <td>{{ currency_format_with_sym($invoice->getChildDue()) }}</td>
                                        @else
                                            <td>{{ currency_format_with_sym($invoice->getDue()) }}</td>
                                        @endif
                                        <td>
                                            @if ($invoice->status == 0)
                                                <span
                                                    class="badge fix_badges bg-primary p-2 px-3 ">{{ __(App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 1)
                                                <span
                                                    class="badge fix_badges bg-info p-2 px-3 ">{{ __(App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 2)
                                                <span
                                                    class="badge fix_badges bg-secondary p-2 px-3 ">{{ __(App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 3)
                                                <span
                                                    class="badge fix_badges bg-warning p-2 px-3 ">{{ __(App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @elseif($invoice->status == 4)
                                                <span
                                                    class="badge fix_badges bg-danger p-2 px-3 ">{{ __(App\Models\Invoice::$statues[$invoice->status]) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table mb-0 pc-dt-simple" id="assets">
                            <thead>
                                <tr>
                                    <th> {{ __('Bill') }}</th>
                                    @if (!\Auth::user()->type != 'vendor')
                                        <th> {{ __('Vendor') }}</th>
                                    @endif
                                    <th> {{ __('Account Type') }}</th>
                                    <th> {{ __('Bill Date') }}</th>
                                    <th> {{ __('Due Date') }}</th>
                                    <th>{{ __('Due Amount') }}</th>
                                    <th>{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $bill)
                                    <tr class="font-style">
                                        <td class="Id">
                                            @permission('bill show')
                                                <a href="{{ route('bill.show', \Crypt::encrypt($bill->id)) }}"
                                                    class="btn btn-outline-primary">{{ Workdo\Account\Entities\Bill::billNumberFormat($bill->bill_id) }}</a>
                                            @else
                                                <a
                                                    class="btn btn-outline-primary">{{ Workdo\Account\Entities\Bill::billNumberFormat($bill->bill_id) }}</a>
                                    @endif
                                    </td>

                                    @if (!\Auth::user()->type != 'vendor')
                                        <td> {{ !empty($bill->vendor_name) ? $bill->vendor_name : '' }}</td>
                                    @endif
                                    <td>{{ $bill->account_type }}</td>
                                    <td>{{ company_date_formate($bill->bill_date) }}</td>
                                    <td>
                                        @if ($bill->due_date < date('Y-m-d'))
                                            <p class="text-danger">
                                                {{ company_date_formate($bill->due_date) }}</p>
                                        @else
                                            {{ company_date_formate($bill->due_date) }}
                                        @endif
                                    </td>
                                    <td>{{ currency_format_with_sym($bill->getDue()) }}</td>
                                    <td>
                                        @if ($bill->status == 0)
                                            <span
                                                class="badge fix_badges bg-primary p-2 px-3 bill_status">{{ __(Workdo\Account\Entities\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 1)
                                            <span
                                                class="badge fix_badges bg-info p-2 px-3 bill_status">{{ __(Workdo\Account\Entities\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 2)
                                            <span
                                                class="badge fix_badges bg-secondary p-2 px-3 bill_status">{{ __(Workdo\Account\Entities\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 3)
                                            <span
                                                class="badge fix_badges bg-warning p-2 px-3 bill_status">{{ __(Workdo\Account\Entities\Bill::$statues[$bill->status]) }}</span>
                                        @elseif($bill->status == 4)
                                            <span
                                                class="badge fix_badges bg-danger p-2 px-3 bill_status">{{ __(Workdo\Account\Entities\Bill::$statues[$bill->status]) }}</span>
                                        @endif
                                    </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    @endif
    </div>
    </div>
