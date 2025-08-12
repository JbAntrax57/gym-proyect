<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Membership;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $memberships = [
            [
                'name' => 'Membresía Semanal',
                'type' => 'Semanal',
                'duration' => 7,
                'price' => 25.00,
                'status' => 'Activa',
                'description' => 'Acceso ilimitado por una semana',
                'max_visits' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Membresía Mensual',
                'type' => 'Mensual',
                'duration' => 30,
                'price' => 80.00,
                'status' => 'Activa',
                'description' => 'Acceso ilimitado por un mes',
                'max_visits' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Membresía Trimestral',
                'type' => 'Mensual',
                'duration' => 90,
                'price' => 220.00,
                'status' => 'Activa',
                'description' => 'Acceso ilimitado por tres meses con descuento',
                'max_visits' => 90,
                'is_active' => true,
            ],
            [
                'name' => 'Membresía Anual',
                'type' => 'Mensual',
                'duration' => 365,
                'price' => 800.00,
                'status' => 'Activa',
                'description' => 'Acceso ilimitado por un año con descuento especial',
                'max_visits' => 365,
                'is_active' => true,
            ],
            [
                'name' => 'Pase por Visita',
                'type' => 'Visita',
                'duration' => 1,
                'price' => 5.00,
                'status' => 'Activa',
                'description' => 'Acceso por una sola visita',
                'max_visits' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Paquete 10 Visitas',
                'type' => 'Visita',
                'duration' => 10,
                'price' => 40.00,
                'status' => 'Activa',
                'description' => 'Paquete de 10 visitas con descuento',
                'max_visits' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Membresía Semanal Premium',
                'type' => 'Semanal',
                'duration' => 7,
                'price' => 35.00,
                'status' => 'Activa',
                'description' => 'Acceso premium con clases especiales incluidas',
                'max_visits' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Membresía Mensual Premium',
                'type' => 'Mensual',
                'duration' => 30,
                'price' => 120.00,
                'status' => 'Activa',
                'description' => 'Acceso premium mensual con todas las clases incluidas',
                'max_visits' => 30,
                'is_active' => true,
            ],
        ];

        foreach ($memberships as $membershipData) {
            Membership::create($membershipData);
        }

        $this->command->info('Membresías sembradas correctamente.');
    }
}
