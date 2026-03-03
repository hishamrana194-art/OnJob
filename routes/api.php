<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AuthAdminController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\CourseUserController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\ModuleItemController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizSubmissionController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SuperAdminController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserCourseEnrollmentController;
use App\Http\Controllers\Api\UserModuleProgressController;
use App\Http\Controllers\Api\UserQuizAttemptController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\RewardController;
use App\Http\Controllers\Api\BadgeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ReelController;

use Illuminate\Support\Facades\Route;

// ========================
// User Authentication Routes
// ========================
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);


// ========================
// Public Registration Routes
// ========================
Route::post('/register/employer', [RegistrationController::class, 'registerEmployer']);
Route::post('/register/vendor', [RegistrationController::class, 'registerVendor']);
Route::post('/verify-email', [RegistrationController::class, 'verifyEmail']);
Route::post('/resend-verification', [RegistrationController::class, 'resendVerification']);

// Protected User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::patch('/me/preferences', [UserController::class, 'updatePreferences']);
});

// ========================
// Admin Authentication Routes
// ========================
Route::post('/admin/register', [AuthAdminController::class, 'register']);
Route::post('/admin/login', [AuthAdminController::class, 'login']);

// Protected Admin Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AuthAdminController::class, 'logout']);
    Route::get('/admin', [AuthAdminController::class, 'admin']);
    Route::get('/admin/me', [AuthAdminController::class, 'me']);
});

// ========================
// API Resources
// ========================
Route::apiResource('/countries', CountryController::class);
Route::apiResource('/employers', EmployerController::class);
Route::apiResource('/vendors', VendorController::class);
Route::apiResource('/packages', PackageController::class);
Route::apiResource('/subscriptions', SubscriptionController::class);

// ========================
// User Management (Protected)
// ========================
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/users', UserController::class);
    Route::post('/users/{id}/assign-role', [UserController::class, 'assignRole']);
    Route::post('/users/{id}/remove-role', [UserController::class, 'removeRole']);
    Route::get('/users/{id}/roles', [UserController::class, 'getUserRoles']);
    Route::get('/users/{id}/permissions', [UserController::class, 'getUserPermissions']);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::get('/my-subscription', [UserController::class, 'getMySubscription']);
    Route::get('/courseuser',[CourseUserController::class, 'getCoursesUser']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
});

// ========================
// Courses & Learning Resources
// ========================
Route::apiResource('/categories', CategoryController::class);
Route::apiResource('/courses', CourseController::class);

// Module progress routes (must be before apiResource to avoid conflicts)
Route::middleware('auth:api')->prefix('modules')->group(function () {
    Route::post('start', [UserModuleProgressController::class, 'startModule']);
    Route::get('overall-progress', [UserModuleProgressController::class, 'overallProgress']);
    Route::post('progress', [UserModuleProgressController::class, 'updateModuleProgress']);
    Route::post('item-progress', [UserModuleProgressController::class, 'updateModuleItemProgress']);
    Route::post('complete', [UserModuleProgressController::class, 'markComplete']);
    Route::get('myprogress', [UserModuleProgressController::class, 'myModuleProgress']);
    Route::get('my-item-progress', [UserModuleProgressController::class, 'myModuleItemProgress']);
});

Route::apiResource('/modules', ModuleController::class);
Route::apiResource('/module-items', ModuleItemController::class);
// Complete a module item (mobile-friendly)
Route::middleware('auth:api')->post('/module-items/{moduleItem}/complete', [UserModuleProgressController::class, 'completeModuleItem']);
Route::apiResource('/quizzes', QuizController::class);
Route::apiResource('/questions', QuestionController::class);


// Nested routes for better organization
Route::get('/courses/{course}/modules', [ModuleController::class, 'byCourse']);
Route::middleware('auth:sanctum')->get('/courses/{course}/lessons', [ModuleController::class, 'lessonsByCourse']);
Route::get('/modules/{module}/items', [ModuleItemController::class, 'byModule']);
Route::get('/courses/{course}/quizzes', [QuizController::class, 'byCourse']);
Route::middleware('auth:sanctum')->get('/quizzes/{quiz}/questions', [QuestionController::class, 'byQuiz']);

// ========================
// Super Admin Routes (Protected - Admin only)
// ========================
Route::middleware('auth:sanctum')->prefix('super-admin')->group(function () {
    Route::post('/employers/create-with-admin', [SuperAdminController::class, 'createEmployerWithAdmin']);
    Route::post('/vendors/create-with-admin', [SuperAdminController::class, 'createVendorWithAdmin']);

});


Route::middleware('auth:api')->prefix('enrollments')->group(function () {
    Route::post('enroll', [UserCourseEnrollmentController::class, 'enroll']);
    Route::get('my-courses', [UserCourseEnrollmentController::class, 'myEnrollments']);
    Route::get('completed', [UserCourseEnrollmentController::class, 'completedCourses']);
    Route::get('in-progress', [UserCourseEnrollmentController::class, 'inProgressCourses']);
});
Route::middleware('auth:api')->prefix('quiz-attempts')->group(function () {

    Route::post('/{id}', [UserQuizAttemptController::class, 'store']);
    Route::get('/my', [UserQuizAttemptController::class, 'myAttempts']);
    Route::get('/my/quiz/{quizId}', [UserQuizAttemptController::class, 'myAttemptsByQuiz']);
    Route::get('/passed', [UserQuizAttemptController::class, 'passedAttempts']);
    Route::get('/failed', [UserQuizAttemptController::class, 'failedAttempts']);

});


Route::middleware('auth:api')->group(function () {

    Route::post('courses/{id}/enroll', [EnrollmentController::class, 'enroll']);
    Route::get('my-enrollments', [EnrollmentController::class, 'myEnrollments']);
    Route::get('courses/{id}/enrollments', [EnrollmentController::class, 'courseEnrollments']);
});



Route::middleware('auth:sanctum')->group(function () {
    Route::post('/quizzes/{id}/submit', [QuizSubmissionController::class, 'submit']);
});

Route::middleware('auth:api')->get(
    '/my-progress',
    [\App\Http\Controllers\Api\ProgressDashboardController::class, 'myProgress']
);

// ========================
// Leaderboard Routes
// ========================
Route::middleware('auth:sanctum')->prefix('leaderboard')->group(function () {
    Route::get('company', [LeaderboardController::class, 'company']);
    Route::get('team', [LeaderboardController::class, 'team']);
    Route::get('global', [LeaderboardController::class, 'global']);
    Route::get('my-rank', [LeaderboardController::class, 'myRank']);
});

// ========================
// Rewards Routes
// ========================
Route::middleware('auth:sanctum')->prefix('rewards')->group(function () {
    Route::get('/', [RewardController::class, 'index']);
    Route::post('redeem', [RewardController::class, 'redeem']);
    Route::get('my-redemptions', [RewardController::class, 'myRedemptions']);
});

// ========================
// Badges Routes
// ========================
Route::middleware('auth:sanctum')->prefix('badges')->group(function () {
    Route::get('/', [BadgeController::class, 'index']);
    Route::get('my', [BadgeController::class, 'myBadges']);
});

// ========================
// Notifications Routes
// ========================
Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
});

// ========================
// Reels Routes
// ========================
Route::middleware('auth:sanctum')->prefix('reels')->group(function () {
    Route::get('/', [ReelController::class, 'index']);
    Route::post('{id}/complete', [ReelController::class, 'complete']);
});

