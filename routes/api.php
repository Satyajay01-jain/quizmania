<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\APIController;
use App\Http\Controllers\PaymentController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// check in video call
Route::get('/check/user/videocall/{user_id}', [APIController::class, 'check_user_video_call']);
Route::get('/check/user/leave/videocall/{user_id}', [APIController::class, 'user_leave_video_call']);

Route::get('/live/user/count/{count}', [APIController::class, 'live_user_count']);
Route::get('/fetch/live/user/count/', [APIController::class, 'fetch_live_user']);
Route::post('/user/register', [APIController::class, 'register_user']);
Route::post('/user/login', [APIController::class, 'login_user']);
Route::post('/user/details', [APIController::class, 'user_details']);
Route::post('/user/update/details', [APIController::class, 'update_user_details']);
Route::post('/user/feedback', [APIController::class, 'feedback']);
Route::post('/user/wallet/transactions', [APIController::class, 'wallet_details']);

Route::get('/contest/categorywise/{categoryId}', [APIController::class, 'singleCategoryContest']);
Route::get('/contest/categorywise/{categoryId}/{user_id}', [APIController::class, 'singleCategoryContest']);
Route::get('/contests', [APIController::class, 'contests']);
Route::get('/contests/list', [APIController::class, 'contest_list']);
Route::get('/user/participated/contests/list/{user_id}', [APIController::class, 'user_participated_contest_list']);


// quizes
Route::get('/practice/quiz/', [APIController::class, 'practice_quiz']);
Route::get('/quiz/{id}', [APIController::class, 'quiz_details']);
Route::post('/quiz/startQuiz', [APIController::class, 'start_quiz']);
Route::post('/quiz/submit', [APIController::class, 'submit_quiz']);
Route::post('/user/check/quiz/registration', [APIController::class, 'check_registration']);
Route::post('/user/register/quiz/wallet', [APIController::class, 'register_for_quiz_wallet']);
Route::post('/quiz/live/update', [APIController::class, 'update_live_quiz']);
Route::get('/quiz/leaderboard/{quiz_id}/{user_id}', [APIController::class, 'quiz_leaderboard']);
Route::get('/quiz/prizepoll/{quiz_id}', [APIController::class, 'quiz_prizepoll']);
Route::get('/live/quiz/leaderboard/{quiz_id}/{user_id}', [APIController::class, 'live_quiz_leaderboard']);
Route::get('/my/leaderboard/{user_id}', [APIController::class, 'user_leaderboard']);
Route::get('/contests/upcoming/list', [APIController::class, 'upcoming_contest_list']);
Route::get('/host/quiz/questions/{id}', [APIController::class, 'quiz_questions']);
// start video call
Route::post('/quiz/start/videocall', [APIController::class, 'start_video_call']);
Route::post('/quiz/start/streaming', [APIController::class, 'start_streaming']);
Route::get('/host/quiz/leaderboard/{quiz_id}', [APIController::class, 'host_quiz_leaderboard']);

Route::get('/get/register/form/details', [APIController::class, 'getBasicRegisterDetails']);
Route::get('/get/profile/form/details', [APIController::class, 'getBasicProfileDetails']);

// topics
Route::get('/topics', [APIController::class, 'getTopics']);
Route::get('/topic/study/material/{topic_id}', [APIController::class, 'getTopicStudyMaterial']);



// payments
Route::post('/user/add/amount', [PaymentController::class, 'addAmount']);
Route::post('/user/update/payment/status', [PaymentController::class, 'updatePayment']);
Route::post('/user/withdraw/amount', [PaymentController::class, 'withdrawAmount']);
Route::post('/user/withdraw/large/amount', [PaymentController::class, 'withdrawLargeAmount']);
Route::post('/user/withdrawbyop/amount', [PaymentController::class, 'withdrawAmountByOp']);

Route::get('user/withdrawbyop', function(){
    return "Hello Worls";
});



//oepn payment route
Route::post('/user/withdraw/openpayoutamt', [PaymentController::class, 'makePayoutWithOpenBank']);

// crons
Route::get('/cron/calculate/marks', [APIController::class, 'calculate_final_marks']);
Route::get('/cron/calculate/ranks', [APIController::class, 'calculate_final_ranks']);
Route::get('/cron/transfer/money/to/wallet', [APIController::class, 'cron_transfer_money_to_wallet']);


