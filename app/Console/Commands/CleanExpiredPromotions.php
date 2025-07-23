<?php

namespace App\Console\Commands;

use App\Models\Promotion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredPromotions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'promotions:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus promosi yang sudah expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Cek promosi yang sudah expired...');

        $expiredPromotions = Promotion::where('end_date', '<', Carbon::now())->get();

        if ($expiredPromotions->isEmpty()) {
            $this->info('Tidak ada promosi yang expired.');
            return Command::SUCCESS;
        }

        foreach ($expiredPromotions as $promotion) {
            $this->info('Menghapus promosi: ' . $promotion->title);
            $promotion->update(['status' => 'inactive']);
            Storage::disk('public')->delete($promotion->thumbnail);
            $promotion->delete();
        }
    }
}
