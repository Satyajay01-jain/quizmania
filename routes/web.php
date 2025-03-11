<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\APIController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

// base route
Route::get('/', function () {
    return view('login');
});

// Admin login routes
Route::get('/admin/login', function () {
    return view('login');
})->name('login');

Route::post('/admin/login', [AdminController::class, 'postAdminController'])->name("post_login");
Route::post('/paymentopen', [PaymentController::class, 'makePayoutOpenBank'])->name("paymentopen");


Route::get('/logout', function () {

    //logout user
    auth()->logout();
    // redirect to homepage
    return redirect('/admin/login');
})->name("logout");

// Admin login routes



Route::middleware(['auth'])->group(function () {
   
    Route::prefix('admin')->group(function () {
        // Dashbaord
        Route::get('/dashbaord', [AdminController::class, 'dashboard'])->name("admin.dashboard");

        // manage question tab
        Route::get('/questions/{topic?}', [AdminController::class, 'questions'])->name("admin.questions");
        Route::post('/questions/{topic?}', [ExcelImportController::class, 'import']);
        // manage question tab
        Route::post('/getfilteredquestions', [AdminController::class, 'getfilteredquestions'])->name("admin.getfilteredquestions");
        Route::get('/search', [AdminController::class, 'search'])->name("admin.questions.search");
       // Route::post('/questions/search',[AdminController::class,'showEmployee'])->name('admin.questions.search');
        Route::get('/new/questions',[AdminController::class, 'new_question'])->name("new.question");
        
        // routes/web.php
        Route::get('/importquestions', [AdminController::class, 'importquestions'])->name('importquestions.show');
        // routes/web.php
        Route::post('/processimport', [AdminController::class, 'processimport'])->name('import.process');


        Route::post('/new/question', [AdminController::class, 'postNewQuestion'])->name("post_new_question");   
        Route::get('/view/question/{id}', [AdminController::class, 'view_question'])->name("admin.view.question");
        Route::get('/edit/question/{id}', [AdminController::class, 'edit_question'])->name("admin.edit.question");
        Route::post('/edit/question', [AdminController::class, 'post_edit_question'])->name("post_edit_question");
        Route::post('/delete/question/{id}', [AdminController::class, 'delete_question'])->name("admin.delete.question");
        
        // manage contest tab
        Route::get('/contests/{type?}', [AdminController::class, 'contests'])->name("admin.contests");
        Route::get('/new/contest',[AdminController::class, 'newContests'])->name("new.contest");
        Route::get('/new/live/contest',[AdminController::class, 'newLiveContests'])->name("admin.new.live.contest");
        Route::get('/new/timelimit/contest',[AdminController::class, 'newTimeLimitContests'])->name("admin.new.timelimit.contest");
        Route::get('/new/anytime/contest',[AdminController::class, 'newAnyTimeContests'])->name("admin.new.anytime.contest");
        Route::post('/new/contest', [AdminController::class, 'postNewContest'])->name("post_new_contest");   
        Route::get('/view/contest/{id}', [AdminController::class, 'view_contest'])->name("admin.view.contest");
        Route::get('/edit/contest/{id}', [AdminController::class, 'edit_contest'])->name("admin.edit.contest");
        Route::post('/edit/contest', [AdminController::class, 'post_edit_contest'])->name("post_edit_contest");
        Route::post('/delete/contest/{id}', [AdminController::class, 'delete_contest'])->name("admin.delete.contest");
        
        Route::get('/contest/payment/ledger/{id}/{from?}/{to?}/{userid?}', [AdminController::class, 'contest_payment_ledger'])->name("admin.contest.payment.ledger");
        Route::get('/contest/widthdraw/ledger/{id}/{from?}/{to?}/{userid?}', [AdminController::class, 'contest_withdraw_ledger'])->name("admin.contest.withdraw.ledger");
        Route::get('/contest/leaderboard/{id}/{from?}/{to?}/{userid?}', [AdminController::class, 'contest_leaderboard_ledger'])->name("admin.contest.leaderboard");
        Route::get('/contest/leaderboardapi/{id}/{from?}/{to?}/{userid?}', [AdminController::class, 'contest_leaderboard_ledger_api'])->name("admin.contest.leaderboard.api");
        Route::get('/contest/preview/{id}', [AdminController::class, 'contest_preview'])->name("admin.preview.contest");
        Route::post('/contest/preview/{id}', [AdminController::class, 'contest_submit'])->name("admin.submit.contest");


        // manage topics tab
        Route::get('/topics', [AdminController::class, 'topics'])->name("admin.topics");
        Route::post('/new/topic',[AdminController::class, 'newTopic'])->name("post_new_topic");
        Route::post('/edit/topic', [AdminController::class, 'post_edit_topic'])->name("post_edit_topic");
        Route::get('/topics/add/material/{topic_id}/{type}', [AdminController::class, 'add_topic_study_material'])->name("admin.add.topic.material");
        Route::get('/topics/delete/material/{topic_id}/{type}', [AdminController::class, 'delete_topic_study_material'])->name("admin.delete.topic.material");
        Route::post('/topics/update/material', [AdminController::class, 'post_add_topic_study_material'])->name("admin.update.topic.material");
        Route::delete('/topic/{id}', [AdminController::class, 'destroy'])->name('admin.destroy'); 
        
        // manage education tab
        Route::get('/education', [AdminController::class, 'education'])->name("admin.education");
        Route::post('/new/education',[AdminController::class, 'newEducation'])->name("post_new_education");
        Route::post('/edit/education', [AdminController::class, 'post_edit_education'])->name("post_edit_education");

         // manage profession tab
        Route::get('/profession', [AdminController::class, 'profession'])->name("admin.profession");
        Route::post('/new/profession',[AdminController::class, 'newProfession'])->name("post_new_profession");
        Route::post('/edit/profession', [AdminController::class, 'post_edit_profession'])->name("post_edit_profession");

        // manage state tab
        Route::get('/state', [AdminController::class, 'state'])->name("admin.state");
        Route::post('/new/state',[AdminController::class, 'newState'])->name("post_new_state");
        Route::post('/edit/state', [AdminController::class, 'post_edit_state'])->name("post_edit_state");

        // manage city tab
        Route::get('/city', [AdminController::class, 'city'])->name("admin.city");
        Route::post('/new/city',[AdminController::class, 'newCity'])->name("post_new_city");
        Route::post('/edit/city', [AdminController::class, 'post_edit_city'])->name("post_edit_city");

       
        // manage faq tab
        Route::get('/faq', [AdminController::class, 'faq'])->name("admin.faq");
        Route::post('/new/faq',[AdminController::class, 'newFAQ'])->name("post_new_faq");
        Route::post('/edit/faq', [AdminController::class, 'post_edit_faq'])->name("post_edit_faq");
        
        
        
        // manage users tab
        Route::get('/users', [AdminController::class, 'users'])->name("admin.users");
        
        Route::get('/view/user/{id}', [AdminController::class, 'view_user'])->name("admin.view.user");
        Route::post('/block-user', [AdminController::class, 'block_user'])->name("admin.block.user");
        Route::post('/delete/user/{id}', [AdminController::class, 'delete_user'])->name("admin.delete.user");
        
         // manage Financial Status tab
        Route::get('/financial-reports', [AdminController::class, 'financialReports'])->name("admin.financial-reports");
        
         Route::get('/get/question', [AdminController::class, 'getAllQuestion'])->name("get.questions");
        
    }); 
});
