<?php

namespace App\Domain;

use DateTime;

class Installment
{
    private Loan $loan;
    private int $number;
    private float $mainValue;
    private DateTime $dueDate;
    private float $totalValue;
    private array $installments;
    private float $debitBalance;
    private float $interestValue;


    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getMainValue(): float
    {
        return $this->mainValue;
    }

    public function setMainValue(float $mainValue): self
    {
        $this->mainValue = $mainValue;
        return $this;
    }

    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(DateTime $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getTotalValue(): float
    {
        return $this->totalValue;
    }

    public function setTotalValue(float $totalValue): self
    {
        $this->totalValue = $totalValue;
        return $this;
    }

    public function getDebitBalance(): float
    {
        return $this->debitBalance;
    }

    public function setDebitBalance(float $debitBalance): self
    {
        $this->debitBalance = $debitBalance;
        return $this;
    }

    public function getInterestValue(): float
    {
        return $this->interestValue;
    }

    public function setInterestValue(float $interestValue): self
    {
        $this->interestValue = $interestValue;
        return $this;
    }

    public function getInstallments(): array
    {
        return $this->installments;
    }

    public function setInstallments(array $installments): self
    {
        $this->installments = $installments;
        return $this;
    }

    public function getLoan(): Loan
    {
        return $this->loan;
    }

    public function setLoan(Loan $loan): self
    {
        $this->loan = $loan;
        return $this;
    }

    public function buildInstallmentsResponse(): array
    {
        $installments = [];

        foreach ($this->getInstallments() as $installment) {
            $installments[] = [
                'numero' => $installment->getNumber(),
                // 'valor_total' => $installment->getTotalValue(),
                // 'valor_principal' => $installment->getMainValue(),
                // 'valor_juros' => $installment->getInterestValue(),
                // 'saldo_devedor' => $installment->getDebitBalance(),
                'data_vencimento' => $installment->getDueDate()->format('d/m/Y'),
            ];
        }

        return $installments;
    }

    public function calculateDueDate(): self
    {
        $installmentNumber = $this->getNumber();

        $dueDate = (clone $this->getLoan()->getFirstDueDate())->modify("+".($installmentNumber - 1)." months");

        $year = (int)$dueDate->format('Y');
        $month = (int)$dueDate->format('m');

        $dueDate->setDate($year, $month, (int)$this->getLoan()->getPaymentDay());

        if ($dueDate->format('m') != sprintf('%02d', $month)) {
            $dueDate->modify('last day of previous month');
        }

        $this->setDueDate($dueDate);

        return $this;
    }

    public function calculateValues() {
        
    }
}
