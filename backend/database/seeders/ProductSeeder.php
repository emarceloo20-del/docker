<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['name' => 'Camiseta básica algodón', 'category' => 'Ropa', 'price' => 19.99, 'stock' => 100],
            ['name' => 'Jeans slim fit', 'category' => 'Ropa', 'price' => 39.90, 'stock' => 60],
            ['name' => 'Chaqueta impermeable', 'category' => 'Ropa', 'price' => 79.50, 'stock' => 25],
            ['name' => 'Zapatillas running', 'category' => 'Calzado', 'price' => 59.99, 'stock' => 40],
            ['name' => 'Botas de cuero', 'category' => 'Calzado', 'price' => 89.00, 'stock' => 20],
            ['name' => 'Audífonos inalámbricos', 'category' => 'Electrónica', 'price' => 49.99, 'stock' => 75],
            ['name' => 'Smartwatch deportivo', 'category' => 'Electrónica', 'price' => 129.99, 'stock' => 30],
            ['name' => 'Cargador USB-C 65W', 'category' => 'Electrónica', 'price' => 24.99, 'stock' => 150],
            ['name' => 'Mochila para laptop', 'category' => 'Accesorios', 'price' => 34.99, 'stock' => 55],
            ['name' => 'Botella térmica 1L', 'category' => 'Accesorios', 'price' => 15.50, 'stock' => 90],
            ['name' => 'Lentes de sol polarizados', 'category' => 'Accesorios', 'price' => 22.00, 'stock' => 65],
            ['name' => 'Set de sartenes antiadherentes', 'category' => 'Hogar', 'price' => 65.00, 'stock' => 18],
            ['name' => 'Lámpara LED de escritorio', 'category' => 'Hogar', 'price' => 27.99, 'stock' => 45],
            ['name' => 'Organizador de closet', 'category' => 'Hogar', 'price' => 18.75, 'stock' => 70],
            ['name' => 'Balón de fútbol profesional', 'category' => 'Deportes', 'price' => 29.99, 'stock' => 40],
            ['name' => 'Yoga mat antideslizante', 'category' => 'Deportes', 'price' => 21.99, 'stock' => 85],
            ['name' => 'Mancuernas ajustables 10kg', 'category' => 'Deportes', 'price' => 55.00, 'stock' => 22],
            ['name' => 'Teclado mecánico RGB', 'category' => 'Electrónica', 'price' => 69.99, 'stock' => 35],
            ['name' => 'Mouse inalámbrico ergonómico', 'category' => 'Electrónica', 'price' => 19.99, 'stock' => 95],
            ['name' => 'Monitor 24" Full HD', 'category' => 'Electrónica', 'price' => 149.99, 'stock' => 15],
        ];

        foreach ($products as $index => $product) {
            Product::firstOrCreate(
                ['sku' => 'SKU-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']).'-'.($index + 1),
                    'description' => "Producto de ejemplo: {$product['name']}. Ideal para demostrar el catálogo del e-commerce.",
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'image_url' => 'https://picsum.photos/seed/'.($index + 1).'/400',
                    'category' => $product['category'],
                    'is_active' => true,
                ]
            );
        }
    }
}
