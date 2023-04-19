<?php

namespace App\LeaderPackages;

use App\Models\Leader;
use App\Models\LeaderPackage;
use App\Models\LeaderPackageTransaction;
use App\Models\Rookie;
use App\Models\Transaction;

class SpendMicromorgi
{
    public $rookie;
    public $leader;
    public $transaction;
    public $leader_packages;

    public static function spend(Transaction $transaction): void
    {
        try {
            new SpendMicromorgi($transaction->rookie_id, $transaction->leader_id, $transaction);
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    public function __construct(int $rookie_id, int $leader_id, Transaction $transaction)
    {
        $this->rookie = Rookie::find($rookie_id);
        $this->leader = Leader::find($leader_id);
        $this->transaction = $transaction;
        $this->leader_packages = LeaderPackage::query()
            ->where('leader_id', $this->leader->id)
            ->where('is_refunded', false)
            ->get();

        try {
            $this->removeMicromorgi();
        }catch (\Exception $exception){
            throw new \Exception($exception->getMessage());
        }
    }

    private function removeMicromorgi(): void
    {
        if($this->removeMicromorgiFromFirstPackage()){
            return;
        }

        $this->removeMicromorgiFromPackages();
    }

    private function removeMicromorgiFromFirstPackage(): bool
    {
        $leader_package = $this->leader_packages->where('amount_available', '>=', $this->transaction->micromorgi)->first();
        if(!isset($leader_package)){
            return false;
        }

        $leader_package->spendMicromorgi($this->transaction->micromorgi);
        $this->createLeaderPackageTransaction($leader_package, $this->transaction->micromorgi);

        return true;
    }

    private function removeMicromorgiFromPackages(): bool
    {
        $leader_packages = $this->leader_packages->where('amount_available', '>', 0);
        if($leader_packages->sum('amount_available') < $this->transaction->micromorgi){
            return false;
        }

        $remaining_micromorgi = $this->transaction->micromorgi;

        foreach ($leader_packages as $leader_package){

            if($remaining_micromorgi<=0){
                break;
            }

            $amount_to_take = ($leader_package->amount_available >= $remaining_micromorgi)
                ? $remaining_micromorgi
                : $leader_package->amount_available;

            $remaining_micromorgi = $remaining_micromorgi - $leader_package->amount_available;

            $leader_package->spendMicromorgi($amount_to_take);
            $this->createLeaderPackageTransaction($leader_package, $amount_to_take);
        }

        return true;
    }

    private function createLeaderPackageTransaction(LeaderPackage $leader_package, int $micromorgi): LeaderPackageTransaction
    {
        return LeaderPackageTransaction::create([
            'leader_package_id' => $leader_package->id,
            'amount' => $micromorgi,
            'transaction_id' => $this->transaction->id
        ]);
    }
}
