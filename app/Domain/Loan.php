<?php

namespace App\Domain;

use App\Domain\Amortization\Amortization;
use App\Domain\Amortization\AmortizationInterface;
use DateTime;
use Exception;

class Loan
{
    private const MIN_LOAN_VALUE = 300;
    private const MIN_LOAN_PERIOD = 1;
    
    private const MAX_LOAN_PERIOD = 60;

    private const MIN_INTEREST_FEE = 0;
    private const MAX_INTEREST_FEE = 5;

    private const PAYMENT_DAY_10 = '10';
    private const PAYMENT_DAY_5 = '5';

    private const ALLOWED_PAYMENT_DAYS = [
        self::PAYMENT_DAY_5,
        self::PAYMENT_DAY_10,
    ];

    private int $period;
    private float $value;
    private float $interest;
    private string $paymentDay;
    private DateTime $firstDueDate;
    private array $installments;

    private AmortizationInterface $amortization;

    public function getAmortization(): AmortizationInterface
    {
        return $this->amortization;
    }

    public function setAmortization(AmortizationInterface $amortization): self
    {
        $this->amortization = $amortization;
        return $this;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        if ($value <= self::MIN_LOAN_VALUE) {
            throw new \InvalidArgumentException(
                "O valor do empréstimo 'R$ " . number_format($value, 2, ',', '.') . "' deve ser maior que R$ 300,00."
            );
        }

        $this->value = $value;
        return $this;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function setPeriod(int $period): self
    {
        if ($period < self::MIN_LOAN_PERIOD || $period > self::MAX_LOAN_PERIOD) {
            throw new \InvalidArgumentException(
                'O prazo deve estar entre ' . self::MIN_LOAN_PERIOD . ' e ' . self::MAX_LOAN_PERIOD . ' meses.'
            );
        }

        $this->period = $period;
        return $this;
    }

    public function getInterest(): float
    {
        return $this->interest;
    }

    public function setInterest(float $interest): self
    {
        if ($interest < self::MIN_INTEREST_FEE || $interest > self::MAX_INTEREST_FEE) {
            throw new \InvalidArgumentException(
                'A taxa de juros deve estar entre ' . self::MIN_INTEREST_FEE . '% e ' . self::MAX_INTEREST_FEE . '% ao mês.'
            );
        }

        $this->interest = $interest / 100;

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

    public function getPaymentDay(): string
    {
        return $this->paymentDay;
    }

    public function setPaymentDay(string $paymentDay): self
    {
        if (!in_array($paymentDay, self::ALLOWED_PAYMENT_DAYS)) {
            throw new \InvalidArgumentException(
                "O dia de pagamento '$paymentDay' não é válido."
            );
        }

        $this->paymentDay = $paymentDay;

        return $this;
    }

    public function getFirstDueDate(): \DateTime
    {
        return $this->firstDueDate;
    }

    public function setFirstDueDate(\DateTime $firstDueDate): self
    {
        $this->firstDueDate = $firstDueDate;
        return $this;
    }

    public function simulate(): self
    {
        $this->generateInstallments();

        $this->getAmortization()->calculateValues($this);

        return $this;
    }

    private function generateInstallments(): Loan
    {
        $installments = [];

        $this->defineFirstDueDate();

        for ($i = 0; $i < $this->getPeriod(); $i++) {
            $installment = (new Installment())
                ->setNumber($i + 1)
                ->setLoan($this)
                ->calculateDueDate()
            ;

            $installments[] = $installment;
        }

        $this->setInstallments($installments);

        return $this;
    }

    private function defineFirstDueDate()
    {
        $today = new \DateTime('now');

        $currentMonthDueDate = (clone $today)->setDate(
            (int)$today->format('Y'),
            (int)$today->format('m'),
            (int)$this->getPaymentDay()
        );

        $firstDueDate = $currentMonthDueDate;

        if ($currentMonthDueDate < $today) {
            $firstDueDate = (clone $currentMonthDueDate)->modify('+1 month');
        }

        $this->setFirstDueDate($firstDueDate);
    }




    // public static function calculateNextDueDate(Loan $loan, int $monthIncrement): \DateTime
    // {
    //     $firstDueDate = $loan->getFirstDueDate();
    //     $paymentDay = $loan->makeRules()->getPaymentDay();

    //     $dueDate = new \DateTime($firstDueDate);
    //     $dueDate->modify('first day of');
    //     $dueDate->modify("+$monthIncrement month");

    //     return $loan->adjustDueDateToPayment($dueDate, $paymentDay);
    // }
}
