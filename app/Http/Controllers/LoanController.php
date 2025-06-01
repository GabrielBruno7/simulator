<?php

namespace App\Http\Controllers;

use App\Domain\Amortization\AmortizationFactory;
use App\Domain\Installment;
use Illuminate\Http\Request;
use App\Domain\Loan;

class LoanController extends Controller
{
    public function actionSimulate(Request $request)
    {
        try {
            $request = $this->validate($request, [
                'amortizacao'   => 'required',
                'valor'         => 'required',
                'juros'         => 'required',
                'prazo'         => 'required',
                'dia_pagamento' => 'required',
            ]);

            $amortization = AmortizationFactory::make($request['amortizacao']);

            $loan = (new Loan())
                ->setAmortization($amortization)
                ->setValue($request['valor'])
                ->setPeriod($request['prazo'])
                ->setInterest($request['juros'])
                ->setPaymentDay($request['dia_pagamento'])
                ->simulate()
            ;

            $installments = (new Installment())
                ->setInstallments($loan->getInstallments())
                ->buildInstallmentsResponse()
            ;

            return response()->json([
                'simulacao' => [
                    'valor' => $loan->getValue(),
                    'prazo' => $loan->getPeriod(),
                    'juros' => $loan->getInterest(),
                    'parcelas' => $installments
                ]
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['Erro' => $e->getMessage()], 400);
        }
    }
}
