<?php

namespace App\Domain\Amortization;

use App\Domain\Installment;
use App\Domain\Loan;

class Price extends Amortization
{
    public function calculateValues(Loan $loan): Loan
    {
        $installments = $loan->getInstallments();
        $pmt = $this->calculatePMT($loan);

        $debitBalance = $loan->getValue();

        foreach ($installments as $installment) {
            $installment->setLoan($loan);

            $this->calculateTotalValue($installment, $pmt);
            $this->calculateInterestValue($installment, $debitBalance);
            $this->calculateMainValue($installment);

            $debitBalance = $this->setDebitBalance($installment, $debitBalance);
        }

        $loan->setInstallments($installments);

        return $loan;
    }

    private function calculateTotalValue(Installment $installment, float $pmt): void
    {
        $installment->setTotalValue($pmt);
    }

    private function calculateInterestValue(Installment $installment, float $debitBalance): void
    {
        $interestValue = round($debitBalance * $installment->getLoan()->getInterest(), 2);

        $installment->setInterestValue($interestValue);
    }

    private function calculateMainValue(Installment $installment): void
    {
        $mainValue = round($installment->getTotalValue() - $installment->getInterestValue(), 2);

        $installment->setMainValue($mainValue);
    }

    private function setDebitBalance(Installment $installment, float $previousDebitBalance): float
    {
        $newDebitBalance = round($previousDebitBalance - $installment->getMainValue(), 2);

        if ($newDebitBalance < 0) {
            $newDebitBalance = 0.0;
        }

        $installment->setDebitBalance($newDebitBalance);

        return $newDebitBalance;
    }

    private function calculatePMT(Loan $loan): float
    {
        $pv = $loan->getValue();
        $i = $loan->getInterest();
        $n = $loan->getPeriod();

        $pmt = $pv * (
            $i * pow(1 + $i, $n)
        ) / (
            pow(1 + $i, $n) - 1
        );

        return round($pmt, 2);
    }
}
