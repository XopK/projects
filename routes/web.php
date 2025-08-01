<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AdultCheck;
use App\Http\Middleware\AuthCheck;
use App\Http\Middleware\TeacherCheck;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.index');
})->name('index');

Route::get('/groups/index', [GroupController::class, 'getGroupWithVideo'])->name('getGroupsIndex');

Route::get('/teachers/index', [TeacherController::class, 'getTeacher'])->name('getTeachersIndex');

Route::controller(AuthController::class)->group(function () {

    Route::post('/signUp', 'signUp')->name('signUp');

    Route::post('/signIn', 'signIn')->name('signIn');

    Route::get('/signOut', 'signOut')->name('signOut');

    Route::get('/forgot_password', 'forgotPassword')->name('forgotPassword');

    Route::post('/forgotPassword', 'forgotPasswordSend')->name('forgotPasswordSend');

    Route::get('/reset_password/{token}', 'resetPassword')->name('resetPassword');

    Route::put('/reset_password/update', 'resetPasswordUpdate')->name('resetPasswordUpdate');

    Route::get('/confirm_phone/{token}', 'confirmPhone')->name('confirmPhone');

    Route::post('/confirm_phone/{token}/verify', 'confirmPhoneVerify')->name('confirmPhoneVerify');

});

Route::controller(UserController::class)->middleware(AuthCheck::class)->prefix('profile')->group(function () {

    Route::get('/', 'index')->name('profile');

    Route::post('/update', 'update')->name('profileUpdate');

    Route::get('/auth/telegram', 'telegram')->name('profileTelegram');

    Route::get('/auth/telegram/unlink', 'unlinkTelegram')->name('profileTelegramUnlink');

    Route::get('/favorites', 'favorites')->name('profileFavorites');

    Route::get('/groups', 'groups')->name('profileGroups');

    Route::get('/my_groups', 'my_groups')->middleware(TeacherCheck::class)->name('profileMyGroups');

    Route::post('/update/avatar', 'avatar_update')->name('profileAvatarUpdate');

    Route::get('/notifications/read', 'markRead')->name('markRead');

    Route::get('/notifications', 'notifications')->name('profileNotifications');

    Route::get('/notifications/allRead', 'notificationsAllRead')->name('profileNotificationsAllRead');

    Route::post('/settings/notify/{field}', 'notifySetting')->name('settingNotify');

    Route::post('/updatePhone', 'updatePhone')->name('updatePhone');

    Route::put('/update/password', 'updatePassword')->name('updatePassword');

    Route::get('/users-list/{group}', 'userList')->middleware(TeacherCheck::class)->name('userList');

    Route::post('/users-list/{group}/update', 'userListStatus')->middleware(TeacherCheck::class)->name('userListStatus');

});

Route::controller(GroupController::class)->group(function () {

    Route::get('/groups', 'index')->name('groups');

    Route::get('/group/{group}', 'group')->name('group')->middleware(AdultCheck::class);

    Route::post('/group/create', 'create')->middleware(TeacherCheck::class)->name('groupCreate');

    Route::delete('/group/{group}/delete', 'deleteGroup')->middleware(TeacherCheck::class)->name('deleteGroup');

    Route::post('/group/{group}/favorite', 'addFavorite')->middleware(AuthCheck::class)->name('groupAddFavorite');

    Route::get('/group/{group}/edit', 'editGroup')->middleware(TeacherCheck::class)->name('groupEdit');

    Route::put('/group/{group}/update', 'updateGroup')->middleware(TeacherCheck::class)->name('groupUpdate');

    Route::get('/group/{group}/userReg', 'registerUser')->middleware(AuthCheck::class)->name('groupUserReg');

    Route::delete('/group/userDelete', 'deleteUser')->middleware(AuthCheck::class)->name('groupUserDelete');

    Route::post('/group/{group}/addPhoto', 'addPhoto')->middleware(TeacherCheck::class)->name('groupAddPhoto');

    Route::post('/group/{group}/addVideo}', 'addVideo')->middleware(TeacherCheck::class)->name('groupAddVideo');

    Route::delete('/group/{group}/deletePhoto', 'deletePhoto')->middleware(TeacherCheck::class)->name('groupDeletePhoto');

    Route::put('/group/{group}/updatePreview', 'updatePreview')->middleware(TeacherCheck::class)->name('groupUpdatePreview');

    Route::delete('/group/{group}/deleteVideo', 'deleteVideo')->middleware(TeacherCheck::class)->name('groupDeleteVideo');

    Route::put('/group/{group}/updatePreviewVideo', 'updatePreviewVideo')->middleware(TeacherCheck::class)->name('groupUpdatePreviewVideo');

});

Route::prefix('callback')->group(function () {

    Route::post('/telegram', [UserController::class, 'callbackTelegram'])->name('callback.telegram');

});

Route::controller(ChatController::class)->middleware(AuthCheck::class)->group(function () {

    Route::get('/chat', 'index')->name('chat');

    Route::get('/getChat', 'getChats')->name('getChat');

    Route::get('/messages', 'getMessages')->name('getMessages');

    Route::post('/sendMessage', 'sendMessage')->name('sendMessage');

    Route::post('/markReadMessage', 'markReadMessage')->name('markReadMessage');

    Route::post('/uploadFileMessages', 'fileUpload')->name('uploadFileMessages');

});

Route::controller(TeacherController::class)->group(function () {

    Route::get('/teachers', 'teachers')->name('teachers');

    Route::put('/teachers/{teacher}/update', 'updateTeacher')->name('updateTeacher');

    Route::get('/teacher/{teacher}', 'teacher')->name('teacher');

    Route::get('/teacher/{teacher}/videos', 'teacherVideo')->name('teacherVideo');

    Route::get('/user/{user}', 'userInfo')->middleware(TeacherCheck::class)->name('userInfo');

    Route::delete('/group/userDeleteTeacher', 'deleteUserTeacher')->middleware(TeacherCheck::class)->name('deleteUserTeacher');

    Route::delete('/teacher/{teacher}/deletePhoto', 'deleteTeacherPhoto')->middleware(TeacherCheck::class)->name('deleteTeacher');

});


