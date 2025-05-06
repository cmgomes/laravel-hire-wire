<?php

namespace App\Http\Controllers;

use App\Models\AccountType;
use Illuminate\Http\JsonResponse;

class AccountTypeController extends Controller
{
    /**
     * Endpint para retornar os tipos de conta
     */
    public function index(): JsonResponse
    {
        $accountTypes = AccountType::all();
        return response()->json($accountTypes);
    }
}