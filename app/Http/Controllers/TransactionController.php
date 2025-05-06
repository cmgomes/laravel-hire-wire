<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use App\Services\TransactionsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transactionsService;

    public function __construct(TransactionsService $transactionsService)
    {
        $this->transactionsService = $transactionsService;
    }

    /**
     * Endpoint para realização de depósitos
     */
    public function deposit(TransactionRequest $request)
    {
        $accountId = $request->input('account_id');
        $amount = $request->input('amount');

        try {
            $this->transactionsService->deposit($accountId, $amount);
            return Redirect::route('deposit')->with('success', 'Depósito realizado com sucesso!');
        } catch(Exception $e) {
            return Redirect::back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Endpoint para efetuar saques nas contas
     */
    public function withdraw(TransactionRequest $request)
    {
        $accountId = $request->input('account_id');
        $amount = $request->input('amount');

        try {
            $this->transactionsService->withdraw($accountId, $amount);
            return Redirect::route('deposit')->with('success', 'Saque realizado com sucesso!');
        } catch(Exception $e) {
            return Redirect::back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    /**
     * Retorna a lista de transações dos últimos 7 dias do usuário logado.
     */
    public function getLastWeekTransactions(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        $transactions = $this->transactionsService->getLastWeekTransactions($user->id);

        return response()->json($transactions);
    }
}
