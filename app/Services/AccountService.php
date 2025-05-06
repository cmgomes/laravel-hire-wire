<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountService
{
    /**
     * Cria uma nova conta
     */
    public function createAccount($accountType, $title, $description)
    {
        Account::create([
            'user_id' => auth()->id(),
            'account_type_id' => $accountType,
            'title' => $title,
            'description' => $description
        ]);
    }

    /**
     * Retorna todas as contas
     */
    public function getAccounts(): Collection
    {
        return Account::all();
    }

    /**
     * Retorna todas as contas de um usuário específico.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserAccounts(int $userId): Collection
    {
        return Account::where('user_id', $userId)
            ->with('accountType:id,name')
            ->get(['id', 'account_type_id', 'title', 'balance']);
    }

    /**
     * Aplica a correção monetária mensal nas contas (Método a ser chamado pelo Scheduler)
     */
    public function applyMonthlyAdjustment(): void
    {
        echo "Iniciando Aplicação de correção mensal \n";
        $accounts = $this->getAccounts();
        $now = Carbon::now();
        $previousMonth = $now->copy()->subMonth()->startOfMonth();

        foreach ($accounts as $account) {
            echo "Corrigindo conta: [{$account->id}] do cliente [{$account->user->name}], Saldo: [{$account->balance}], Última Correção: [{$account->last_correction_at}]\n";
            $lastCorrectionAt = $account->last_correction_at ? Carbon::parse($account->last_correction_at) : null;
            $shouldApplyCorrection = false;

            if (!$lastCorrectionAt) {
                $shouldApplyCorrection = true;
            } elseif ($lastCorrectionAt->isBefore($previousMonth->endOfMonth())) {
                $shouldApplyCorrection = true;
            }

            if ($shouldApplyCorrection) {
                $this->applyCorrectionToAccount($account);
            } else {
                echo "A conta {$account->id} já foi corrigida esse mês";
            }
        }
    }

    /**
     * Aplica a correção monetária a uma conta específica.
     */
    protected function applyCorrectionToAccount(Account $account): void
    {
        $accountType = $account->accountType;
        $correctionRate = $accountType->correction_rate;
        echo "Taxa de Correção de contas [{$accountType->name}] é: {$correctionRate}\n";

        if ($correctionRate > 0) {
            $adjustmentAmountCents = round($account->balance * $correctionRate);
            echo "Valor da Correção a ser aplicada em centavos: {$adjustmentAmountCents}\n";

            //apenas correções maiores do que 1 centavo serão aplicadas
            if ($adjustmentAmountCents >= 1) {
                try {
                    echo "Aplicando a correção\n";
                    DB::beginTransaction();

                    $account->balance += $adjustmentAmountCents;
                    $account->last_correction_at = Carbon::now();
                    $account->save();

                    $account->transactions()->create([
                        'type' => 'correção',
                        'amount' => $adjustmentAmountCents,
                        'description' => 'Correção monetária mensal',
                    ]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo "Erro ao aplicar correção monetária na conta {$account->id}: {$e->getMessage()}\n";
                }
            } else {
                echo "deu ruim?";
            }
        }
    }
}
