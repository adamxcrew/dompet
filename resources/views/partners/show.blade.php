@extends('layouts.settings')

@section('title', __('partner.transactions'))

@section('content_settings')

<div class="page-header">
    <h1 class="page-title">{{ $partner->name }}</h1>
    <div class="page-subtitle">{{ __('partner.transactions') }}</div>
    <div class="page-options d-flex">
        {{ link_to_route('partners.index', __('partner.back_to_index'), [], ['class' => 'btn btn-secondary float-right']) }}
    </div>
</div>

@include('transactions.partials.stats')

@if ($partner->description)
    <div class="alert alert-info"><strong>{{ __('app.description') }}:</strong><br>{{ $partner->description }}</div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card table-responsive">
            <div class="card-header">
                @include('partners.partials.show_filter')
            </div>
            @desktop
            <table class="table table-sm table-responsive-sm table-hover table-bordered mb-0">
                <thead>
                    <tr>
                        <th class="text-center col-md-1">{{ __('app.table_no') }}</th>
                        <th class="text-center col-md-2">{{ __('app.date') }}</th>
                        <th class="col-md-7">{{ __('transaction.description') }}</th>
                        <th class="text-right col-md-2">{{ __('transaction.amount') }}</th>
                        <th class="text-center">{{ __('app.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $key => $transaction)
                    <tr>
                        <td class="text-center">{{ 1 + $key }}</td>
                        <td class="text-center">{{ $transaction->date }}</td>
                        <td>
                            {{ $transaction->description }}
                            <div class="float-right">
                                {!! optional($transaction->category)->name_label !!}
                                {!!optional($transaction->loan)->type_label !!}
                            </div>
                        </td>
                        <td class="text-right">{{ $transaction->amount_string }}</td>
                        <td class="text-center">
                            @can('update', $transaction)
                                {!! link_to_route(
                                    'partners.show',
                                    __('app.edit'),
                                    [$partner->id, 'action' => 'edit', 'id' => $transaction->id] + request(['start_date', 'end_date', 'query', 'category_id']),
                                    ['id' => 'edit-transaction-'.$transaction->id]
                                ) !!}
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5">{{ __('transaction.not_found') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">{{ __('app.total') }}</th>
                        <th class="text-right">
                            {{ format_number($transactions->sum(function ($transaction) {
                                return $transaction->in_out ? $transaction->amount : -$transaction->amount;
                            })) }}
                        </th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            @elsedesktop
            <div class="card-body">
                @foreach ($transactions as $transaction)
                    @include('partners.partials.single_transaction_mobile', ['transaction' => $transaction])
                @endforeach
            </div>
            @enddesktop
        </div>
    </div>
</div>
@if(Request::has('action'))
@include('partners.partials.transaction-forms')
@endif
@endsection

@section('styles')
    {{ Html::style(url('css/plugins/bootstrap-colorpicker.min.css')) }}
    {{ Html::style(url('css/plugins/jquery.datetimepicker.css')) }}
@endsection

@push('scripts')
    {{ Html::script(url('js/plugins/bootstrap-colorpicker.min.js')) }}
    {{ Html::script(url('js/plugins/jquery.datetimepicker.js')) }}
    {{ Html::script(url('js/plugins/number-format.js')) }}
<script>
(function () {
    $('#transactionModal').modal({
        show: true,
        backdrop: 'static',
    });
    $('.date-select').datetimepicker({
        timepicker:false,
        format:'Y-m-d',
        closeOnDateSelect: true,
        scrollInput: false,
        dayOfWeekStart: 1
    });
    initNumberFormatter('#amount');
})();
</script>
@endpush
