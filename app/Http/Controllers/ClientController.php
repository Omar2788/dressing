<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $userId = $user->id;
    
            $clients = Client::where('user_id', $userId)->get();
    
            return response()->json($clients, 200);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
    
            return response()->json(['error' => 'Errorr fetching user meals.'], 500);
        }
       
    }
    public function addClient(Request $request)
    {
        $user = Auth::user();
    
        $client = new Client([
            'nom' => $request->input('nom'),
            'prenom' => $request->input('prenom'),
            'description' => $request->input('description'),
            'num' => $request->input('num'),
            'image' => $request->input('image'),
            'user_id' => $user->id, 
        ]);
    
       
        $client->save();
    
        return response()->json($client, 200);
    }
   
}
