<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes (only for guests)
Route::middleware('guest')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
});

// Logout route (requires authentication)
Route::match(['get', 'post'], '/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Session management routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('sessions', [App\Http\Controllers\SessionController::class, 'index'])->name('sessions.index');
    Route::delete('sessions/{sessionId}', [App\Http\Controllers\SessionController::class, 'revoke'])->name('sessions.revoke');
    Route::post('sessions/revoke-all-others', [App\Http\Controllers\SessionController::class, 'revokeAllOthers'])->name('sessions.revoke-all-others');
});

// Profile routes (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::get('profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/change-password', [App\Http\Controllers\ProfileController::class, 'changePassword'])->name('profile.change-password');
});

// format for calling the controller method in the route
// users -> folder
// user -> filename
// index -> method in the controller

// User routes
Route::middleware(['auth', 'user'])->group(function () {
    Route::get('users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
    Route::get('users/all-items', [App\Http\Controllers\UserController::class, 'allItems'])->name('users.all-items');
    Route::get('users/search-items', [App\Http\Controllers\UserController::class, 'searchItems'])->name('users.search-items');
    Route::post('users/item/{itemId}/borrow', [App\Http\Controllers\UserController::class, 'borrow'])->name('users.item.borrow');
    Route::post('users/all-items/item/{itemId}/borrow', [App\Http\Controllers\UserController::class, 'borrowAllItems'])->name('users.all-items.item.borrow');
    Route::post('users/borrow-history/{borrowHistory}/return', [App\Http\Controllers\UserController::class, 'returnItem'])->name('users.borrow-history.return');
    Route::post('users/borrow-request/{borrowRequest}/cancel', [App\Http\Controllers\UserController::class, 'cancelBorrowRequest'])->name('users.borrow-request.cancel');
    Route::get('users/check-changes', [App\Http\Controllers\ApiController::class, 'checkUserChanges'])->name('users.check-changes');
});

// Resource Officer routes
Route::middleware(['auth', 'resource-officer'])->group(function () {
    Route::get('resource-officer', [App\Http\Controllers\ResourceOfficerController::class, 'index'])->name('resource-officer');
    Route::get('resource-officer/search-items', [App\Http\Controllers\ResourceOfficerController::class, 'searchItems'])->name('resource-officer.search-items');
    Route::get('resource-officer/borrow-history/export', [App\Http\Controllers\ResourceOfficerController::class, 'exportBorrowHistory'])->name('resource-officer.borrow-history.export');
    Route::get('resource-officer/form', [App\Http\Controllers\ResourceOfficerController::class, 'form'])->name('resource-officer.form');
    Route::get('resource-officer/import', [App\Http\Controllers\ResourceOfficerController::class, 'importForm'])->name('resource-officer.import');
    Route::post('resource-officer/import', [App\Http\Controllers\ResourceOfficerController::class, 'import'])->name('resource-officer.import.upload');
    Route::get('resource-officer/import-engineering', [App\Http\Controllers\ResourceOfficerController::class, 'importEngineeringForm'])->name('resource-officer.import-engineering');
    Route::post('resource-officer/import-engineering', [App\Http\Controllers\ResourceOfficerController::class, 'importEngineering'])->name('resource-officer.import-engineering.upload');
    Route::get('resource-officer/import-operation', [App\Http\Controllers\ResourceOfficerController::class, 'importOperationForm'])->name('resource-officer.import-operation');
    Route::post('resource-officer/import-operation', [App\Http\Controllers\ResourceOfficerController::class, 'importOperation'])->name('resource-officer.import-operation.upload');
    Route::get('resource-officer/import-mechanical', [App\Http\Controllers\ResourceOfficerController::class, 'importMechanicalForm'])->name('resource-officer.import-mechanical');
    Route::post('resource-officer/import-mechanical', [App\Http\Controllers\ResourceOfficerController::class, 'importMechanical'])->name('resource-officer.import-mechanical.upload');
    Route::post('resource-officer/inventory', [App\Http\Controllers\ResourceOfficerController::class, 'inventory'])->name('resource-officer.inventory');
    Route::get('resource-officer/item/{item}/edit', [App\Http\Controllers\ResourceOfficerController::class, 'edit'])->name('resource-officer.item.edit');
    Route::put('resource-officer/item/{item}', [App\Http\Controllers\ResourceOfficerController::class, 'update'])->name('resource-officer.item.update');
    Route::delete('resource-officer/item/{item}', [App\Http\Controllers\ResourceOfficerController::class, 'destroy'])->name('resource-officer.item.destroy');
    Route::post('resource-officer/borrow-request/{borrowRequest}/release', [App\Http\Controllers\ResourceOfficerController::class, 'releaseBorrowRequest'])->name('resource-officer.borrow-request.release');
    Route::post('resource-officer/return/{borrowHistory}/approve', [App\Http\Controllers\ResourceOfficerController::class, 'approveReturn'])->name('resource-officer.return.approve');
    Route::post('resource-officer/return/{borrowHistory}/reject', [App\Http\Controllers\ResourceOfficerController::class, 'rejectReturn'])->name('resource-officer.return.reject');
    Route::get('resource-officer/check-changes', [App\Http\Controllers\ApiController::class, 'checkResourceOfficerChanges'])->name('resource-officer.check-changes');
});

// Manager routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('manager', [App\Http\Controllers\AdminController::class, 'index'])->name('manager');

    Route::get('manager/view', [App\Http\Controllers\AdminController::class, 'adminView'])->name('manager.view');
    Route::get('manager/search-items', [App\Http\Controllers\AdminController::class, 'searchAdminItems'])->name('manager.search-items');

    Route::get('admin/forms', [App\Http\Controllers\AdminController::class, 'adminForms'])->name('admin.forms');
    Route::post('admin/inventory', [App\Http\Controllers\AdminController::class, 'inventory'])->name('admin.inventory');

    Route::post('manager/borrow-request/{borrowRequest}/approve', [App\Http\Controllers\AdminController::class, 'approveBorrowRequest'])->name('manager.borrow-request.approve');

    Route::post('manager/borrow-request/{borrowRequest}/reject', [App\Http\Controllers\AdminController::class, 'rejectBorrowRequest'])->name('manager.borrow-request.reject');

    Route::get('manager/check-changes', [App\Http\Controllers\ApiController::class, 'checkAdminChanges'])->name('manager.check-changes');

});

// Super Admin routes for user management
Route::middleware(['auth', 'superadmin'])->group(function () {
    Route::get('superadmin', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('superadmin/users', [App\Http\Controllers\SuperAdminController::class, 'userManagement'])->name('superadmin.user-management');
    Route::post('superadmin/users/{user}/approve', [App\Http\Controllers\SuperAdminController::class, 'approveUser'])->name('superadmin.user.approve');
    Route::post('superadmin/users/{user}/reject', [App\Http\Controllers\SuperAdminController::class, 'rejectUser'])->name('superadmin.user.reject');
    Route::post('superadmin/users/{user}/deactivate', [App\Http\Controllers\SuperAdminController::class, 'deactivateUser'])->name('superadmin.user.deactivate');
    Route::post('superadmin/users/{user}/reactivate', [App\Http\Controllers\SuperAdminController::class, 'reactivateUser'])->name('superadmin.user.reactivate');
    Route::put('superadmin/users/{user}', [App\Http\Controllers\SuperAdminController::class, 'updateUser'])->name('superadmin.user.update');
});
