<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false, 'verify' => true]);

Route::get('/setup-account/{token}', 'AccountController@create')->name('setup-account');

Route::middleware(['auth' => 'verified'])->group(function () {
    // Route::get('/test-job-reminders', function () {
    //     Artisan::call('send-interpreterjob-reminders');
    //     return 'Job reminders sent!';
    // });
    
    Route::get('/test-job-reminders', [TestJobReminderController::class, 'run']);

    Route::middleware('redirect_new_agent_to_profile')->group(function () {
        Route::get('/', 'InterpreterJobController@index')->name('index');

        Route::get('/interpreter-jobs', 'InterpreterJobController@index')->name('interpreter-jobs.index');
        Route::get('/interpreter-jobs/{interpreterJob}/quotes', 'InterpreterJobQuoteController@index')->name('interpreter-jobs.quotes.index')->middleware('can:viewQuotes,interpreterJob');
        Route::get('/interpreter-jobs/{interpreterJob}/allupdates', 'InterpreterJobController@allupdates')->name('interpreter-jobs.allupdates.index')->middleware('role:admin');
        Route::get('/interpreter-jobs/{interpreterJob}/allupdates', 'InterpreterJobController@allupdates')->name('interpreter-jobs.allupdates.index')->middleware('role:admin');
        //dna and retrn
        Route::put('/interpreter-job/{interpreterJob}/dna', 'InterpreterJobController@dna')->name('interpreter-jobs.dna')->middleware('role:admin');
        Route::put('/interpreter-jobs/{interpreterJob}/retrn', 'InterpreterJobController@retrn')->name('interpreter-jobs.retrn')->middleware('role:admin');

        Route::get('interpreter-jobs/{interpreterJob}/documents', 'InterpreterJobDocumentController@index')->name('interpreter-jobs.documents.index');
        Route::post('interpreter-jobs/{interpreterJob}/documents', 'InterpreterJobDocumentController@store')->name('interpreter-jobs.documents.store');
        Route::post('interpreter-jobs/travelDetails', 'InterpreterJobDocumentController@travel_details')->name('travelDetails.travel_details');

        Route::get('/translator-jobs/{translatorJob}/allupdates', 'TranslatorJobController@allupdates')->name('translator-jobs.allupdates.index')->middleware('role:admin');
        //   Route::get('/timesheet','InterpreterJobController@timesheet')->name('interpreter-jobs.timesheet.index');

        Route::get('/translator-jobs', 'TranslatorJobController@index')->name('translator-jobs.index');
        Route::get('/translator-jobs/{translatorJob}/quotes', 'TranslatorJobQuoteController@index')->name('translator-jobs.quotes.index')->middleware('can:viewQuotes,translatorJob');
    });

    Route::middleware('role:admin')->group(function () {

    });
    Route::middleware('role:admin|client')->group(function () {

        Route::group(['prefix' => '/interpreter-jobs', 'as' => 'interpreter-jobs.'], function () {

            Route::get('/export', 'InterpreterJobController@export')->name('export')->middleware('role:admin');
            Route::get('/create', 'InterpreterJobController@create')->name('create');
            Route::get('/createbulk', 'InterpreterJobController@create_bulk')->name('createbulk');

            Route::post('/', 'InterpreterJobController@store')->name('store');
            Route::post('/createbulk', 'InterpreterJobController@store_bulk')->name('storebulk');
            Route::get('/{interpreterJob}/edit', 'InterpreterJobController@edit')->name('edit')->middleware('can:edit,interpreterJob');
            Route::put('/{interpreterJob}', 'InterpreterJobController@update')->name('update')->middleware('can:update,interpreterJob');
            Route::get('/{interpreterJob}/agent', 'InterpreterJobAgentController@index')->name('agent.index')->middleware('can:edit,interpreterJob');
        });

        Route::group(['prefix' => '/translator-jobs', 'as' => 'translator-jobs.'], function () {
            Route::get('/export', 'TranslatorJobController@export')->name('export')->middleware('role:admin');
            Route::get('/create', 'TranslatorJobController@create')->name('create');
            Route::post('/', 'TranslatorJobController@store')->name('store');
            Route::get('/{translatorJob}/edit', 'TranslatorJobController@edit')->name('edit')->middleware('can:edit,translatorJob');
            Route::put('/{translatorJob}', 'TranslatorJobController@update')->name('update')->middleware('can:update,translatorJob');
        });

        Route::post('/interpreter-jobs/{interpreterJob}/cancel', 'InterpreterJobCancellationController@store')->name('interpreter-jobs.cancel')->middleware('can:edit,interpreterJob');
        Route::post('/translator-jobs/{translatorJob}/cancel', 'TranslatorJobCancellationController@store')->name('translator-jobs.cancel')->middleware('can:edit,translatorJob');

        Route::get('/report', 'ReportController@index')->name('report.index');
        Route::get('/report-export', 'ReportController@export')->name('report-export')->middleware('role:admin');

    });

    Route::middleware('role:admin|client|agent')->group(function () {
        Route::put('/interpreter-jobs/{interpreterJob}/complete', 'CompletedInterpreterJobController@update')->name('interpreter-jobs.complete')->middleware('can:complete,interpreterJob');
    });
	
    Route::middleware('role:admin|client')->group(function () {

        Route::group(['prefix' => '/client/profile', 'as' => 'clients.profile.'], function () {
            Route::get('/edit', 'ClientProfileController@edit')->name('edit');
            Route::put('/', 'ClientProfileController@update')->name('update');
        });
        Route::get('/addresses', 'InterpreterJobController@getaddress')->name('getaddress');
        Route::put('/quotes/{adminQuote}', 'AdminQuoteController@update')->name('admin-quotes.update');
        Route::delete('/quotes/{adminQuote}', 'AdminQuoteController@destroy')->name('admin-quotes.destroy');

        Route::put('/clients/{client}/update-seen-terms', 'ClientSeenTermsController@update')->name('client.seen-terms');


        Route::put('/translator-jobs/{translatorJob}/complete', 'CompletedTranslatorJobController@update')->name('translator-jobs.complete')->middleware('can:complete,translatorJob');
    });

    Route::middleware('role:new-agent')->group(function () {
        Route::get('/agent/profile/create', 'AgentProfileController@create')->name('agents.profile.create');
        Route::post('/agent/profile', 'AgentProfileController@store')->name('agents.profile.store');
    });

    Route::middleware('role:agent')->group(function () {
        Route::group(['prefix' => '/interpreter-jobs/{interpreterJob}', 'as' => 'interpreter-jobs.'], function () {
            Route::get('/', 'InterpreterJobController@show')->name('show')->middleware('can:view,interpreterJob');
            Route::post('/matched/reject', 'InterpreterJobMatchedAgentController@reject')->name('matched.reject')->middleware('can:view,interpreterJob');
            Route::delete('/matched/{matchedAgent}/unassign', 'InterpreterJobMatchedAgentController@unAssignForAgents')
            ->name('matched.unassign'); // Change the URL to "/unassign"
        });

        Route::group(['prefix' => '/translator-jobs/{translatorJob}', 'as' => 'translator-jobs.'], function () {
            Route::get('/', 'TranslatorJobController@show')->name('show')->middleware('can:view,translatorJob');
            Route::post('/matched/reject', 'TranslatorJobMatchedAgentController@reject')->name('matched.reject')->middleware('can:view,translatorJob');
        });
    });

    Route::middleware('role:new-agent|agent')->group(function () {
        Route::group(['prefix' => '/agent/profile', 'as' => 'agents.profile.'], function () {
            Route::get('/edit', 'AgentProfileController@edit')->name('edit');
            Route::put('/', 'AgentProfileController@update')->name('update');
            Route::get('/documents', 'AgentDocumentController@edit')->name('documents.edit');
        });
    });

    Route::middleware('role:admin')->group(function () {
        Route::group(['prefix' => '/companies', 'as' => 'companies.archived.'], function () {
            Route::get('/archived', 'ArchivedCompanyController@index')->name('index');
            Route::put('/archived/{company}', 'ArchivedCompanyController@update')->name('update');
            Route::delete('/archived/{company}', 'ArchivedCompanyController@destroy')->name('destroy');
        });

        Route::resource('/companies', 'CompanyController')->except('show');

        Route::group(['prefix' => '/interpreter-jobs/{interpreterJob}', 'as' => 'interpreter-jobs.'], function () {
            Route::get('/matched', 'InterpreterJobMatchedAgentController@index')->name('matched.index');
            Route::post('/matched', 'InterpreterJobMatchedAgentController@store')->name('matched.store')->middleware('can:assign,interpreterJob');
            Route::delete('/matched/{matchedAgent}', 'InterpreterJobMatchedAgentController@destroy')->name('matched.destroy')->middleware('can:assign,interpreterJob');
        });

        Route::group(['prefix' => '/translator-jobs/{translatorJob}', 'as' => 'translator-jobs.'], function () {
            Route::get('/matched', 'TranslatorJobMatchedAgentController@index')->name('matched.index');
            Route::post('/matched', 'TranslatorJobMatchedAgentController@store')->name('matched.store');
            Route::delete('/matched/{matchedAgent}', 'TranslatorJobMatchedAgentController@destroy')->name('matched.destroy');
        });

        Route::resource('/clients', 'ClientController')->except(['show']);
        // routes/web.php


        Route::get('/clients/{client}/toggle-show-agents', 'ClientController@toggleShowAgents')->name('clients.toggleShowAgents');


        Route::group(['prefix' => '/clients', 'as' => 'clients.'], function () {

            Route::group(['prefix' => '/new-requests', 'as' => 'new-requests.'], function () {
                Route::get('/', 'NewRequestController@index')->name('index');
                Route::put('/{client}', 'NewRequestController@update')->name('update');
                Route::delete('/{client}', 'NewRequestController@destroy')->name('destroy');

                Route::get('/{client}/client', 'NewRequestClientController@edit')->name('client.edit');
                Route::put('/{client}/client', 'NewRequestClientController@update')->name('client.update');

                Route::get('/{client}/job', 'NewRequestJobController@edit')->name('job.edit');
                Route::put('/{client}/job', 'NewRequestJobController@update')->name('job.update');
            });

            Route::group(['prefix' => '/archived', 'as' => 'archived.'], function () {
                Route::get('/', 'ArchivedClientController@index')->name('index');
                Route::get('/{client}', 'ArchivedClientController@show')->name('show');
                Route::put('/{client}', 'ArchivedClientController@update')->name('update');
                Route::delete('/{client}', 'ArchivedClientController@destroy')->name('destroy');
            });

            Route::group(['prefix' => '/rejected', 'as' => 'rejected.'], function () {
                Route::get('/', 'RejectedClientController@index')->name('index');
                Route::put('/{client}', 'RejectedClientController@update')->name('update');
            });
        });

        Route::group(['prefix' => '/agents', 'as' => 'agents.'], function () {

            Route::group(['prefix' => '/archived', 'as' => 'archived.'], function () {
                Route::get('/', 'ArchivedAgentController@index')->name('index');
                Route::put('/{agent}', 'ArchivedAgentController@update')->name('update');
                Route::get('/{agent}', 'ArchivedAgentController@show')->name('show');
                Route::delete('/{agent}', 'ArchivedAgentController@destroy')->name('destroy');
            });

            Route::get('/new', 'NewAgentController@index')->name('new.index');
            Route::put('/new/{agent}', 'NewAgentController@update')->name('new.update');
        });

        Route::group(['prefix' => '/agents/{agent}', 'as' => 'agents.documents.'], function () {
            Route::get('/documents/edit', 'AgentDocumentController@edit')->name('edit');
        });

        Route::resource('/agents', 'AgentController')->except('show');

        Route::post('/users/{user}/send-password-reset-link', 'ResetUserPassword')->name('users.send-password-reset-link');
    });

    Route::middleware('role:new-agent|agent|admin')->group(function () {
        Route::put('/agents/{agent}/documents', 'AgentDocumentController@update')->name('agents.documents.update');
    });

    Route::middleware('role:agent|admin')->group(function () {

        Route::group(['prefix' => '/interpreter-jobs/{interpreterJob}', 'as' => 'interpreter-jobs.'], function () {
            Route::put('/matched/{matchedAgent?}', 'InterpreterJobMatchedAgentController@update')->name('matched.update')->middleware('can:accept,interpreterJob');
            Route::post('/quotes', 'InterpreterJobQuoteController@store')->name('quotes.store')->middleware('can:viewQuotes,interpreterJob');
        });

        Route::group(['prefix' => '/translator-jobs/{translatorJob}', 'as' => 'translator-jobs.'], function () {
            Route::put('/matched/{matchedAgent?}', 'TranslatorJobMatchedAgentController@update')->name('matched.update')->middleware('can:accept,translatorJob');
            Route::post('/quotes', 'TranslatorJobQuoteController@store')->name('quotes.store')->middleware('can:viewQuotes,translatorJob');
        });
    });

    Route::middleware('role:super-admin')->group(function () {
        Route::resource('/admins', 'AdminController')->except('show');

    });
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::resource('/admins', 'AdminController')->except('show');
        Route::resource('/languages', 'LanguageController');

    });

    Route::resource('/feedback', 'FeedbackController');


    Route::delete('/documents/{document}/delete', 'DocumentController@destroy')->name('documents.destroy')->middleware('can:delete,document');

    Route::post('matched/{matchedAgent}/cancel', 'MatchedAgentCancellationController@store')->name('matched.cancel');

    Route::get('translator-jobs/{translatorJob}/documents', 'TranslatorJobDocumentController@index')->name('translator-jobs.documents.index')->middleware('can:viewDocuments,translatorJob');
    Route::post('translator-jobs/{translatorJob}/documents', 'TranslatorJobDocumentController@store')->name('translator-jobs.documents.store')->middleware('can:viewDocuments,translatorJob');

    Route::post('translator-jobs/{translatorJob}/comments', 'TranslatorJobCommentController@store')->name('translator-jobs.comments.store');

    Route::get('/{user}/impersonate', 'AdminController@impersonate')->name('impersonate');
    Route::get('/stop', 'AdminController@stopImpersonate')->name('unimpersonate');

    Route::resource('/timesheet', 'TimeSheetController');
    Route::get('/timesheet_status/{id}/{status}', 'TimeSheetController@updateStatus')->name('timesheet.status');
    Route::get('/feedback_status/{id}', 'FeedbackController@updateStatus')->name('feedback.status');

    Route::get('/timesheet-pdf/{id}', 'TimeSheetController@generateTimesheet')->name('timesheet-pdf');
});
