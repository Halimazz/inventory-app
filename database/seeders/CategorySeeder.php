<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Product;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $categories = [
            'Elektronik',
            'Pakaian Pria',
            'Pakaian Wanita',
            'Makanan & Minuman',
            'Perlengkapan Rumah',
            'Otomotif',
            'Olahraga & Outdoor',
            'Kesehatan & Kecantikan',
            'Buku & Alat Tulis',
            'Mainan & Hobi',
        ];

        foreach ($categories as $categoryName) {
            Category::create([
                'name' => $categoryName,
                // Anda mungkin ingin menambahkan kolom 'slug' juga untuk SEO-friendly URLs.
                // Jika tidak ada di migrasi Anda, Anda bisa tambahkan kolom 'slug'
                // atau hapus baris ini jika memang tidak diperlukan.
                // 'slug' => Str::slug($categoryName),
            ]);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
