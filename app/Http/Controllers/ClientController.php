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
    
            return response()->json(['error' => 'Errorr fetching user clients.'], 500);
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

    public function deleteClient($id)
    {
        $user = Auth::user();
        $client = $user->client()->find($id);
        
        if (!$client) {
            return response()->json(['error' => 'client not found.'], 404);
        }
    
        $client->delete();
    
        return response()->json('client deleted successfully.');
    }

    public function updateClient($id, Request $request)
    {
        $client= client::find($id);
        $client->update($request->all());
        return response()->json('client updated successfully');
    }
   
}
