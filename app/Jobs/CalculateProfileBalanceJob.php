<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class CalculateProfileBalanceJob extends Job
{
    /** @var User */
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Пересчёт баланса в профиле пользователя.
     *
     * @return void
     */
    public function handle()
    {
        $transactions = $this->user->transactions;
        $profile = $this->user->profile;
        $profile->balance = $this->calculateBalance($transactions);
        $profile->save();
    }

    /**
     * Вычисляет текущий баланс
     *
     * @param Collection $transactions
     * @return int
     */
    private function calculateBalance(Collection $transactions)
    {
        $newBalance = 0;
        foreach ($transactions as $transaction) {

            switch ($transaction->type) {
                case Transaction::TYPE_REFILL:
                    $newBalance += $transaction->value;
                    break;
                
                case Transaction::TYPE_DEBIT:
                    $newBalance -= $transaction->value;
                    break;

                case Transaction::TYPE_REFUND:
                    $newBalance -= $transaction->value;
                    break;   
            }
        }
        return $newBalance;
    }
}