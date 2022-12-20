<?php

use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\EventController;

Route::get('/', [EventController::class, 'index']);

//->criar um novo registro
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth'); //só é acessada por usuarios logados

//show->para ver um dado do banco
Route::get('/events/{id}', [EventController::class, 'show']); 

//store ->enviar dados ao banco 
Route::post('/events', [EventController::class, 'store']); 

//destroy ->deletar dados ao banco 
Route::delete('/events/{id}', [EventController::class, 'destroy'])->middleware('auth'); 

//edit->para editar um dado do banco
Route::get('/events/edit/{id}', [EventController::class, 'edit'])->middleware('auth'); 

//edit->para fazer o update em si
Route::put('/events/update/{id}', [EventController::class, 'update'])->middleware('auth'); 

Route::get('/contact', function () {
    return view('contact');
});

//rota do dashboard
Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');

//rota da action para ligar usuario ao evento
Route::post('/events/join/{id}', [EventController::class, 'joinEvent'])->middleware('auth');

//rota da action para deletar participante ao evento
Route::delete('/events/leave/{id}', [EventController::class, 'leaveEvent'])->middleware('auth');
