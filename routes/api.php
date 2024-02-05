<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ClientController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/client', [ClientController::class, 'addClient']);
Route::get('/client', [ClientController::class,'index']);
Route::post('/article', [MealController::class, 'store']);

Route::get('/api/userMeal', [MealController::class, 'usersMeal']);
Route::delete('/article/{id}', [MealController::class,'deleteArticle']);
Route::put('/meal/{id}', [MealController::class,'updateMeal']);
Route::get('/meal/{id}', [MealController::class,'showMeal']);
Route::post('/comments', [CommentController::class, 'addComment']);
Route::get('/meal', [MealController::class,'usersMeal']);
Route::get('/article', [MealController::class,'usersMeal']);

Route::get('/comments/{mealId}', [CommentController::class, 'getComments']);
Route::resource('meal', MealController::class);
Route::middleware('auth:sanctum')->get('/article', [MealController::class,'usersMeal']);
Route::middleware('auth:sanctum')->post('/client', [ClientController::class, 'addClient']);
Route::middleware('auth:sanctum')->get('/client', [ClientController::class,'index']);
Route::middleware('auth:sanctum')->post('/article', [MealController::class, 'store']);
Route::middleware('auth:sanctum')->get('/meal', [MealController::class, 'usersMeal']);
Route::middleware('auth:sanctum')->post('/favoritemeals', [MealController::class, 'addToFavorite']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class,'logout']);
Route::middleware('auth:sanctum')->get('/favoritemeals', [MealController::class, 'userFavoriteMeal']);
Route::middleware('auth:sanctum')->put('/meal/{id}', [MealController::class, 'updateMeal']);
Route::middleware('auth:sanctum')->delete('/article/{id}', [MealController::class, 'deleteArticle']);
Route::middleware('auth:sanctum')->get('/meal/{id}', [MealController::class, 'showMeal']);
Route::middleware('auth:sanctum')->post('/comments', [CommentController::class, 'addComment']);
Route::middleware('auth:sanctum')->get('/comments/{mealId}', [CommentController::class, 'getComments']);
Route::middleware('auth:sanctum')->get('/user/{id}', [CommentController::class, 'getnameuser']);
