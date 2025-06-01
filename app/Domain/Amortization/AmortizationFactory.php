<?php

namespace App\Domain\Amortization;

class AmortizationFactory
{
    public static function make(string $type): AmortizationInterface
    {
        if (!in_array($type, Amortization::ALLOWED_AMORTIZATION_TYPES)) {
            throw new \InvalidArgumentException(
                "O tipo de amortização '$type' é inválido."
            );
        }

        return match(strtolower($type)) {
            Amortization::AMORTIZATION_SAC => new Sac(),
            Amortization::AMORTIZATION_PRICE => new Price(),
        };
    }
}
