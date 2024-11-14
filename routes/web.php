<?php
Route::get('get-video', function () {
    return Artisan::call('app:get-video');
 });
 Route::get('run-video', function () {
     return Artisan::call('app:generate-video');
  });
 Route::get('optimize-clear', function () {
     Artisan::call('optimize:clear');
 });
 Route::get('storage-link', function () {
     Artisan::call('storage:link');
 });
 Route::get('composer-update', function () {
     shell_exec('composer update');
 });
 Route::get('key-generate', function () {
     shell_exec('key:generate');
 });
 
Route::view('/', 'welcome');
Route::get('userVerification/{token}', 'UserVerificationController@approve')->name('userVerification');
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', '2fa', 'admin']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Session
    Route::delete('sessions/destroy', 'SessionController@massDestroy')->name('sessions.massDestroy');
    Route::post('sessions/media', 'SessionController@storeMedia')->name('sessions.storeMedia');
    Route::post('sessions/ckmedia', 'SessionController@storeCKEditorImages')->name('sessions.storeCKEditorImages');
    Route::resource('sessions', 'SessionController')->exept('show');

    // Todo
    Route::delete('todos/destroy', 'TodoController@massDestroy')->name('todos.massDestroy');
    Route::resource('todos', 'TodoController');

    // Payments
    Route::delete('payments/destroy', 'PaymentsController@massDestroy')->name('payments.massDestroy');
    Route::resource('payments', 'PaymentsController');

    // Credits
    Route::delete('credits/destroy', 'CreditsController@massDestroy')->name('credits.massDestroy');
    Route::resource('credits', 'CreditsController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    Route::get('messenger', 'MessengerController@index')->name('messenger.index');
    Route::get('messenger/create', 'MessengerController@createTopic')->name('messenger.createTopic');
    Route::post('messenger', 'MessengerController@storeTopic')->name('messenger.storeTopic');
    Route::get('messenger/inbox', 'MessengerController@showInbox')->name('messenger.showInbox');
    Route::get('messenger/outbox', 'MessengerController@showOutbox')->name('messenger.showOutbox');
    Route::get('messenger/{topic}', 'MessengerController@showMessages')->name('messenger.showMessages');
    Route::delete('messenger/{topic}', 'MessengerController@destroyTopic')->name('messenger.destroyTopic');
    Route::post('messenger/{topic}/reply', 'MessengerController@replyToTopic')->name('messenger.reply');
    Route::get('messenger/{topic}/reply', 'MessengerController@showReply')->name('messenger.showReply');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
        Route::post('profile/two-factor', 'ChangePasswordController@toggleTwoFactor')->name('password.toggleTwoFactor');
    }
});
Route::group(['as' => 'frontend.', 'namespace' => 'Frontend', 'middleware' => ['auth', '2fa']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Session
    Route::delete('sessions/destroy', 'SessionController@massDestroy')->name('sessions.massDestroy');
    Route::post('sessions/media', 'SessionController@storeMedia')->name('sessions.storeMedia');
    Route::post('sessions/ckmedia', 'SessionController@storeCKEditorImages')->name('sessions.storeCKEditorImages');
    Route::resource('sessions', 'SessionController');
    Route::get('session/recorder/{id}', 'SessionController@recorder')->name('session.recorder');
    Route::post('session/upload', 'SessionController@upload')->name('session.upload');
    Route::get('check-updates/{id}', 'SessionController@checkUpdates')->name('checkUpdates');
    Route::post('check-session-status/{id}', 'SessionController@checkSessionStatus')->name('checkSessionStatus');
    Route::post('create-todo-list/{id}', 'SessionController@createToDoList')->name('createToDoList');
    Route::post('save-notes', 'SessionController@saveNotes')->name('saveNotes');
    Route::post('update-todo-status', 'SessionController@UpdateTodoStatus')->name('updateTodoStatus');
    Route::post('update-todo-research', 'SessionController@UpdateTodoResearch')->name('UpdateTodoResearch');

    //PDF Download
    Route::get('view-pdf/{id}', 'PDFController@viewPDF')->name('viewPDF');
    Route::get('pdf-download/{id}', 'PDFController@downloadPDF')->name('downloadPDF');
    // Todo
    Route::delete('todos/destroy', 'TodoController@massDestroy')->name('todos.massDestroy');
    Route::resource('todos', 'TodoController');
    Route::post('todos/delete-all', 'TodoController@deleteAll')->name('todos.deleteAll');

    // Payments
    Route::delete('payments/destroy', 'PaymentsController@massDestroy')->name('payments.massDestroy');
    Route::resource('payments', 'PaymentsController');

    // Credits
    Route::delete('credits/destroy', 'CreditsController@massDestroy')->name('credits.massDestroy');
    Route::resource('credits', 'CreditsController');

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    Route::get('frontend/profile', 'ProfileController@index')->name('profile.index');
    Route::post('frontend/profile', 'ProfileController@update')->name('profile.update');
    Route::post('frontend/profile/destroy', 'ProfileController@destroy')->name('profile.destroy');
    Route::post('frontend/profile/password', 'ProfileController@password')->name('profile.password');
    Route::post('profile/toggle-two-factor', 'ProfileController@toggleTwoFactor')->name('profile.toggle-two-factor');
});
Route::group(['namespace' => 'Auth', 'middleware' => ['auth', '2fa']], function () {
    // Two Factor Authentication
    if (file_exists(app_path('Http/Controllers/Auth/TwoFactorController.php'))) {
        Route::get('two-factor', 'TwoFactorController@show')->name('twoFactor.show');
        Route::post('two-factor', 'TwoFactorController@check')->name('twoFactor.check');
        Route::get('two-factor/resend', 'TwoFactorController@resend')->name('twoFactor.resend');
        Route::get('/auth/redirect', 'LoginController@redirectToProvider');
        Route::get('/auth/callback', 'LoginController@handleProviderCallback');
    }
});
