<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\ModuleItem;
use Illuminate\Database\Seeder;

class ModuleItemSeeder extends Seeder
{
    public function run(): void
    {
        $modules = Module::all();

        $itemTemplates = [
            [
                'type' => 'video',
                'title' => ['en' => 'Welcome & Overview', 'ar' => 'الترحيب والنظرة العامة'],
                'content' => ['en' => '', 'ar' => ''],
                'file_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4',
                'duration' => 15,
            ],
            [
                'type' => 'video',
                'title' => ['en' => 'Main Lesson', 'ar' => 'الدرس الرئيسي'],
                'content' => ['en' => '', 'ar' => ''],
                'file_url' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'duration' => 60,
            ],
            [
                'type' => 'document',
                'title' => ['en' => 'Study Materials', 'ar' => 'مواد الدراسة'],
                'content' => ['en' => '', 'ar' => ''],
                'file_url' => 'https://example.com/docs/materials.pdf',
                'duration' => 0,
            ],
            [
                'type' => 'text',
                'title' => ['en' => 'Summary & Key Points', 'ar' => 'الملخص والنقاط الرئيسية'],
                'content' => ['en' => 'This is the summary of the module with key takeaways and important notes to remember.', 'ar' => 'هذا ملخص الموديول مع النقاط الرئيسية والملاحظات المهمة للتذكر.'],
                'file_url' => null,
                'duration' => 0,
            ],
        ];

        foreach ($modules as $module) {
            $itemCount = rand(2, 4);
            
            for ($i = 0; $i < $itemCount; $i++) {
                $template = $itemTemplates[$i % count($itemTemplates)];
                
                ModuleItem::create([
                    'module_id' => $module->id,
                    'type' => $template['type'],
                    'title' => $template['title'],
                    'content' => $template['content'],
                    'file_url' => $template['file_url'],
                    'duration' => $template['duration'],
                    'order' => $i + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}

