<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserPointCategoryController;
use App\Http\Controllers\Api\UserPostParticipationController;
use App\Http\Controllers\Api\UserTrophyController;
use App\Http\Controllers\Api\UsersRelationController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\PushNotificationController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('image/{path}', [ImageController::class, 'getImage'])->where('path', '.*');

Route::middleware('api_key')->group(
    function () {

        // Authentication
        /**
         * @unauthenticated
         */
        Route::post('/login', [AuthenticationController::class, 'login']);
        /**
         * @unauthenticated
         */
        Route::post('/register', [AuthenticationController::class, 'register']);

        Route::post('request-reset-password', [AuthenticationController::class, 'requestResetPassword']);
        Route::post('reset-password', [AuthenticationController::class, 'resetPassword']);

        Route::middleware('auth:sanctum')->group(function () {
            // User 
            Route::get('/me', [UserController::class, 'getUserData']);
            Route::patch('/me', [UserController::class, 'update']);
            Route::delete('/me', [UserController::class, 'destroy']);

            Route::post('change-password', [AuthenticationController::class, 'changePassword']);

            // images
            Route::post('users/{userId}/uploadImage', [ImageController::class, 'uploadImageUser']);
            Route::post('posts/{postId}/uploadImage', [ImageController::class, 'uploadImagePost']);

            // other user
            Route::get('users/{userId}', [UserController::class, 'show']);

            // User post participation
            Route::get('posts/{postId}/participants', [UserPostParticipationController::class, 'getParticipantsByPostId']);
            Route::post('posts/{postId}/participants', [UserPostParticipationController::class, 'store']);
            Route::patch('posts/{postId}/participants', [UserPostParticipationController::class, 'update']);
            Route::delete('posts/{postId}/participants/{userId}', [UserPostParticipationController::class, 'destroy']);
            // end a challenge 
            Route::patch('posts/{postId}/participants/completed', [UserPostParticipationController::class, 'endChallenge']);

            Route::post('/posts/{postId}/likes', [LikeController::class, 'likePost']);
            Route::delete('/posts/{postId}/likes', [LikeController::class, 'unlikePost']);

            Route::post('posts/{postId}/comments', [CommentController::class, 'store']);
            Route::patch('posts/comments/{id}', [CommentController::class, 'update']);
            Route::delete('posts/comments/{id}', [CommentController::class, 'destroy']);

            Route::get('/posts-by-tag/{tag}', [PostController::class, 'getPostsByTag']);

            Route::get('users/{userId}/actions', [UserPostParticipationController::class, 'getUserActions']);
            Route::get('users/{userId}/challenges/completed', [UserPostParticipationController::class, 'getPostsByUserCompleted']);
            Route::get('users/{userId}/challenges/in-progress', [UserPostParticipationController::class, 'getPostsByUserInProgress']);
            Route::get('users/{userId}/challenges/abandoned', [UserPostParticipationController::class, 'getPostsByUserAbandoned']);
            Route::get('users/{userId}/challenges/next', [UserPostParticipationController::class, 'getPostsByUserNext']);
            Route::get('users/{userId}/posts', [UserPostParticipationController::class, 'getPostsByUser']);

            //Search
            Route::get('/search/{q}', [SearchController::class, 'getResult']);

            // User Relations
            Route::post('users/{userId}/subscribe', [UsersRelationController::class, 'subscribe']);
            Route::delete('users/{userId}/unsubscribe', [UsersRelationController::class, 'unSubscribe']);
            Route::post('users/{userId}/accept-subscription-request', [UsersRelationController::class, 'acceptSubscriptionRequest']);
            Route::delete('users/{userId}/decline-subscription-request', [UsersRelationController::class, 'declineSubscriptionRequest']);
            Route::delete('users/{userId}/cancel-subscription-request', [UsersRelationController::class, 'cancelSubscriptionRequest']);
            Route::post('users/{userId}/block', [UsersRelationController::class, 'blockUser']);
            Route::delete('users/{userId}/unblock', [UsersRelationController::class, 'unblockUser']);

            // notifications 
            Route::get('/me/notifications', [NotificationsController::class, 'index']);

            // Report
            Route::post('/submit-report', [ReportController::class, 'submitReport']);

            Route::delete('remove-follower/{userId}', [UsersRelationController::class, 'removeFollower']);

            // API business routes
            Route::apiResources([
                'posts' => PostController::class, // posts?page=1 => 30 firsts posts; posts?page=2 => 30 next posts
                'categories' => CategoryController::class,
                'users/{userId}/categories-points' => UserPointCategoryController::class, // user points in categories
                'users/{userId}/trophies' => UserTrophyController::class, // user trophies
                'tags' => TagController::class,
            ]);

            
        });

    }
);
