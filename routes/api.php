<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ClientController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/client', [ClientController::class, 'addClient']);
Route::get('/client', [ClientController::class,'index']);
Route::post('/article', [ArticleController::class, 'store']);
Route::get('/api/userMeal', [ArticleController::class, 'usersMeal']);
Route::delete('/article/{id}', [ArticleController::class,'deleteArticle']);
Route::put('/article/{id}', [ArticleController::class,'updateArticle']);
Route::put('/client/{id}', [ClientController::class,'updateClient']);

Route::get('/article', [ArticleController::class,'userArticles']);
Route::delete('/client/{id}', [ClientController::class,'deleteClient']);
Route::get('articles', [ArticleController::class, 'displayChart']);
Route::middleware('auth:sanctum')->get('/article', [ArticleController::class,'userArticles']);
Route::middleware('auth:sanctum')->post('/client', [ClientController::class, 'addClient']);
Route::middleware('auth:sanctum')->get('/client', [ClientController::class,'index']);
Route::middleware('auth:sanctum')->post('/article', [ArticleController::class, 'store']);
Route::middleware('auth:sanctum')->post('/favoritemeals', [ArticleController::class, 'addToFavorite']);
Route::middleware('auth:sanctum')->post('/logout', [LoginController::class,'logout']);
Route::middleware('auth:sanctum')->put('/article/{id}', [ArticleController::class, 'updateArticle']);
Route::middleware('auth:sanctum')->put('/client/{id}', [ClientController::class, 'updateClient']);
Route::middleware('auth:sanctum')->delete('/article/{id}', [ArticleController::class, 'deleteArticle']);
Route::middleware('auth:sanctum')->delete('/client/{id}', [ClientController::class, 'deleteClient']);

