<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // Bebidas
            [
                'name' => 'AGUA MINERAL 500ML',
                'description' => 'Agua mineral natural en botella de 500ml',
                'price' => 15.00,
                'stock' => 100,
                'category' => 'Bebidas',
                'is_active' => true
            ],
            [
                'name' => 'BEBIDA ENERGÉTICA MONSTER',
                'description' => 'Bebida energética Monster 473ml',
                'price' => 35.00,
                'stock' => 50,
                'category' => 'Bebidas',
                'is_active' => true
            ],
            [
                'name' => 'BEBIDA ENERGÉTICA RED BULL',
                'description' => 'Bebida energética Red Bull 250ml',
                'price' => 45.00,
                'stock' => 30,
                'category' => 'Bebidas',
                'is_active' => true
            ],
            [
                'name' => 'PROTEÍNA EN POLVO WHEY',
                'description' => 'Proteína en polvo Whey 1kg',
                'price' => 850.00,
                'stock' => 25,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'CREATINA MONOHIDRATO',
                'description' => 'Creatina monohidrato 300g',
                'price' => 450.00,
                'stock' => 20,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'BCAA AMINOÁCIDOS',
                'description' => 'BCAA aminoácidos ramificados 300g',
                'price' => 380.00,
                'stock' => 15,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'GLOVES DE ENTRENAMIENTO',
                'description' => 'Guantes de entrenamiento con agarre antideslizante',
                'price' => 180.00,
                'stock' => 40,
                'category' => 'Accesorios',
                'is_active' => true
            ],
            [
                'name' => 'CINTURÓN DE LEVANTAMIENTO',
                'description' => 'Cinturón de cuero para levantamiento de pesas',
                'price' => 320.00,
                'stock' => 12,
                'category' => 'Accesorios',
                'is_active' => true
            ],
            [
                'name' => 'TOALLA MICROFIBRA',
                'description' => 'Toalla de microfibra para gimnasio 30x60cm',
                'price' => 95.00,
                'stock' => 60,
                'category' => 'Accesorios',
                'is_active' => true
            ],
            [
                'name' => 'CAMISETA DEPORTIVA',
                'description' => 'Camiseta deportiva de secado rápido',
                'price' => 280.00,
                'stock' => 35,
                'category' => 'Ropa Deportiva',
                'is_active' => true
            ],
            [
                'name' => 'PANTS DEPORTIVOS',
                'description' => 'Pants deportivos con bolsillos laterales',
                'price' => 420.00,
                'stock' => 28,
                'category' => 'Ropa Deportiva',
                'is_active' => true
            ],
            [
                'name' => 'TENIS DEPORTIVOS',
                'description' => 'Tenis deportivos para entrenamiento',
                'price' => 650.00,
                'stock' => 18,
                'category' => 'Ropa Deportiva',
                'is_active' => true
            ],
            [
                'name' => 'BANDA DE RESISTENCIA',
                'description' => 'Banda de resistencia elástica multinivel',
                'price' => 120.00,
                'stock' => 45,
                'category' => 'Equipamiento',
                'is_active' => true
            ],
            [
                'name' => 'ROLLO DE ESPUMA',
                'description' => 'Rollo de espuma para masaje muscular',
                'price' => 180.00,
                'stock' => 22,
                'category' => 'Equipamiento',
                'is_active' => true
            ],
            [
                'name' => 'BOTELLA DE AGUA 1L',
                'description' => 'Botella de agua reutilizable de 1 litro',
                'price' => 85.00,
                'stock' => 55,
                'category' => 'Equipamiento',
                'is_active' => true
            ],
            [
                'name' => 'PROTEÍNA EN POLVO CASEÍNA',
                'description' => 'Proteína caseína en polvo 500g',
                'price' => 520.00,
                'stock' => 8,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'VITAMINAS MULTIVITAMÍNICAS',
                'description' => 'Complejo multivitamínico 60 cápsulas',
                'price' => 280.00,
                'stock' => 32,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'OMEGA 3',
                'description' => 'Suplemento de Omega 3 90 cápsulas',
                'price' => 320.00,
                'stock' => 25,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'PRE-ENTRENO',
                'description' => 'Pre-entreno en polvo 300g',
                'price' => 580.00,
                'stock' => 15,
                'category' => 'Suplementos',
                'is_active' => true
            ],
            [
                'name' => 'BARRAS DE PROTEÍNA',
                'description' => 'Barras de proteína 12 unidades',
                'price' => 180.00,
                'stock' => 40,
                'category' => 'Suplementos',
                'is_active' => true
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        $this->command->info('✅ Productos creados exitosamente');
    }
} 