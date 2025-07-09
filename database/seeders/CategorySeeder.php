<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $categories = [
            'زواج',
            'تهنئة',
            'دبلوم',
            'عيد ميلاد',
            'تخرج',
            'عيد',
            'مناسبة خاصة',
            'عمل',
            'دعوة',
            'شكر',
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}
