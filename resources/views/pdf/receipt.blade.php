<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Receipt {{ $invoice->invoice_number }}</title>
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
    .header { display: flex; align-items: center; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; margin-bottom: 20px; }
    .header img { width: 64px; height: 64px; vertical-align: middle; }
    .school { display: inline-block; vertical-align: middle; margin-left: 10px; }
    .school h1 { margin: 0; font-size: 16px; color: #1f2937; }
    .school p { margin: 0; font-size: 10px; color: #6b7280; }
    .title { text-align: right; font-size: 22px; font-weight: bold; color: #4f46e5; }
    .meta { margin-top: 14px; }
    .meta td { padding: 3px 6px; }
    table.lines { width: 100%; border-collapse: collapse; margin-top: 18px; }
    table.lines th, table.lines td { border-bottom: 1px solid #e5e7eb; padding: 8px; text-align: left; }
    table.lines th { background: #f3f4f6; font-size: 11px; }
    .total { font-size: 16px; font-weight: bold; color: #16a34a; text-align: right; padding-top: 12px; }
    .footer { margin-top: 50px; font-size: 10px; color: #6b7280; text-align: center; }
    .badge-paid { display: inline-block; padding: 4px 10px; background: #dcfce7; color: #166534; border-radius: 999px; font-size: 11px; font-weight: bold; }
</style>
</head>
<body>
    <table class="header" style="width:100%; border:0;">
        <tr>
            <td style="width:70%;">
                @if ($logoData)
                    <img src="{{ $logoData }}" />
                @endif
                <span class="school">
                    <h1>{{ $school['name'] }}</h1>
                    @if ($school['address'])  <p>{{ $school['address'] }}</p> @endif
                    @if ($school['phone'] || $school['email'])
                        <p>{{ $school['phone'] }} · {{ $school['email'] }}</p>
                    @endif
                    @if ($school['reg_no'])
                        <p>{{ __('Reg. No.') }} {{ $school['reg_no'] }}</p>
                    @endif
                </span>
            </td>
            <td style="width:30%; text-align:right;" class="title">{{ __('RECEIPT') }}</td>
        </tr>
    </table>

    <table class="meta" style="width:100%;">
        <tr>
            <td><strong>{{ __('Receipt #') }}:</strong> {{ $invoice->invoice_number }}</td>
            <td><strong>{{ __('Issued') }}:</strong> {{ optional($invoice->paid_at)->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td><strong>{{ __('Teacher') }}:</strong> {{ $invoice->user->name ?? '—' }}</td>
            <td><strong>{{ __('Period') }}:</strong> {{ $invoice->period_month->format('M Y') }}</td>
        </tr>
    </table>

    <table class="lines">
        <thead>
            <tr>
                <th>{{ __('Description') }}</th>
                <th style="width:20%; text-align:right;">{{ __('Amount (RM)') }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ __('Monthly fee for') }} {{ $invoice->period_month->format('F Y') }}</td>
                <td style="text-align:right;">{{ number_format($invoice->amount, 2) }}</td>
            </tr>
            @if ($invoice->late_fee > 0)
                <tr>
                    <td>{{ __('Late fee') }}</td>
                    <td style="text-align:right;">{{ number_format($invoice->late_fee, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    <p class="total">{{ __('Total Paid') }}: RM {{ number_format($invoice->total, 2) }}
        <span class="badge-paid">{{ __('PAID') }}</span>
    </p>

    @if ($payment)
        <p style="font-size:11px; color:#374151; margin-top:18px;">
            <strong>{{ __('Method') }}:</strong> {{ $payment->method?->label() }}<br>
            @if ($payment->bayarcash_transaction_id)
                <strong>{{ __('Reference') }}:</strong> {{ $payment->bayarcash_transaction_id }}<br>
            @elseif ($payment->bayarcash_exchange_reference)
                <strong>{{ __('Reference') }}:</strong> {{ $payment->bayarcash_exchange_reference }}<br>
            @endif
        </p>
    @endif

    <div class="footer">
        @if (!empty($school['footer']))
            {{ $school['footer'] }}<br>
        @endif
        {{ __('Generated on') }} {{ now()->format('d M Y H:i') }}
    </div>
</body>
</html>
