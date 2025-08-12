<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = [
            [
                'name' => 'Diaria',
                'duration_days' => 1,
                'price' => 50.00,
                'description' => 'Acceso por un día completo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Semanal',
                'duration_days' => 7,
                'price' => 250.00,
                'description' => 'Acceso por una semana completa',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mensual',
                'duration_days' => 30,
                'price' => 800.00,
                'description' => 'Acceso por un mes completo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Anual',
                'duration_days' => 365,
                'price' => 8000.00,
                'description' => 'Acceso por un año completo con descuento',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('membership_types')->insert($membershipTypes);
    }
}
