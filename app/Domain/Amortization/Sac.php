<?php

namespace App\Domain\Amortization;

use App\Domain\Installment;
use App\Domain\Loan;

class Sac extends Amortization
{
    public function calculateValues(Loan $loan): Loan
    {
        $installments = $loan->getInstallments();
        $pmt = $this->calculatePMT($loan);

        $debitBalance = $loan->getValue();

        foreach ($installments as $installment) {
            $installment->setLoan($loan);

            $this->calculateMainValue($installment);
            $this->calculateInterestValue($installment, $debitBalance);
            $this->calculateTotalValue($installment, $pmt);

            $debitBalance = $this->calculateDebitBalance($installment, $debitBalance);
        }

        $loan->setInstallments($installments);

        return $loan;
    }

    public function calculateTotalValue(Installment $installment, float $pmt): void
    {
        $totalValue = round($installment->getMainValue() + $installment->getInterestValue(), 2);

        $installment->setTotalValue($totalValue);
    }

    public function calculateMainValue(Installment $installment): void
    {
        $mainValue = round($installment->getLoan()->getValue() / $installment->getLoan()->getPeriod(), 2);

        $installment->setMainValue($mainValue);
    }

    public function calculateDebitBalance(Installment $installment, float $previousDebitBalance): float
    {
        $newDebitBalance = round($previousDebitBalance - $installment->getMainValue(), 2);

        if ($newDebitBalance < 0) {
            $newDebitBalance = 0.0;
        }

        $installment->setDebitBalance($newDebitBalance);

        return $newDebitBalance;
    }
}
