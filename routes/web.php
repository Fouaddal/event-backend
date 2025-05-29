<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ServiceController;
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
Route::get('/dashboard', [AdminController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/admin/provider-requests', [AdminController::class, 'listProviderRequests'])
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('admin.providerRequests');

Route::post('/admin/provider-requests/{id}/approve', [AdminController::class, 'approveProviderRequest'])
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('admin.approve');

Route::post('/admin/provider-requests/{id}/reject', [AdminController::class, 'rejectProviderRequest'])
    ->middleware(['auth', 'verified', 'is_admin'])
    ->name('admin.reject');


Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/services', [ServiceController::class, 'unapprovedServices'])->name('services.index');
    Route::post('/services/{id}/approve', [ServiceController::class, 'approve'])->name('services.approve');
    Route::post('/services/{id}/reject', [ServiceController::class, 'reject'])->name('services.reject');
});




/*Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/provider-requests', [AdminController::class, 'listProviderRequests'])
    ->middleware(['auth', 'verified', 'is_admin']) // Ensure the user is authenticated, verified, and an admin
    ->name('admin.providerRequests');

     Route::get('login', [AuthenticatedSessionController::class, 'create'])
                ->name('login');

                use Illuminate\Support\Facades\Redirect;

Route::fallback(function () {
    return Redirect::route('login');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

require __DIR__.'/auth.php';
