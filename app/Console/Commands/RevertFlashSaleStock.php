<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FlashSale;
use App\Models\ProductFlashSale;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RevertFlashSaleStock extends Command
{
    protected $signature = 'flashsale:revert-stock';
    protected $description = 'Kembalikan stok sisa flash sale yang sudah expired ke stok normal';

    public function handle()
    {
        $this->info('Cek flash sale yang sudah expired...');

        $expiredFlashSales = FlashSale::where('end_date', '<', Carbon::now())->get();

        if ($expiredFlashSales->isEmpty()) {
            $this->info('Tidak ada flash sale yang expired.');
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            foreach ($expiredFlashSales as $flashSale) {
                foreach ($flashSale->productFlashSales as $item) {
                    $variantSize = $item->variantSize;
                    if ($variantSize) {
                        $variantSize->quantity += $item->quantity;
                        $variantSize->save();
                    }
                }

                $flashSale->delete(); // Hapus flash sale setelah dikembalikan
            }

            DB::commit();
            $this->info('Berhasil mengembalikan stok dari flash sale.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Gagal: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
