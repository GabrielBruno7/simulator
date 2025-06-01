<?php

namespace App\Domain\Amortization;

use App\Domain\Installment;
use App\Domain\Loan;

abstract class Amortization implements AmortizationInterface
{
    public const AMORTIZATION_SAC = 'sac';
    public const AMORTIZATION_PRICE = 'price';

    public const ALLOWED_AMORTIZATION_TYPES = [
        self::AMORTIZATION_SAC,
        self::AMORTIZATION_PRICE
    ];

    abstract function calculateValues(Loan $loan): Loan;

    abstract function calculateMainValue(Installment $installment): void;
    abstract function calculateTotalValue(Installment $installment, float $pmt): void;
    abstract function calculateDebitBalance(Installment $installment, float $previousDebitBalance): float;



    public function calculateInterestValue(Installment $installment, float $debitBalance): void
    {
        $interestValue = round($debitBalance * $installment->getLoan()->getInterest(), 2);

        $installment->setInterestValue($interestValue);
    }

    public function calculatePMT(Loan $loan): float
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
