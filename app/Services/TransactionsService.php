<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class TransactionsService
{
    /**
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function deposit($accountId, $amount): Transaction
    {
        try {
            DB::beginTransaction();

            $account = Account::lockForUpdate()->findOrFail($accountId);

            if ($account->user_id !== auth()->id()) {
                throw new \Exception('Você não tem permissão para depositar nesta conta.', 403);
            }

            $transaction = $this->newTransaction($accountId, 'deposito', $amount, 'Depósito manual');

            if (in_array($account->accountType->id, [2, 3])) {
                $transaction = $this->newTransaction($accountId, 'deposito', $account->accountType->increment, 'Acréscimo automático por tipo de conta');
                $amount += $account->accountType->increment;
            }

            $account->balance += $amount;
            $account->save();

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function withdraw($accountId, $amount): Transaction
    {
        try {
            DB::beginTransaction();

            $account = Account::lockForUpdate()->findOrFail($accountId);

            if ($account->user_id !== auth()->id()) {
                throw new \Exception('Você não tem permissão para retirar desta conta.', 403);
            }

            if ($account->balance < $amount) {
                throw new \Exception('Saldo insuficiente para realizar a retirada.', 400);
            }

            $account->balance -= $amount;
            $account->save();
            $transaction = $this->newTransaction($accountId, 'saque', $amount, 'Saque manual');

            DB::commit();

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return \App\Models\Transaction
     * @throws \Exception
     */
    public function newTransaction($accountId, $type, $amount, $description): Transaction
    {
        try {
            DB::beginTransaction();
            $transaction = Transaction::create([
                'account_id' => $accountId,
                'type' => $type,
                'amount' => $amount,
                'description' => $description
            ]);
            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Retorna todas as transações dos últimos 7 dias para o usuário logado,
     * ordenadas do mais recente para o mais antigo.
     */
    public function getLastWeekTransactions(int $userId): Collection
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);
        $accountIds = Account::where('user_id', $userId)->pluck('id');

        return Transaction::whereIn('account_id', $accountIds)
                        ->where('created_at', '>=', $sevenDaysAgo)
                        ->orderByDesc('created_at')
                        ->get(['id', 'account_id', 'description', 'created_at', 'amount']);
    }
}
