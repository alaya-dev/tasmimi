<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contacts = [
            [
                'name' => 'Ahmed Al-Rashid',
                'email' => 'ahmed@example.com',
                'content' => 'مرحباً، أواجه مشكلة في تصميم البطاقة الذكية الخاصة بي. لا يمكنني حفظ التغييرات التي أقوم بها. هل يمكنكم مساعدتي؟',
                'status' => 'pending',
                'created_at' => now()->subDays(2),
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'content' => 'Hello, I am having trouble with the smart card design platform. The templates are not loading properly. Could you please help me resolve this issue?',
                'status' => 'in_progress',
                'created_at' => now()->subDays(1),
            ],
            [
                'name' => 'Mohammed Hassan',
                'email' => 'mohammed@example.com',
                'content' => 'السلام عليكم، أريد أن أستفسر عن إمكانية إضافة المزيد من القوالب للبطاقات الذكية. هل هناك خطط لإضافة قوالب جديدة قريباً؟',
                'status' => 'resolved',
                'created_at' => now()->subHours(12),
            ],
            [
                'name' => 'Emily Davis',
                'email' => 'emily@example.com',
                'content' => 'Hi there! I love the platform but I think it would be great to have more customization options for the smart cards. Are there any plans to add more design tools?',
                'status' => 'pending',
                'created_at' => now()->subHours(6),
            ],
            [
                'name' => 'Omar Al-Zahra',
                'email' => 'omar@example.com',
                'content' => 'مرحباً، أواجه صعوبة في فهم كيفية استخدام أدوات التصميم المتقدمة. هل يمكنكم توفير دليل مستخدم أو فيديوهات تعليمية؟',
                'status' => 'in_progress',
                'created_at' => now()->subHours(3),
            ],
        ];

        foreach ($contacts as $contact) {
            Contact::create($contact);
        }
    }
}
