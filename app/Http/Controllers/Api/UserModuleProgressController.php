<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleProgress;
use Illuminate\Http\Request;
use App\Models\UserModuleProgress;
use App\Models\UserModuleItemProgress;
use App\Models\UserCourseEnrollment;
use App\Models\ModuleItem;

class UserModuleProgressController extends Controller
{
    // 1️⃣ Start a module
    public function startModule(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'course_id' => 'required|exists:courses,id',
        ]);


        $progress = UserModuleProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'module_id' => $request->module_id,
            ],
            [
                'course_id' => $request->course_id,
                'is_completed' => false,
                'progress_percentage' => 0,
                'time_spent_seconds' => 0,
            ]
        );

        return response()->api('Module started successfully', new ModuleProgress($progress));}


       //3ayza 23mel code y7sb overall prog
        public function overallProgress()
         {
             $user = auth()->user();

    // كل تقدم الموديولات
           $modulesProgress = UserModuleProgress::where('user_id', $user->id)->get();

         $totalModules = $modulesProgress->count();

        $completedModules = $modulesProgress->where('is_completed', true)->count();

        $overallPercentage = $totalModules > 0
         ? round(($completedModules / $totalModules) * 100, 2)
        : 0;

    $totalTimeSpent = $modulesProgress->sum('time_spent_seconds');

    return response()->api('Overall progress retrieved successfully', [
        'total_modules' => $totalModules,
        'completed_modules' => $completedModules,
        'overall_progress_percentage' => $overallPercentage,
        'total_time_spent_seconds' => $totalTimeSpent,
    ]);
}

    // 2️⃣ Update module progress
    public function updateModuleProgress(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'progress_percentage' => 'required|numeric|min:0|max:100',
            'time_spent_seconds' => 'nullable|numeric|min:0',
        ]);

        $progress = UserModuleProgress::where('user_id', $user->id)
            ->where('module_id', $request->module_id)
            ->first();

        if (!$progress) {
            return response()->api('Module progress not found', null, 404);
        }

        $progress->progress_percentage = $request->progress_percentage;
        if ($request->filled('time_spent_seconds')) {
            $progress->time_spent_seconds = $request->time_spent_seconds;
        }

        $progress->is_completed = $request->progress_percentage >= 100;
        $progress->save();

        return response()->api('Module progress updated successfully', $progress);
    }

    // 3️⃣ Update module item progress
    public function updateModuleItemProgress(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'module_item_id' => 'required|exists:module_items,id',
            'module_id' => 'required|exists:modules,id',
            'watched_duration_seconds' => 'required|numeric|min:0',
            'is_completed' => 'nullable|boolean',
        ]);

        $itemProgress = UserModuleItemProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'module_item_id' => $request->module_item_id,
            ],
            [
                'module_id' => $request->module_id,
                'watched_duration_seconds' => $request->watched_duration_seconds,
                'is_completed' => $request->boolean('is_completed', false),
            ]
        );

        return response()->api('Module item progress updated successfully', $itemProgress);
    }

    // 4️⃣ Get my module progress
    public function myModuleProgress()
{

    $user = auth()->user();

    $progress = UserModuleProgress::where('user_id', $user->id)
        ->with('module') // مع withDefault
        ->get();

    $data = $progress->map(function($p) {
        return [
            'id' => $p->id,
            'module_id' => $p->module_id,
            'module_title' => $p->module->title ?? 'Module Deleted',
            'progress_percentage' => $p->progress_percentage,
            'is_completed' => $p->is_completed,
        ];
    });

    return response()->api('My module progress retrieved successfully', $data);
}

    // 6️⃣ Simple mark module/item as complete (used by Flutter app)
    public function markComplete(Request $request)
    {
        $user = auth()->user();

        // NOTE: Flutter sends module_id but it is actually a ModuleItem ID.
        $request->validate([
            'module_id' => 'required|integer',
            'status' => 'nullable|string',
        ]);

        $moduleItem = ModuleItem::with(['module'])->find($request->module_id);

        if (!$moduleItem) {
            return response()->api('Module item not found', null, 404);
        }

        $parentModuleId = $moduleItem->module_id;
        $courseId = $moduleItem->module?->course_id;

        // Update or create item progress record
        UserModuleItemProgress::updateOrCreate(
            [
                'user_id'        => $user->id,
                'module_item_id' => $moduleItem->id,
            ],
            [
                'module_id'               => $parentModuleId,
                'is_completed'            => true,
                'watched_duration_seconds'=> 0,
            ]
        );

        // Ensure parent module progress exists then mark complete
        if ($courseId) {
            UserModuleProgress::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'module_id' => $parentModuleId,
                ],
                [
                    'course_id' => $courseId,
                    'is_completed' => false,
                    'progress_percentage' => 0,
                    'time_spent_seconds' => 0,
                ]
            );

            UserModuleProgress::where('user_id', $user->id)
                ->where('module_id', $parentModuleId)
                ->update(['is_completed' => true, 'progress_percentage' => 100]);
        }

        // Award XP once per module_item (video/pdf only)
        $type = strtolower((string)($moduleItem->type ?? 'video'));
        if (in_array($type, ['pdf', 'document'])) {
            \App\Services\GamificationService::awardXp($user, \App\Services\GamificationService::XP_PDF_COMPLETE, 'module_item_complete', 'module_item', (int)$moduleItem->id);
            \App\Services\GamificationService::awardCoins($user, \App\Services\GamificationService::COINS_PDF_COMPLETE, 'module_item_complete', 'module_item', (int)$moduleItem->id);
        } elseif ($type === 'video') {
            \App\Services\GamificationService::awardXp($user, \App\Services\GamificationService::XP_VIDEO_COMPLETE, 'module_item_complete', 'module_item', (int)$moduleItem->id);
            \App\Services\GamificationService::awardCoins($user, \App\Services\GamificationService::COINS_VIDEO_COMPLETE, 'module_item_complete', 'module_item', (int)$moduleItem->id);
        }

        // Recalculate and update enrollment progress_percentage (based on ITEMS)
        if ($courseId) {
            $totalItems = ModuleItem::whereHas('module', fn($q) => $q->where('course_id', $courseId))->count();
            $completedItems = UserModuleItemProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->whereHas('moduleItem', fn($q) => $q->whereHas('module', fn($q2) => $q2->where('course_id', $courseId)))
                ->count();
            $newProgress = $totalItems > 0 ? round($completedItems / $totalItems * 100) : 0;

            UserCourseEnrollment::where('user_id', $user->id)
                ->where('course_id', $courseId)
                ->update(['progress_percentage' => $newProgress]);
        }

        return response()->api('Module marked as complete', [
            'module_item_id' => (int)$moduleItem->id,
            'course_id' => $courseId,
            'xp' => (int)($user->fresh()->xp ?? 0),
            'coins' => (int)($user->fresh()->coins ?? 0),
        ]);
    }

// 5️⃣ Get my module item progress
    public function myModuleItemProgress()
    {
        $user = auth()->user();

        $progress = UserModuleItemProgress::where('user_id', $user->id)
            ->with([
                'module' => fn($q) => $q->withDefault(),
                'moduleItem' => fn($q) => $q->withDefault()
            ])
            ->get();

        return response()->api('My module item progress retrieved successfully', $progress);
    }


    // ✅ New: complete a module item by URL param (recommended for mobile)
    public function completeModuleItem(string $moduleItemId)
    {
        $request = request();
        $request->merge(['module_id' => (int)$moduleItemId]);
        return $this->markComplete($request);
    }
}
