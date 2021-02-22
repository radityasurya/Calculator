<?php
namespace App;

use App\Models\BillItem;
use App\BillCollection;

class Calculator{

    private BillCollection $bills;
    private array $payments;

    public function __construct(BillCollection $bills){
        $this->bills = $bills;
        $this->payments = [];
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
    
}