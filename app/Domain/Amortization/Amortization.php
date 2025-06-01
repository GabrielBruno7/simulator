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
    abstract function calculatePMT(Loan $loan): float;
    abstract function calculateMainValue(Installment $installment): void;
    abstract function calculateTotalValue(Installment $installment, float $pmt): void;
    abstract function calculateInterestValue(Installment $installment, float $debitBalance): void;
    abstract function setDebitBalance(Installment $installment, float $previousDebitBalance): float;
}
