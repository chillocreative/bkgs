<?php

use App\Livewire\Admin;
use App\Livewire\PayInvoice;
use App\Livewire\PaymentReturn;
use App\Livewire\Teacher;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/invoices', Teacher\InvoicesIndex::class)->name('teacher.invoices.index');
    Route::get('/invoices/{invoice}', Teacher\InvoiceShow::class)->name('teacher.invoices.show');
    Route::get('/invoices/{invoice}/receipt', [\App\Http\Controllers\ReceiptController::class, 'download'])->name('teacher.invoices.receipt');
    Route::get('/pay/{invoice}', PayInvoice::class)->name('payment.pay');
    Route::get('/payment/return', PaymentReturn::class)->name('payment.return');
});

Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');

    Route::get('/teachers', Admin\Teachers\Index::class)->name('teachers.index');
    Route::get('/teachers/create', Admin\Teachers\Form::class)->name('teachers.create');
    Route::get('/teachers/{user}/edit', Admin\Teachers\Form::class)->name('teachers.edit');
    Route::get('/teachers/{user}', Admin\Teachers\Show::class)->name('teachers.show');
    Route::get('/teachers-import', Admin\Teachers\BulkImport::class)->name('teachers.import');

    Route::get('/fee-structures', Admin\FeeStructures\Index::class)->name('fee-structures.index');

    Route::get('/invoices', Admin\Invoices\Index::class)->name('invoices.index');
    Route::get('/invoices/generate', Admin\Invoices\GenerateMonthly::class)->name('invoices.generate');
    Route::get('/invoices/{invoice}', Admin\Invoices\Show::class)->name('invoices.show');

    Route::get('/payments', Admin\Payments\Index::class)->name('payments.index');
    Route::get('/payments/record/{invoice}', Admin\Payments\RecordManual::class)->name('payments.record');

    Route::get('/notifications', Admin\NotificationsLog::class)->name('notifications.index');

    Route::get('/templates', Admin\Settings\WhatsappTemplates::class)->name('templates.index');

    Route::get('/settings', Admin\Settings\General::class)->name('settings.general');
    Route::get('/settings/branding', Admin\Settings\Branding::class)->name('settings.branding');

    Route::get('/reports/monthly', [\App\Http\Controllers\ReportController::class, 'monthly'])->name('reports.monthly');
});

Route::middleware(['auth', 'role:super-admin'])->prefix('admin/settings')->name('admin.settings.')->group(function () {
    Route::get('/bayarcash', Admin\Settings\Bayarcash::class)->name('bayarcash');
    Route::get('/sendora', Admin\Settings\Sendora::class)->name('sendora');
});

require __DIR__.'/auth.php';
