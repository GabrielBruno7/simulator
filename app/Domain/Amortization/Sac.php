<?php

namespace App\Domain\Amortization;

use App\Domain\Loan;

class Sac extends Amortization
{
    public function calculateValues(Loan $loan): Loan
    {
        return $loan;
    }
}
