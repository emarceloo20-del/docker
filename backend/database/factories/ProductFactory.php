<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name).'-'.Str::random(6),
            'description' => fake()->paragraph(),
            'price' => fake()->randomFloat(2, 5, 200),
            'stock' => fake()->numberBetween(0, 200),
            'sku' => 'SKU-'.strtoupper(Str::random(8)),
            'image_url' => 'https://picsum.photos/seed/'.fake()->uuid().'/400',
            'category' => fake()->randomElement(['Ropa', 'Calzado', 'Electrónica', 'Hogar', 'Deportes', 'Accesorios']),
            'is_active' => true,
        ];
    }
}
