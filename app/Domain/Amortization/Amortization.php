<?php

namespace App\Domain\Amortization;

use App\Domain\Loan;

class Amortization implements AmortizationInterface
{
    private string $type;

    private const AMORTIZATION_SAC = 'sac';
    private const AMORTIZATION_PRICE = 'price';

    private const ALLOWED_AMORTIZATION_TYPES = [
        self::AMORTIZATION_SAC,
        self::AMORTIZATION_PRICE
    ];

    public function setType(string $type): self
    {
        if (!in_array($type, self::ALLOWED_AMORTIZATION_TYPES)) {
            throw new \InvalidArgumentException(
                "O tipo de amortização '$type' é inválido."
            );
        }

        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function make(): AmortizationInterface
    {
        return match($this->getType()) {
            self::AMORTIZATION_SAC => new Sac(),
            self::AMORTIZATION_PRICE => new Price(),
        };
    }

    public function calculateValues(Loan $loan): Loan
    {
        return $loan;
    }
}
