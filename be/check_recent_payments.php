<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

echo "=== DONG_GOPS IN DB ===\n";
$dongGops = DB::table('dong_gops')->orderBy('id', 'desc')->get();
foreach ($dongGops as $dg) {
    echo "ID: {$dg->id} | User ID: {$dg->nguoi_dung_id} | Content: {$dg->noi_dung} | Status: {$dg->trang_thai} | Created At: {$dg->created_at}\n";
}

echo "\n=== RECENT SEPAY TRANSACTIONS ===\n";
$apiToken = env('SEPAY_API_TOKEN');
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $apiToken,
])->get('https://my.sepay.vn/userapi/transactions/list', ['limit' => 10]);

if ($response->successful()) {
    $transactions = $response->json('transactions') ?? [];
    foreach ($transactions as $tx) {
        $amount = $tx['amount_in'] ?? $tx['amount'] ?? 0;
        echo "Tx ID: {$tx['id']} | Content: {$tx['transaction_content']} | Amount: {$amount} | Date: {$tx['transaction_date']}\n";
    }
} else {
    echo "Failed to connect to SePay: " . $response->body() . "\n";
}
