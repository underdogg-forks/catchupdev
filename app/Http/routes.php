<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

// Application setup
Route::get('/setup', 'AppController@showSetup');
Route::post('/setup', 'AppController@doSetup');
Route::get('/install', 'AppController@install');
Route::get('/update', 'AppController@update');

// Public pages
Route::get('/', 'HomeController@showIndex');
Route::get('/log_error', 'HomeController@logError');
Route::get('/invoice_now', 'HomeController@invoiceNow');
Route::get('/keep_alive', 'HomeController@keepAlive');
Route::post('/get_started', 'CompanyController@getStarted');

// Client visible pages
Route::group(['middleware' => 'auth:client'], function () {
    Route::get('view/{invitation_key}', 'ClientPortalController@view');
    Route::get('download/{invitation_key}', 'ClientPortalController@download');
    Route::put('sign/{invitation_key}', 'ClientPortalController@sign');
    Route::get('view', 'HomeController@viewLogo');
    Route::get('approve/{invitation_key}', 'QuoteController@approve');
    Route::get('payment/{invitation_key}/{gateway_type?}/{source_id?}', 'OnlinePaymentController@showPayment');
    Route::post('payment/{invitation_key}', 'OnlinePaymentController@doPayment');
    Route::match(['GET', 'POST'], 'complete/{invitation_key?}/{gateway_type?}', 'OnlinePaymentController@offsitePayment');
    Route::get('bank/{routing_number}', 'OnlinePaymentController@getBankInfo');
    Route::get('client/payment_methods', 'ClientPortalController@paymentMethods');
    Route::post('client/payment_methods/verify', 'ClientPortalController@verifyPaymentMethod');
    //Route::get('client/payment_methods/add/{gateway_type}/{source_id?}', 'ClientPortalController@addPaymentMethod');
    //Route::post('client/payment_methods/add/{gateway_type}', 'ClientPortalController@postAddPaymentMethod');
    Route::post('client/payment_methods/default', 'ClientPortalController@setDefaultPaymentMethod');
    Route::post('client/payment_methods/{source_id}/remove', 'ClientPortalController@removePaymentMethod');
    Route::get('client/quotes', 'ClientPortalController@quoteIndex');
    Route::get('client/credits', 'ClientPortalController@creditIndex');
    Route::get('client/invoices', 'ClientPortalController@invoiceIndex');
    Route::get('client/invoices/recurring', 'ClientPortalController@recurringInvoiceIndex');
    Route::post('client/invoices/auto_bill', 'ClientPortalController@setAutoBill');
    Route::get('client/documents', 'ClientPortalController@documentIndex');
    Route::get('client/payments', 'ClientPortalController@paymentIndex');
    Route::get('client/dashboard/{contact_key?}', 'ClientPortalController@dashboard');
    Route::get('client/documents/js/{documents}/{filename}', 'ClientPortalController@getDocumentVFSJS');
    Route::get('client/documents/{invitation_key}/{documents}/{filename?}', 'ClientPortalController@getDocument');
    Route::get('client/documents/{invitation_key}/{filename?}', 'ClientPortalController@getInvoiceDocumentsZip');

    Route::get('api/client.quotes', ['as' => 'api.client.quotes', 'uses' => 'ClientPortalController@quoteDatatable']);
    Route::get('api/client.credits', ['as' => 'api.client.credits', 'uses' => 'ClientPortalController@creditDatatable']);
    Route::get('api/client.invoices', ['as' => 'api.client.invoices', 'uses' => 'ClientPortalController@invoiceDatatable']);
    Route::get('api/client.recurring_invoices', ['as' => 'api.client.recurring_invoices', 'uses' => 'ClientPortalController@recurringInvoiceDatatable']);
    Route::get('api/client.documents', ['as' => 'api.client.documents', 'uses' => 'ClientPortalController@documentDatatable']);
    Route::get('api/client.payments', ['as' => 'api.client.payments', 'uses' => 'ClientPortalController@paymentDatatable']);
    Route::get('api/client.activity', ['as' => 'api.client.activity', 'uses' => 'ClientPortalController@activityDatatable']);
});

