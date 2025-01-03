<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', "App\Http\Controllers\HomeController@index")->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::group(['prefix' => 'users','middleware' => ['auth','admin']], function () {
    Route::get('/', "App\Http\Controllers\UserController@index")->name('users.index');
    Route::post('/', "App\Http\Controllers\UserController@store")->name('users.store');
    Route::get("/create", "App\Http\Controllers\UserController@create")->name('users.create');
    Route::get('/edit/{id}', "App\Http\Controllers\UserController@edit")->name('users.edit');
    Route::post('/update/{id}', "App\Http\Controllers\UserController@update")->name('users.update');
    Route::get('/delete/{id}', "App\Http\Controllers\UserController@delete")->name('users.delete');
    Route::get('/show/{id}', "App\Http\Controllers\UserController@show")->name('users.show');
    Route::get('/search', "App\Http\Controllers\UserController@search")->name('users.search');

});

Route::group(['prefix' => 'roles','middleware' => ['auth','admin']], function () {
    Route::get('/', "App\Http\Controllers\RoleController@index")->name('roles.index');
});

Route::group(['prefix' => 'api','middleware' => ['auth']], function () {
    Route::get('/users', "App\Http\Controllers\api\ApiUserController@index")->name('api.users');
    Route::put('/users/{id}', "App\Http\Controllers\api\ApiUserController@update")->name('api.users.update');
    Route::get('/roles', "App\Http\Controllers\api\ApiRoleController@index")->name('api.roles');
    Route::post('/roles',"App\Http\Controllers\api\ApiRoleController@store")->name('api.roles.store');
    Route::put('/roles/{id}',"App\Http\Controllers\api\ApiRoleController@update")->name('api.roles.update');
    Route::post('/customers',"App\Http\Controllers\api\ApiCustomerController@store")->name('api.customers.store');
    Route::get('/customers',"App\Http\Controllers\api\ApiCustomerController@index")->name('api.customers.index');
    Route::put('/customers/{id}',"App\Http\Controllers\api\ApiCustomerController@update")->name('api.customers.update');
    Route::get('/suppliers',"App\Http\Controllers\api\ApiSupplierController@index")->name('api.suppliers.index');
    Route::post('/suppliers',"App\Http\Controllers\api\ApiSupplierController@store")->name('api.suppliers.store');
    Route::put('/suppliers/{id}',"App\Http\Controllers\api\ApiSupplierController@update")->name('api.suppliers.update');
    Route::get('/categories',"App\Http\Controllers\api\ApiCategoryController@index")->name('api.categories.index');
    Route::post('/categories',"App\Http\Controllers\api\ApiCategoryController@store")->name('api.categories.store');
    Route::put('/categories/{id}',"App\Http\Controllers\api\ApiCategoryController@update")->name('api.categories.update');
    Route::get('units',"App\Http\Controllers\api\ApiUnitController@index")->name('api.units.index');
    Route::post('units',"App\Http\Controllers\api\ApiUnitController@store")->name('api.units.store');
    Route::put('units/{id}',"App\Http\Controllers\api\ApiUnitController@update")->name('api.units.update');
    Route::get('products',"App\Http\Controllers\api\ApiProductController@index")->name('api.products.index');
    Route::post('products',"App\Http\Controllers\api\ApiProductController@store")->name('api.products.store');
    Route::put('products/{id}',"App\Http\Controllers\api\ApiProductController@show")->name('api.products.show');
    Route::get('products/{id}/units',"App\Http\Controllers\api\ApiProductController@getUnits")->name('api.products.getUnits');
    Route::get('products/{id}',"App\Http\Controllers\api\ApiProductController@find")->name('api.products.find');
    Route::post('/products/{id}/addQuantity',"App\Http\Controllers\api\ApiProductController@addQuantity")->name('api.products.addQuantity');
    Route::get('stocks',"App\Http\Controllers\api\ApiStockController@index")->name('api.stocks.index');
    Route::post('stocks',"App\Http\Controllers\api\ApiStockController@store")->name('api.stocks.store');
    Route::put('stocks/{id}',"App\Http\Controllers\api\ApiStockController@update")->name('api.stocks.update');
    Route::get('stocks/{id}',"App\Http\Controllers\api\ApiStockController@find")->name('api.stocks.find');
    Route::post('stocks/{id}/addQuantity',"App\Http\Controllers\api\ApiStockController@addQuantity")->name('api.stocks.addQuantity');
    Route::get('/inventory',"App\Http\Controllers\api\ApiInventoryController@index")->name('api.inventory.index');
    Route::get('/inventory/all',"App\Http\Controllers\api\ApiInventoryController@all")->name('api.inventory.all');
    Route::get('/inventory/{id}',"App\Http\Controllers\api\ApiInventoryController@show")->name('api.inventory.show');
    Route::post('/inventory',"App\Http\Controllers\api\ApiInventoryController@store")->name('api.inventory.store');
    Route::put('/inventory/{id}',"App\Http\Controllers\api\ApiInventoryController@update")->name('api.inventory.update');
    Route::post('/pos',"App\Http\Controllers\api\ApiPosController@store")->name('api.pos.store');
    Route::get('/pos/getQuantity/{product_id}/{unit_id}',"App\Http\Controllers\api\ApiPosController@getQuantity")->name('api.pos.getQuantity');
    Route::post('/pos/sell',"App\Http\Controllers\api\ApiPosController@sell")->name('api.pos.sell');
    Route::get('/sales',"App\Http\Controllers\api\ApiSalesController@index")->name('api.sales.index');
    Route::delete('/sales/{id}/cancel',"App\Http\Controllers\api\ApiSalesController@cancel")->name('api.sales.cancel');
    Route::get('/sales/{id}',"App\Http\Controllers\api\ApiSalesController@find")->name('api.sales.find');
    Route::get('/sales/total/{id}',"App\Http\Controllers\api\ApiSalesController@total")->name('api.sales.total');
    Route::get('/payments',"App\Http\Controllers\api\ApiPaymentController@index")->name('api.payments.index');
    Route::post('/payments',"App\Http\Controllers\api\ApiPaymentController@store")->name('api.payments.store');
    Route::get('/payments/{id}',"App\Http\Controllers\api\ApiPaymentController@show")->name('api.payments.show');
    Route::get('/payment_methods',"App\Http\Controllers\api\ApiPaymentMethodController@index")->name('api.payment_methods.index');
    Route::post('/invoices',"App\Http\Controllers\api\ApiInvoiceController@store")->name('api.invoices.store');
    Route::get('/invoices',"App\Http\Controllers\api\ApiInvoiceController@index")->name('api.invoices.index');
    Route::get('/invoices/{uuid}',"App\Http\Controllers\api\ApiInvoiceController@find")->name('api.invoices.find');
    Route::get('/expenses', "App\Http\Controllers\api\ApiExpenseController@index")->name('api.expenses.index');
    Route::post('/expenses', "App\Http\Controllers\api\ApiExpenseController@store")->name('api.expenses.store');
    Route::put('/expenses/{expense}', "App\Http\Controllers\api\ApiExpenseController@update")->name('api.expenses.update');
    Route::get('/expense_categories',"App\Http\Controllers\api\ApiExpenseCategoryController@index")->name('api.expense_categories.index');
    Route::post('/stocks/convert',"App\Http\Controllers\api\ApiStockController@convert")->name('api.stocks.convert');
    Route::get('/discounts',"App\Http\Controllers\api\ApiDiscountController@index")->name('api.discounts.index');
    Route::post('/discounts',"App\Http\Controllers\api\ApiDiscountController@store")->name('api.discounts.store');
    Route::put('/discounts/{id}',"App\Http\Controllers\api\ApiDiscountController@update")->name('api.discounts.update');
    Route::delete('/discounts/product/{productId}/{discountId}',"App\Http\Controllers\api\ApiDiscountController@removeToProduct")->name('api.discounts.removeToProduct');
    Route::post('/discounts/product/{id}','App\Http\Controllers\api\ApiDiscountController@applyToProduct')->name('api.discounts.applyToProduct');
    Route::delete('/discounts/{id}','App\Http\Controllers\api\ApiDiscountController@destroy')->name('api.discounts.destroy');
    Route::get('discount_types',"App\Http\Controllers\api\ApiDiscountTypeController@index")->name('api.discount_types.index');  
});

