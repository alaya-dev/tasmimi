<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('terms_of_service', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // عنوان اتفاقية الاستخدام
            $table->longText('content'); // محتوى الاتفاقية بتنسيق HTML
            $table->boolean('is_active')->default(true); // هل الاتفاقية نشطة
            $table->string('version')->default('1.0'); // إصدار الاتفاقية
            $table->timestamp('effective_date')->nullable(); // تاريخ سريان الاتفاقية
            $table->foreignId('created_by')->constrained('users'); // من أنشأ الاتفاقية
            $table->foreignId('updated_by')->nullable()->constrained('users'); // من حدث الاتفاقية
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_of_service');
    }
};
