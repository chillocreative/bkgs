<?php

namespace Database\Seeders;

use App\Models\WhatsappTemplate;
use Illuminate\Database\Seeder;

class WhatsappTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'invoice_issued',
                'name' => 'Invoice Issued',
                'body_template' => "Salam {{ teacher_name }},\n\nInvois bulan *{{ month }}* berjumlah *RM {{ amount }}* telah dikeluarkan.\nTarikh akhir bayaran: *{{ due_date }}*.\nNo. Invois: {{ invoice_number }}\n\nSila log masuk untuk membayar:\n{{ pay_url }}\n\nTerima kasih,\n{{ school_name }}",
            ],
            [
                'key' => 'payment_received',
                'name' => 'Payment Received',
                'body_template' => "Terima kasih {{ teacher_name }}!\n\nPembayaran *RM {{ amount }}* untuk invois *{{ invoice_number }}* telah berjaya diterima pada {{ paid_at }}.\n\nResit boleh dimuat turun melalui akaun anda.\n\n{{ school_name }}",
            ],
            [
                'key' => 'payment_reminder',
                'name' => 'Payment Reminder',
                'body_template' => "Salam {{ teacher_name }},\n\nPeringatan: Invois *{{ invoice_number }}* sebanyak *RM {{ amount }}* akan tamat tempoh pada *{{ due_date }}*.\n\nSila buat pembayaran:\n{{ pay_url }}\n\nTerima kasih,\n{{ school_name }}",
            ],
            [
                'key' => 'payment_overdue',
                'name' => 'Payment Overdue',
                'body_template' => "Salam {{ teacher_name }},\n\nInvois *{{ invoice_number }}* sebanyak *RM {{ amount }}* telah melepasi tarikh akhir ({{ due_date }}).\n\nSila buat pembayaran segera:\n{{ pay_url }}\n\nTerima kasih,\n{{ school_name }}",
            ],
            [
                'key' => 'manual_reminder',
                'name' => 'Manual Reminder',
                'body_template' => "Salam {{ teacher_name }},\n\nIni adalah peringatan untuk invois *{{ invoice_number }}* sebanyak *RM {{ amount }}*.\nTarikh akhir: {{ due_date }}\n\nSila log masuk untuk lebih maklumat:\n{{ pay_url }}\n\nTerima kasih,\n{{ school_name }}",
            ],
        ];

        foreach ($templates as $t) {
            WhatsappTemplate::updateOrCreate(['key' => $t['key']], $t + ['is_active' => true]);
        }
    }
}