Route::get('license', 'NinjaController@show_license_payment');
Route::post('license', 'NinjaController@do_license_payment');
Route::get('claim_license', 'NinjaController@claim_license');

Route::post('signup/validate', 'CompanyController@checkEmail');
Route::post('signup/submit', 'CompanyController@submitSignup');

Route::get('/auth/{provider}', 'Auth\AuthController@authLogin');
Route::get('/auth_unlink', 'Auth\AuthController@authUnlink');
Route::match(['GET', 'POST'], '/buy_now/{gateway_type?}', 'OnlinePaymentController@handleBuyNow');

Route::post('/hook/email_bounced', 'AppController@emailBounced');
Route::post('/hook/email_opened', 'AppController@emailOpened');
Route::post('/hook/bot/{platform?}', 'BotController@handleMessage');
Route::post('/payment_hook/{companyKey}/{gatewayId}', 'OnlinePaymentController@handlePaymentWebhook');

// Laravel auth routes
Route::get('/signup', ['as' => 'signup', 'uses' => 'Auth\AuthController@getRegister']);
Route::post('/signup', ['as' => 'signup', 'uses' => 'Auth\AuthController@postRegister']);
Route::get('/login', ['as' => 'login', 'uses' => 'Auth\AuthController@getLoginWrapper']);
Route::post('/login', ['as' => 'login', 'uses' => 'Auth\AuthController@postLoginWrapper']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogoutWrapper']);
Route::get('/recover_password', ['as' => 'forgot', 'uses' => 'Auth\PasswordController@getEmail']);
Route::post('/recover_password', ['as' => 'forgot', 'uses' => 'Auth\PasswordController@postEmail']);
Route::get('/password/reset/{token}', ['as' => 'forgot', 'uses' => 'Auth\PasswordController@getReset']);
Route::post('/password/reset', ['as' => 'forgot', 'uses' => 'Auth\PasswordController@postReset']);
Route::get('/user/confirm/{code}', 'UserController@confirm');

// Client auth
Route::get('/client/login', ['as' => 'login', 'uses' => 'ClientAuth\AuthController@getLogin']);
Route::post('/client/login', ['as' => 'login', 'uses' => 'ClientAuth\AuthController@postLogin']);
Route::get('/client/logout', ['as' => 'logout', 'uses' => 'ClientAuth\AuthController@getLogout']);
Route::get('/client/sessionexpired', ['as' => 'logout', 'uses' => 'ClientAuth\AuthController@getSessionExpired']);
Route::get('/client/recover_password', ['as' => 'forgot', 'uses' => 'ClientAuth\PasswordController@getEmail']);
Route::post('/client/recover_password', ['as' => 'forgot', 'uses' => 'ClientAuth\PasswordController@postEmail']);
Route::get('/client/password/reset/{invitation_key}/{token}', ['as' => 'forgot', 'uses' => 'ClientAuth\PasswordController@getReset']);
Route::post('/client/password/reset', ['as' => 'forgot', 'uses' => 'ClientAuth\PasswordController@postReset']);

if (Utils::isNinja()) {
    Route::post('/signup/register', 'CompanyController@doRegister');
    Route::get('/news_feed/{user_type}/{version}/', 'HomeController@newsFeed');
    Route::get('/demo', 'CompanyController@demo');
}

if (Utils::isReseller()) {
    Route::post('/reseller_stats', 'AppController@stats');
}

Route::group(['middleware' => 'auth:user'], function () {
    Route::get('dashboard', 'DashboardController@index');
    Route::get('dashboard_chart_data/{group_by}/{start_date}/{end_date}/{currency_id}/{include_expenses}', 'DashboardController@chartData');
    Route::get('set_entity_filter/{entity_type}/{filter?}', 'CompanyController@setEntityFilter');
    Route::get('hide_message', 'HomeController@hideMessage');
    Route::get('force_inline_pdf', 'UserController@forcePDFJS');
    Route::get('company/get_search_data', ['as' => 'get_search_data', 'uses' => 'CompanyController@getSearchData']);
    Route::get('check_invoice_number/{invoice_id?}', 'InvoiceController@checkInvoiceNumber');
    Route::post('save_sidebar_state', 'UserController@saveSidebarState');
    Route::post('contact_us', 'HomeController@contactUs');

    Route::get('settings/user_details', 'CompanyController@showUserDetails');
    Route::post('settings/user_details', 'CompanyController@saveUserDetails');
    Route::post('settings/payment_gateway_limits', 'CompanyController@savePaymentGatewayLimits');
    Route::post('users/change_password', 'UserController@changePassword');

    Route::resource('clients', 'ClientController');
    Route::get('api/clients', 'ClientController@getDatatable');
    Route::get('api/activities/{client_id?}', 'ActivityController@getDatatable');
    Route::post('clients/bulk', 'ClientController@bulk');
    Route::get('clients/statement/{client_id}', 'ClientController@statement');

    Route::resource('tasks', 'TaskController');
    Route::get('api/tasks/{client_id?}', 'TaskController@getDatatable');
    Route::get('tasks/create/{client_id?}/{project_id?}', 'TaskController@create');
    Route::post('tasks/bulk', 'TaskController@bulk');
    Route::get('projects', 'ProjectController@index');
    Route::get('api/projects', 'ProjectController@getDatatable');
    Route::get('projects/create/{client_id?}', 'ProjectController@create');
    Route::post('projects', 'ProjectController@store');
    Route::put('projects/{projects}', 'ProjectController@update');
    Route::get('projects/{projects}/edit', 'ProjectController@edit');
    Route::post('projects/bulk', 'ProjectController@bulk');

    Route::get('api/recurring_invoices/{client_id?}', 'InvoiceController@getRecurringDatatable');

    Route::get('invoices/invoice_history/{invoice_id}', 'InvoiceController@invoiceHistory');
    Route::get('quotes/quote_history/{invoice_id}', 'InvoiceController@invoiceHistory');

    Route::resource('invoices', 'InvoiceController');
    Route::get('api/invoices/{client_id?}', 'InvoiceController@getDatatable');
    Route::get('invoices/create/{client_id?}', 'InvoiceController@create');
    Route::get('recurring_invoices/create/{client_id?}', 'InvoiceController@createRecurring');
    Route::get('recurring_invoices', 'RecurringInvoiceController@index');
    Route::get('recurring_invoices/{invoices}/edit', 'InvoiceController@edit');
    Route::get('invoices/{invoices}/clone', 'InvoiceController@cloneInvoice');
    Route::post('invoices/bulk', 'InvoiceController@bulk');
    Route::post('recurring_invoices/bulk', 'InvoiceController@bulk');

    Route::get('documents/{documents}/{filename?}', 'DocumentController@get');
    Route::get('documents/js/{documents}/{filename}', 'DocumentController@getVFSJS');
    Route::get('documents/preview/{documents}/{filename?}', 'DocumentController@getPreview');
    Route::post('documents', 'DocumentController@postUpload');
    Route::delete('documents/{documents}', 'DocumentController@delete');

    Route::get('quotes/create/{client_id?}', 'QuoteController@create');
    Route::get('quotes/{invoices}/clone', 'InvoiceController@cloneInvoice');
    Route::get('quotes/{invoices}/edit', 'InvoiceController@edit');
    Route::put('quotes/{invoices}', 'InvoiceController@update');
    Route::get('quotes/{invoices}', 'InvoiceController@edit');
    Route::post('quotes', 'InvoiceController@store');
    Route::get('quotes', 'QuoteController@index');
    Route::get('api/quotes/{client_id?}', 'QuoteController@getDatatable');
    Route::post('quotes/bulk', 'QuoteController@bulk');

    Route::resource('payments', 'PaymentController');
    Route::get('payments/create/{client_id?}/{invoice_id?}', 'PaymentController@create');
    Route::get('api/payments/{client_id?}', 'PaymentController@getDatatable');
    Route::post('payments/bulk', 'PaymentController@bulk');

    Route::resource('credits', 'CreditController');
    Route::get('credits/create/{client_id?}/{invoice_id?}', 'CreditController@create');
    Route::get('api/credits/{client_id?}', 'CreditController@getDatatable');
    Route::post('credits/bulk', 'CreditController@bulk');

    Route::get('api/products', 'ProductController@getDatatable');
    Route::resource('products', 'ProductController');
    Route::post('products/bulk', 'ProductController@bulk');

    Route::get('/resend_confirmation', 'CompanyController@resendConfirmation');
    Route::post('/update_setup', 'AppController@updateSetup');

    // vendor
    Route::resource('vendors', 'VendorController');
    Route::get('api/vendors', 'VendorController@getDatatable');
    Route::post('vendors/bulk', 'VendorController@bulk');

    // Expense
    Route::resource('expenses', 'ExpenseController');
    Route::get('expenses/create/{vendor_id?}/{client_id?}/{category_id?}', 'ExpenseController@create');
    Route::get('api/expenses', 'ExpenseController@getDatatable');
    Route::get('api/expenses/{id}', 'ExpenseController@getDatatableVendor');
    Route::post('expenses/bulk', 'ExpenseController@bulk');
    Route::get('expense_categories', 'ExpenseCategoryController@index');
    Route::get('api/expense_categories', 'ExpenseCategoryController@getDatatable');
    Route::get('expense_categories/create', 'ExpenseCategoryController@create');
    Route::post('expense_categories', 'ExpenseCategoryController@store');
    Route::put('expense_categories/{expense_categories}', 'ExpenseCategoryController@update');
    Route::get('expense_categories/{expense_categories}/edit', 'ExpenseCategoryController@edit');
    Route::post('expense_categories/bulk', 'ExpenseCategoryController@bulk');

    // BlueVine
    Route::post('bluevine/signup', 'BlueVineController@signup');
    Route::get('bluevine/hide_message', 'BlueVineController@hideMessage');
    Route::get('bluevine/completed', 'BlueVineController@handleCompleted');
    Route::get('white_label/hide_message', 'NinjaController@hideWhiteLabelMessage');

    Route::get('reports', 'ReportController@showReports');
    Route::post('reports', 'ReportController@showReports');
});

Route::group([
    'middleware' => ['auth:user', 'permissions.required'],
    'permissions' => 'admin',
], function () {
    Route::get('api/users', 'UserController@getDatatable');
    Route::resource('users', 'UserController');
    Route::post('users/bulk', 'UserController@bulk');
    Route::get('send_confirmation/{user_id}', 'UserController@sendConfirmation');
    Route::get('/switch_company/{user_id}', 'UserController@switchCompany');
    Route::get('/company/{company_key}', 'UserController@viewCompanyByKey');
    Route::get('/unlink_company/{user_company_id}/{user_id}', 'UserController@unlinkCompany');
    Route::get('/manage_corporations', 'UserController@manageCompanies');

    Route::get('api/tokens', 'TokenController@getDatatable');
    Route::resource('tokens', 'TokenController');
    Route::post('tokens/bulk', 'TokenController@bulk');

    Route::get('api/tax_rates', 'TaxRateController@getDatatable');
    Route::resource('tax_rates', 'TaxRateController');
    Route::post('tax_rates/bulk', 'TaxRateController@bulk');

    Route::get('settings/email_preview', 'CompanyController@previewEmail');
    Route::post('settings/client_portal', 'CompanyController@saveClientPortalSettings');
    Route::post('settings/email_settings', 'CompanyController@saveEmailSettings');
    Route::get('corporation/{section}/{subSection?}', 'CompanyController@redirectLegacy');
    Route::get('settings/data_visualizations', 'ReportController@d3');

    Route::post('settings/change_plan', 'CompanyController@changePlan');
    Route::post('settings/cancel_company', 'CompanyController@cancelCompany');
    Route::post('settings/company_details', 'CompanyController@updateDetails');
    Route::post('settings/{section?}', 'CompanyController@doSection');

    Route::post('user/setTheme', 'UserController@setTheme');
    Route::post('remove_logo', 'CompanyController@removeLogo');

    Route::post('/export', 'ExportController@doExport');
    Route::post('/import', 'ImportController@doImport');
    Route::post('/import_csv', 'ImportController@doImportCSV');

    Route::get('gateways/create/{show_wepay?}', 'CompanyGatewayController@create');
    Route::resource('gateways', 'CompanyGatewayController');
    Route::get('gateways/{public_id}/resend_confirmation', 'CompanyGatewayController@resendConfirmation');
    Route::get('api/gateways', 'CompanyGatewayController@getDatatable');
    Route::post('company_gateways/bulk', 'CompanyGatewayController@bulk');

    Route::get('bank_companies/import_ofx', 'BankAccountController@showImportOFX');
    Route::post('bank_companies/import_ofx', 'BankAccountController@doImportOFX');
    Route::resource('bank_companies', 'BankAccountController');
    Route::get('api/bank_companies', 'BankAccountController@getDatatable');
    Route::post('bank_companies/bulk', 'BankAccountController@bulk');
    Route::post('bank_companies/validate', 'BankAccountController@validateCompany');
    Route::post('bank_companies/import_expenses/{bank_id}', 'BankAccountController@importExpenses');
    Route::get('self-update', 'SelfUpdateController@index');
    Route::post('self-update', 'SelfUpdateController@update');
    Route::get('self-update/download', 'SelfUpdateController@download');
});

Route::group(['middleware' => 'auth:user'], function () {
    Route::get('settings/{section?}', 'CompanyController@showSection');
});

// Route groups for API
Route::group(['middleware' => 'api', 'prefix' => 'api/v1'], function () {
    Route::get('ping', 'CompanyApiController@ping');
    Route::post('login', 'CompanyApiController@login');
    Route::post('oauth_login', 'CompanyApiController@oauthLogin');
    Route::post('register', 'CompanyApiController@register');
    Route::get('static', 'CompanyApiController@getStaticData');
    Route::get('companies', 'CompanyApiController@show');
    Route::put('companies', 'CompanyApiController@update');
    Route::resource('clients', 'ClientApiController');
    Route::get('quotes', 'QuoteApiController@index');
    Route::get('invoices', 'InvoiceApiController@index');
    Route::get('download/{invoice_id}', 'InvoiceApiController@download');
    Route::resource('invoices', 'InvoiceApiController');
    Route::resource('payments', 'PaymentApiController');
    Route::get('tasks', 'TaskApiController@index');
    Route::resource('tasks', 'TaskApiController');
    Route::post('hooks', 'IntegrationController@subscribe');
    Route::post('email_invoice', 'InvoiceApiController@emailInvoice');
    Route::get('user_companies', 'CompanyApiController@getUserCompanies');
    Route::resource('products', 'ProductApiController');
    Route::resource('tax_rates', 'TaxRateApiController');
    Route::resource('users', 'UserApiController');
    Route::resource('expenses', 'ExpenseApiController');
    Route::post('add_token', 'CompanyApiController@addDeviceToken');
    Route::post('update_notifications', 'CompanyApiController@updatePushNotifications');
    Route::get('dashboard', 'DashboardApiController@index');
    Route::resource('documents', 'DocumentAPIController');
    Route::resource('vendors', 'VendorApiController');
    Route::resource('expense_categories', 'ExpenseCategoryApiController');
});


/*
if (Utils::isNinjaDev())
{
  //ini_set('memory_limit','1024M');
  //set_time_limit(0);
  Auth::loginUsingId(1);
}
*/

// Include static app constants
require_once app_path() . '/Constants.php';
