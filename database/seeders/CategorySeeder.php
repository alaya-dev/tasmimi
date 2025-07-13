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
        // Nouvelles catégories selon les spécifications
        $categories = [
            'دعوة مناسبات عامة',
            'دعوة خاصة',
            'زواج',
            'خطبة',
            'نجاح',
            'تخرج',
            'مولود',
            'حفل',
            'شكر وتقدير',
            'تهنئة',
            'غلاف',
            'رمضان',
            'عيد الفطر',
            'عيد الأضحى',
            'اليوم الوطني',
            'يوم التأسيس'
        ];

        // Supprimer les anciennes catégories de manière sécurisée
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        foreach ($categories as $category) {
            Category::create([
                'name' => $category,
            ]);
        }
    }
}
