<?php

namespace App\Domain\Amortization;

use App\Domain\Installment;
use App\Domain\Loan;

interface AmortizationInterface
{
    public function calculatePMT(Loan $loan): float;

    public function calculateValues(Loan $loan): Loan;

    public function calculateMainValue(Installment $installment): void;

    public function calculateTotalValue(Installment $installment, float $pmt): void;

    public function calculateInterestValue(Installment $installment, float $debitBalance): void;

    public function calculateDebitBalance(Installment $installment, float $previousDebitBalance): float;
}
