<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Inisialisasi Faker
        $faker = Faker::create('id_ID'); // Menggunakan lokal Indonesia untuk data yang lebih relevan

        // Ambil semua ID kategori yang ada
        $categoryIds = Category::pluck('id')->toArray();

        // Jika tidak ada kategori, buat setidaknya satu kategori dummy
        if (empty($categoryIds)) {
            $this->command->info('No categories found. Creating a dummy category...');
            $dummyCategory = Category::create([
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'description' => 'Default category for products without a specific category.',
            ]);
            $categoryIds[] = $dummyCategory->id;
        }

        // Buat 50 produk dummy
        for ($i = 0; $i < 10; $i++) {
            Product::create([
                'name' => $faker->words(rand(2, 4), true), // Nama produk acak 2-4 kata
                'description' => $faker->paragraph(rand(3, 7)), // Deskripsi produk acak 3-7 paragraf
                'sku' => $faker->unique()->ean13(), // SKU unik menggunakan EAN13
                'price' => $faker->randomFloat(2, 10000, 1000000), // Harga antara 10.000 - 1.000.000
                'stock' => $faker->numberBetween(0, 500), // Stok antara 0 - 500
                'category_id' => $faker->randomElement($categoryIds), // Pilih ID kategori secara acak dari yang sudah ada
            ]);
        }

        $this->command->info('Products seeded successfully!');
    }
}
