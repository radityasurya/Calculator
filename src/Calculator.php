<?php
namespace App;

use App\Models\BillItem;
use App\BillCollection;

class Calculator{

    private BillCollection $bills;
    private bool $isOptimized;
    private array $payments;

    public function __construct(BillCollection $bills){
        $this->bills = $bills;
        $this->isOptimized = false;
        $this->payments = [];
    }

    /**
     * Set the $isOptimized of this object for optimized calculation
     * 
     * @param bool $isOptimized
     */
    public function setIsOptimized(bool $isOptimized): void
    {
        $this->isOptimized = $isOptimized;
    }
    
    /**
     * printBill will print the calculation results
     *
     * @return void
     */
    public function printBill(): void
    {
        $payout = $this->calculate();

        foreach($payout as $debtor => $lines){
            $debtor = ucfirst($debtor);

            foreach($lines as $creditor => $amount){

                $amount = number_format($amount, 2);
                $creditor = ucfirst($creditor);
                echo "$debtor pays $creditor $amount" . PHP_EOL;
            }
        }
    }
    
    /**
     * calculate will calculate the bills based on the debtors
     *
     * @return array
     */
    private function calculate(): array
    {
        $loans = [];

        $creditors = $this->bills->getCreditors();

        // Find total loans per creditors
        foreach ($creditors as $lender) {
            $loans[$lender] = $this->calculateLoans($lender, $this->bills->getBillItems());
        }

        // Calculate payments
        foreach ($creditors as $debtor) {
            foreach ($loans as $loanLender => $debt) {
                // Check if current lender is in the loans debt list
                if (array_key_exists($debtor, $debt)) {
                    $difference = $loans[$debtor][$loanLender] - $debt[$debtor];

                    if ($difference > 0) {
                        $this->payments[$loanLender][$debtor] = $difference;
                    } else {
                        $this->payments[$debtor][$loanLender] = $difference * -1;
                    }
                }
            }
        }

        if ($this->isOptimized) {
            $this->calculateOptimized($this->payments);
        }

        return $this->payments;
    }
    
    /**
     * calculateLoans will filter the creditor bills 
     * and sums the debt amount of debts owed to that creditor.
     *
     * @param  mixed $creditor The creditor who pays for the bills.
     * @param  mixed $bills The bills collections.
     * @return array
     */
    private function calculateLoans(string $creditor, array $bills): array
    {
        $payments = [];

        $creditorBills = array_filter($bills, function($bill) use ($creditor) {
            return $bill->getCreditor() === $creditor;
        });

        foreach ($creditorBills as $bill) {
            foreach ($bill->getDebtors() as $debtor => $debt) {
                if (isset($payments[$debtor])) {
                    $payments[$debtor] += $debt[$creditor];
                } else {
                    $payments[$debtor] = $debt[$creditor];
                }
            }
        }

        return $payments;
    }
    
    /**
     * calculateOptimized will find the common debtors
     * and pay the debt based on the found common debtors.
     *
     * @return void
     */
    private function calculateOptimized(): void
    {
        foreach ($this->payments as $currentDebtor => $currentCreditors) {
            foreach ($currentCreditors as $creditor => $amount) {
                // Find common debtors
                $commonDebtors = $this->findCommonDebtors($currentDebtor, $creditor);

                if (count($commonDebtors) > 0) {
                    $this->payCommonDebt($currentDebtor, $creditor, $commonDebtors);
                }
            }
        }
    }
    
    /**
     * findCommonDebtors will return an array of the debtors 
     * of the current creditor and current debtor.
     *
     * @param  mixed $currentDebtor the current debtor.
     * @param  mixed $creditor the current creditor.
     * @return array
     */
    private function findCommonDebtors(string $currentDebtor, string $creditor): array
    {
        $currentCreditorDebtors = $this->payments[$creditor];
        $currentDebtorDebtors = $this->payments[$currentDebtor];

        // Check if the current creditor or current debtor debtors list not empty
        if (empty($currentCreditorDebtors) || (empty($currentDebtorDebtors))) {
            return [];
        }

        return array_intersect_key($currentDebtorDebtors, $currentCreditorDebtors);
    }
    
    /**
     * payCommonDebt will calculate the debt based on the creditor from the common debtors,
     * It will compare the amount of the current debtor debt to the current creditor
     * And remove the payments if it's done with the calculation.
     *
     * @param  mixed $debtor
     * @param  mixed $currentCreditor
     * @param  mixed $commonDebtors
     * @return void
     */
    private function payCommonDebt(string $debtor, string $currentCreditor, array $commonDebtors): void
    {
        foreach ($commonDebtors as $creditor => $debt) {
            $currentDebtAmount = $this->payments[$debtor][$currentCreditor];

            if ($currentDebtAmount > 0) {
                $currentCreditorDebt = $this->payments[$currentCreditor][$creditor];
                $currentDebtorDebt = $this->payments[$debtor][$creditor];

                if ($currentDebtAmount > $currentCreditorDebt){
                    $this->payments[$debtor][$creditor] += $currentCreditorDebt;
                    $this->payments[$debtor][$currentCreditor] = $currentDebtAmount - $currentCreditorDebt;
                    // Remove creditor debt in current creditor debt list
                    unset($this->payments[$currentCreditor][$creditor]);
                } else {
                    $this->payments[$debtor][$creditor] = $currentDebtorDebt + $currentDebtAmount;
                    $this->payments[$currentCreditor][$creditor] -= $currentDebtAmount;
                    // Remove current creditor debt in the debtor debt list
                    unset($this->payments[$debtor][$currentCreditor]);
                }
            }
        }
    }
}
