<?php

namespace App\Domain\Amortization;

use App\Domain\Loan;

interface AmortizationInterface
{
    public function calculateValues(Loan $loan): Loan;
}
