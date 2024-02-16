<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Log;
use App\Services\UserService;

class ReportController extends Controller
{
    protected UserService $userService;
    public function __construct(UserService $userService)
    {
      $this->userService = $userService;
    }
    public function submitReport(Request $request)
    {
        $user = $this->userService->getUser();

        $validatedData = $request->validate([
            'ID' => 'required',
            'title' => 'required',
            'authorID' => 'required',
            'result' => 'required',
            'content' => 'nullable',
        ]);
     
        Mail::to('report@ecogest.dev')->send(new \App\Mail\ReportMail($validatedData));

        return response()->json(['message' => 'Formulaire soumis avec succès']);
    }
}
