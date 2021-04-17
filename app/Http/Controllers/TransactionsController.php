<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Jobs\CalculateProfileBalanceJob;
use App\Models\User;

class TransactionsController extends Controller
{
    /** @var Transaction */
    private $transactions;

    /** @var User */
    private $users;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Transaction $transactions, User $users)
    {
        $this->transactions = $transactions;
        $this->users = $users;
    }

    /**
     * Сохраняет транзакцию в базе
     * Пересчитывает баланс пользователя
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'bail|required|exists:users,id',
            'value' => 'required|numeric',
            'description' => 'required|string',
            'type' => 'required',
            'created_at' => 'required',
        ]);
        $newTransaction = $this->transactions->create($request->all());
        $user = $newTransaction->user;
        dispatch(new CalculateProfileBalanceJob($user));
        return response()->json([
            'status' => 'ok'
        ], 202);
    }

    /**
     * Возвращает баланс профиля пользователя
     *
     * @param string $id
     * @return void
     */
    public function getBalance(string $id)
    {
        $user = $this->users->findOrFail($id);
        return response()->json([
            'status' => 'ok',
            'data' => [
                'balance' => $user->profile->balance
            ]
        ], 200);
    }
    
}