Route::group(
    ['prefix' => 'customers', 'middleware' => ['auth', 'admin']],
    function () {
        Route::get('/', 'App\Http\Controllers\CustomerController@index')->name('customers.index');
        Route::post('/', 'App\Http\Controllers\CustomerController@store')->name('customers.store');
        Route::get('/create', 'App\Http\Controllers\CustomerController@create')->name('customers.create');
    }
);

Route::group(['prefix' => 'suppliers', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'App\Http\Controllers\SupplierController@index')->name('suppliers.index');
    Route::post('/', 'App\Http\Controllers\SupplierController@store')->name('suppliers.store');
    Route::get('/create', 'App\Http\Controllers\SupplierController@create')->name('suppliers.create');
});

Route::group(['prefix' => 'categories', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'App\Http\Controllers\CategoryController@index')->name('categories.index');
    Route::post('/', 'App\Http\Controllers\CategoryController@store')->name('categories.store');
    Route::get('/create', 'App\Http\Controllers\CategoryController@create')->name('categories.create');
});

Route::group(['prefix'=>'units','middleware'=>['auth','admin']],function(){
    Route::get('/','App\Http\Controllers\UnitController@index')->name('units.index');
    Route::post('/','App\Http\Controllers\UnitController@store')->name('units.store');
    Route::get('/create','App\Http\Controllers\UnitController@create')->name('units.create');
});

