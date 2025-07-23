<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Untuk dummy user
        User::factory(10)->create()->each(function ($user) {
            UserProfile::create([
                'user_id' => $user->id,
                'address' => 'Alamat dummy',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta Selatan',
                'subdistrict' => 'Kebayoran Baru',
                'postal_code' => '12140',
                'birth_date' => Carbon::now()->subYears(rand(20, 40))->subDays(rand(0, 365)),
            ]);
        });

        $this->call([
            RoleTableSeeder::class,
        ]);
    }
}
