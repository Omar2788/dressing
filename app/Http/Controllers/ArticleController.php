<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class ArticleController extends Controller
{
    
    public function store(Request $request)
    {
        
        $user = Auth::user();
    
        $article = new Article([
            'nom' => $request->input('nom'),
            'description' => $request->input('description'),
            'proprietaire' => $request->input('proprietaire'),
            'status' => $request->input('status'),
            'image' => $request->input('image'),
            'prix' => $request->input('prix'),
            'user_id' => $user->id, 
        ]);
    
       
        $article->save();
    
        return response()->json($article, 200);
    }
    
    public function userArticles()
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
    
            $articles = Article::where('user_id', $userId)->get();
    
            return response()->json($articles, 200);
        } catch (\Exception $e) {
    
            return response()->json(['error' => 'Errorr fetching user articles.'], 500);
        }
    }
    
   
    public function updateArticle($id, Request $request)
    {
        $article= Article::find($id);
        $article->update($request->all());
        return response()->json('article updated successfully');
    }
    


    public function deleteArticle($id)
    {
        $user = Auth::user();
        $article = $user->article()->find($id);
        
        if (!$article) {
            return response()->json(['error' => 'article not found.'], 404);
        }

        $article->delete();

        return response()->json('article deleted successfully.');
    }

    public function displayChart(Request $request)
    {
        try {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

            $soldArticlesCount = Article::where('status', 'vendu')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $availableArticlesCount = Article::where('status', 'disponible')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $chartData = [
                'vendu' => $soldArticlesCount,
                'disponible' => $availableArticlesCount,
            ];

            return response()->json($chartData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching chart data.'], 500);
        }
    }

    public function articlesVendu(Request $request)
    {
        try {
            $startDate = $request->input('startDate');
            $endDate = $request->input('endDate');

             //send just the prix of the articles vendu
            $articlesVendu = Article::where('status', 'vendu')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get(['prix']);

            return response()->json($articlesVendu, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching articles vendu.'], 500);
        }
    }

    public function displayPie(Request $request)
    {
        try {
            $soldArticlesCount = Article::where('status', 'vendu')
                ->count();

            $availableArticlesCount = Article::where('status', 'disponible')
                ->count();

            $pieData = [
                'vendu' => $soldArticlesCount,
                'disponible' => $availableArticlesCount,
            ];

            return response()->json($pieData, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching chart data.'], 500);
        }
    }

}
