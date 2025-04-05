<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2 contractors
        User::factory()->count(2)->create([
            'user_type' => 'contractor',
        ]);

        //5 freelancers
        User::factory()->count(5)->create([
            'user_type' => 'freelancer',
        ]);
    }
}
