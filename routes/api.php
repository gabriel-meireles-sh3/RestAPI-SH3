<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TicketController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Expr\FuncCall;

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

Route::middleware('api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'signIn']);
    Route::post('/register', [AuthController::class, 'signUp']);
});

Route::middleware('api')->group(function () {
    // ADMIN ROUTES
    Route::middleware('role_check:admin')->group(function () {
        Route::get('/getSupportList', [AuthController::class, 'findAllSupport'])->name('listSupport');
        Route::delete('/deleteTicket', [TicketController::class, 'deleteById'])->name('deleteTicket');
        Route::delete('/deleteService', [ServiceController::class, 'deleteById'])->name('deleteService');
        Route::post('/restoreTicket', [TicketController::class, 'restoreById'])->name('restoreTicket');
        Route::post('/restoreService', [ServiceController::class, 'restoreById'])->name('restoreService');
        Route::put('/putService', [ServiceController::class, 'update'])->name('editService');
        Route::get('/getServicesAreas', [ServiceController::class, 'servicesArea'])->name('listServicesAreas');
        Route::get('/getServicesTypes', [ServiceController::class, 'servicesTypes'])->name('listServicesTypes');
        Route::get('/getIncompletedServices', [ServiceController::class, 'incompletedServices'])->name('listIncompletedServices');
        Route::get('/getCompletedServices', [ServiceController::class, 'completedServices'])->name('listCompletedServices');
    });

    // ATTENDANT ROTES
    Route::middleware('role_check:admin,attendant')->group(function () {
        Route::post('/postTicket', [TicketController::class, 'create'])->name('createTicket');
        Route::put('/putTicket', [TicketController::class, 'update'])->name('editTicket');
        Route::post('/postService', [ServiceController::class, 'create'])->name('createService');
        Route::get('/getAvailableSupport', [AuthController::class, 'findAvailableSupport'])->name('listAvailableSupport');
    });

    // SUPPORT ROUTES
    Route::middleware('role_check:admin,support')->group(function () {
        Route::put('/putAssociateService', [ServiceController::class, 'associateService'])->name('associateService');
        Route::put('/putcompleteService', [ServiceController::class, 'completeService'])->name('completeService');
        Route::get('/getUnassociateServices', [ServiceController::class, 'unassociateServices'])->name('unassociateServices');
    });

    // USER ROUTES
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/getTickets', [TicketController::class, 'findAll'])->name('findTickets');
    Route::get('/getTicket', [TicketController::class, 'findById'])->name('findTicketById');
    Route::get('/getServices', [ServiceController::class, 'findAll'])->name('findServices');
    Route::get('/getService', [ServiceController::class, 'findById'])->name('findServiceById');
    Route::get('/getServiceSupport', [ServiceController::class, 'findBySupportId'])->name('findServiceBySupportId');
    Route::get('/getServiceTicket', [ServiceController::class, 'findByTicketId'])->name('findServiceByTicketId');
});
