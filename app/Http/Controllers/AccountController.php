<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Services\AccountService;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Endoint para criação de nova Conta
     */
    public function store(StoreAccountRequest $request)
    {
        $this->accountService->createAccount($request->account_type_id, $request->title, $request->description);
        return Redirect::route('nova.conta')->with('success', 'Sua nova conta foi criada com sucesso!');
    }

    /**
     * Endpoint para retornar a lista de contas do usuário logado.
     */
    public function getUserAccounts(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        $accounts = $this->accountService->getUserAccounts($user->id);

        $formattedAccounts = $accounts->map(function ($account) {
            return [
                'id' => $account->id,
                'account_type' => $account->accountType->name ?? null,
                'title' => $account->title,
                'balance' => $account->balance,
            ];
        });

        return response()->json($formattedAccounts);
    }
}
