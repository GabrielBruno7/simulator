<?php

namespace Tests;

class LoanTest extends TestCase
{
    private const SIMULATE_ROUTE = '/simulate';

    public function testShouldCorrectlySimulate(): void
    {
        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => "sac",
            "dia_pagamento" => 10,
            "valor"         => 10000,
            "prazo"         => 12,
            "juros"         => 3
        ]);

        $this->assertEquals(200, $response->status());
    }

    public function testShouldThrowExceptionWhenAmortizationTypeIsInvalid(): void
    {
        $invalidAmortizationType = 'of the king the power';

        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => $invalidAmortizationType,
            "dia_pagamento" => 10,
            "valor"         => 10000,
            "prazo"         => 12,
            "juros"         => 3
        ]);

        $this->assertEquals(400, $response->status());

        $responseMessage = json_decode($response->getContent(), true);

        $this->assertEquals(
            "O tipo de amortização '$invalidAmortizationType' é inválido.",
            $responseMessage['Erro']
        );
    }

    public function testShouldThrowExceptionWhenPaymentDayIsInvalid(): void
    {
        $invalidPaymentDay = 1;

        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => "sac",
            "dia_pagamento" => $invalidPaymentDay,
            "valor"         => 10000,
            "prazo"         => 12,
            "juros"         => 3
        ]);

        $this->assertEquals(400, $response->status());

        $responseMessage = json_decode($response->getContent(), true);

        $this->assertEquals(
            "O dia de pagamento '$invalidPaymentDay' não é válido.",
            $responseMessage['Erro']
        );
    }

    public function testShouldThrowExceptionWhenLoanValueIsInvalid(): void
    {
        $invalidLoanValue = 200;

        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => "sac",
            "dia_pagamento" => 10,
            "valor"         => $invalidLoanValue,
            "prazo"         => 12,
            "juros"         => 3
        ]);

        $this->assertEquals(400, $response->status());

        $responseMessage = json_decode($response->getContent(), true);

        $this->assertEquals(
            "O valor do empréstimo 'R$ $invalidLoanValue,00' deve ser maior que R$ 300,00.",
            $responseMessage['Erro']
        );
    }

    public function testShouldThrowExceptionWhenPeriodIsInvalid(): void
    {
        $invalidPeriod = 70;

        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => "sac",
            "dia_pagamento" => 10,
            "valor"         => 2000,
            "prazo"         => $invalidPeriod,
            "juros"         => 3
        ]);

        $this->assertEquals(400, $response->status());

        $responseMessage = json_decode($response->getContent(), true);

        $this->assertEquals(
            "O prazo deve estar entre 1 e 60 meses.",
            $responseMessage['Erro']
        );
    }

    public function testShouldThrowExceptionWhenInterestFeeIsInvalid(): void
    {
        $invalidInterestFee = -3;

        $response = $this->postJson(self::SIMULATE_ROUTE, [
            "amortizacao"   => "sac",
            "dia_pagamento" => 10,
            "valor"         => 2000,
            "prazo"         => 13,
            "juros"         => $invalidInterestFee
        ]);

        $this->assertEquals(400, $response->status());

        $responseMessage = json_decode($response->getContent(), true);

        $this->assertEquals(
            "A taxa de juros deve estar entre 0% e 5% ao mês.",
            $responseMessage['Erro']
        );
    }
}
