<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});



// guest routes public
Route::middleware('guest')->group(function(){
   
// all login routes will be there

});


// authenticated customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function(){

});

// authenticated vendor routes
Route::middleware(['auth','role:vendor','vendor.active'])->prefix('vendor')->name('vendor.')->group(function(){

});

// authenticated admin routes
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){

});






