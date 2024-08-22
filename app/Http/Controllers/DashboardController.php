<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTopupWalletRequest;
use App\Http\Requests\StoreWithdrawWalletRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function wallet(){

        $user= Auth::user();

        $wallet_transaction = WalletTransaction::where('user_id', $user->id)
        ->orderByDesc('id')
        ->paginate(10);

        return view('dashboard.wallet', compact('wallet_transaction'));
    }

    public function withdraw_wallet()
    {
        return view('dashboard.withdraw_wallet');
    }

    public function withdraw_wallet_store(StoreWithdrawWalletRequest $request){
        $user = Auth::user();

        if($user->wallet->balance < 100000){
            return redirect()->back()->withErrors('amount', 'Balance saat ini tidak cukup');
        }

        DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['type'] = 'Withdraw';
            $validated['amount'] = $user->wallet->balance;
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newWithdrawwallet = WalletTransaction::create($validated);

            $user->wallet->update([
                'balance' => 0
            ]);

        });

        return redirect()->route('dashboard.wallet')->with('success', 'Withdraw wallet created successfully');
    }

    public function topup_wallet()
    {
        return view('dashboard.topup_wallet');
    }

    public function topup_wallet_store(StoreTopupWalletRequest $request){
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {
            $validated = $request->validated();

            if($request->hasFile('proof')) {
                $proofPath = $request->file('proof')->store('proofs', 'public');
                $validated['proof'] = $proofPath;
            }

            $validated['type'] = 'Topup';
            $validated['is_paid'] = false;
            $validated['user_id'] = $user->id;

            $newTopupwallet = WalletTransaction::create($validated);

        });

        return redirect()->route('dashboard.wallet')->with('success', 'Topup wallet created successfully');
    }
}
