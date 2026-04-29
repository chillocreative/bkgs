<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Storage;

class ReceiptController extends Controller
{
    public function download(Invoice $invoice)
    {
        $user = auth()->user();
        if (! $user) abort(401);
        if ($invoice->user_id !== $user->id && ! $user->isAdmin()) {
            throw new AuthorizationException();
        }
        if (! $invoice->isPaid()) {
            abort(404, 'Receipt is only available after payment.');
        }
        $invoice->load('successfulPayment', 'user');

        $logoPath = Setting::get('logo_small');
        $logoData = null;
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoFile = Storage::disk('public')->path($logoPath);
            $logoData = 'data:image/png;base64,'.base64_encode(@file_get_contents($logoFile));
        }

        $pdf = Pdf::loadView('pdf.receipt', [
            'invoice' => $invoice,
            'payment' => $invoice->successfulPayment,
            'school' => [
                'name' => Setting::get('school_name', config('app.name')),
                'address' => Setting::get('school_address'),
                'email' => Setting::get('school_email'),
                'phone' => Setting::get('school_phone'),
                'reg_no' => Setting::get('school_registration_number'),
                'footer' => Setting::get('receipt_footer'),
            ],
            'logoData' => $logoData,
        ])->setPaper('a4');

        return $pdf->download('Receipt-'.$invoice->invoice_number.'.pdf');
    }
}
