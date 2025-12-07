<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => ['en' => 'Technology', 'ar' => 'التقنية'],
                'description' => ['en' => 'Technology and IT related courses', 'ar' => 'دورات التقنية وتكنولوجيا المعلومات'],
                'slug' => 'technology',
                'order' => 1,
                'is_active' => true,
                'children' => [
                    ['name' => ['en' => 'Web Development', 'ar' => 'تطوير الويب'], 'slug' => 'web-development'],
                    ['name' => ['en' => 'Mobile Development', 'ar' => 'تطوير التطبيقات'], 'slug' => 'mobile-development'],
                    ['name' => ['en' => 'Data Science', 'ar' => 'علم البيانات'], 'slug' => 'data-science'],
                    ['name' => ['en' => 'Cybersecurity', 'ar' => 'الأمن السيبراني'], 'slug' => 'cybersecurity'],
                ],
            ],
            [
                'name' => ['en' => 'Business', 'ar' => 'الأعمال'],
                'description' => ['en' => 'Business and management courses', 'ar' => 'دورات الأعمال والإدارة'],
                'slug' => 'business',
                'order' => 2,
                'is_active' => true,
                'children' => [
                    ['name' => ['en' => 'Marketing', 'ar' => 'التسويق'], 'slug' => 'marketing'],
                    ['name' => ['en' => 'Finance', 'ar' => 'المالية'], 'slug' => 'finance'],
                    ['name' => ['en' => 'Entrepreneurship', 'ar' => 'ريادة الأعمال'], 'slug' => 'entrepreneurship'],
                    ['name' => ['en' => 'Project Management', 'ar' => 'إدارة المشاريع'], 'slug' => 'project-management'],
                ],
            ],
            [
                'name' => ['en' => 'Design', 'ar' => 'التصميم'],
                'description' => ['en' => 'Design and creative courses', 'ar' => 'دورات التصميم والإبداع'],
                'slug' => 'design',
                'order' => 3,
                'is_active' => true,
                'children' => [
                    ['name' => ['en' => 'Graphic Design', 'ar' => 'التصميم الجرافيكي'], 'slug' => 'graphic-design'],
                    ['name' => ['en' => 'UI/UX Design', 'ar' => 'تصميم واجهات المستخدم'], 'slug' => 'ui-ux-design'],
                    ['name' => ['en' => '3D & Animation', 'ar' => 'الرسوم المتحركة والـ 3D'], 'slug' => '3d-animation'],
                ],
            ],
            [
                'name' => ['en' => 'Languages', 'ar' => 'اللغات'],
                'description' => ['en' => 'Language learning courses', 'ar' => 'دورات تعلم اللغات'],
                'slug' => 'languages',
                'order' => 4,
                'is_active' => true,
                'children' => [
                    ['name' => ['en' => 'English', 'ar' => 'الإنجليزية'], 'slug' => 'english'],
                    ['name' => ['en' => 'Arabic', 'ar' => 'العربية'], 'slug' => 'arabic'],
                    ['name' => ['en' => 'French', 'ar' => 'الفرنسية'], 'slug' => 'french'],
                ],
            ],
            [
                'name' => ['en' => 'Personal Development', 'ar' => 'التطوير الشخصي'],
                'description' => ['en' => 'Self improvement and soft skills', 'ar' => 'تحسين الذات والمهارات الناعمة'],
                'slug' => 'personal-development',
                'order' => 5,
                'is_active' => true,
                'children' => [
                    ['name' => ['en' => 'Leadership', 'ar' => 'القيادة'], 'slug' => 'leadership'],
                    ['name' => ['en' => 'Communication', 'ar' => 'التواصل'], 'slug' => 'communication'],
                    ['name' => ['en' => 'Productivity', 'ar' => 'الإنتاجية'], 'slug' => 'productivity'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create($categoryData);

            foreach ($children as $index => $child) {
                Category::create([
                    'name' => $child['name'],
                    'slug' => $child['slug'],
                    'parent_id' => $category->id,
                    'order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }
}