Route::group(['prefix'=>'products','middleware'=>['auth','admin']],function(){
    Route::get('/','App\Http\Controllers\ProductController@index')->name('products.index');
    Route::post('/','App\Http\Controllers\ProductController@store')->name('products.store');
    Route::get('/create','App\Http\Controllers\ProductController@create')->name('products.create');
    Route::put('/{id}','App\Http\Controllers\ProductController@update')->name('products.update');
});

Route::group(['prefix' => 'stocks', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'App\Http\Controllers\StockController@index')->name('stocks.index');
    Route::post('/', 'App\Http\Controllers\StockController@store')->name('stocks.store');
    Route::get('/create', 'App\Http\Controllers\StockController@create')->name('stocks.create');
    Route::get('/convert','App\Http\Controllers\StockController@convert')->name('stocks.convert');
});

Route::group(['prefix' => 'inventory', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/', 'App\Http\Controllers\InventoryController@index')->name('inventory.index');
    Route::get('/all', 'App\Http\Controllers\InventoryController@all')->name('inventory.all');
    Route::post('/', 'App\Http\Controllers\InventoryController@store')->name('inventory.store');
    Route::get('/create', 'App\Http\Controllers\InventoryController@create')->name('inventory.create');
});

Route::group(['prefix'=>'pos','middleware'=>'auth'], function (){
    Route::get('/','App\Http\Controllers\PosController@index')->name('pos.index');
    Route::post('/','App\Http\Controllers\PosController@store')->name('pos.store');
});

Route::group(['prefix'=>'sales','middleware'=>['auth']],function(){
    Route::get('/','App\Http\Controllers\SaleController@index')->name('sales.index');
    Route::get('/report','App\Http\Controllers\SaleController@report')->name('sales.report');
    Route::get('/{id}','App\Http\Controllers\SaleController@show')->name('sales.show');
});

Route::group(['prefix'=>'payments','middleware'=>['auth']],function(){
    Route::get('/','App\Http\Controllers\PaymentController@index')->name('payments.index');
    Route::get('/create','App\Http\Controllers\PaymentController@create')->name('payments.create');
    Route::get('/{id}','App\Http\Controllers\PaymentController@show')->name('payments.show');
});

Route::group(['prefix' => 'invoices', 'middleware' => ['auth']], function (){
    Route::get('/', "App\Http\Controllers\InvoiceController@index")->name('invoices.index');
    Route::get('/create', "App\Http\Controllers\InvoiceController@create")->name('invoices.create');
    Route::get('/show/{invoice}', "App\Http\Controllers\InvoiceController@show")->name('invoices.show');
});

Route::group(['prefix'=>'expenses', 'middleware' => 'auth'], function (){
   Route::get('/', "App\Http\Controllers\ExpenseController@index")->name('expenses.index');
   Route::get('/create', "App\Http\Controllers\ExpenseController@create")->name('expenses.create');
   Route::get('/show', "App\Http\Controllers\ExpenseController@show")->name('expenses.show');
});

Route::group(['prefix'=>'discounts','middleware'=>['auth','admin']],function(){
    Route::get('/','App\Http\Controllers\DiscountController@index')->name('discounts.index');
    Route::get('/create','App\Http\Controllers\DiscountController@create')->name('discounts.create');
    Route::get('/edit/{id}','App\Http\Controllers\DiscountController@edit')->name('discounts.edit');
});