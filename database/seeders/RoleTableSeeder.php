<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'user', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'reseller', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'guest', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'affiliator', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
