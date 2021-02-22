<?php

namespace App;

use App\Models\BillItem;

class BillCollection
{
    private array $bills = [];
    
    /**
     * __construct
     *
     * @param  mixed $bills
     * @return void
     */
    public function __construct(array $bills)
    {
        foreach($bills as $bill){
            $this->addBill($bill);
        }
    }
    
    /**
     * addBill
     *
     * @param  mixed $bill
     * @param  mixed $key
     * @return void
     */
    public function addBill(string $bill, $key = null): void
    {
        if ($key === null) {
            $this->bills[] = new BillItem($bill);
        } else {
            $this->bills[$key] = new BillItem($bill);
        }
    }
    
    /**
     * getBillItems
     *
     * @return array
     */
    public function getBillItems(): array
    {
        return $this->bills;
    }
    
    /**
     * getCreditors
     *
     * @return array
     */
    public function getCreditors(): array
    {
        $lenders = [];

        foreach ($this->bills as $bill) {
            if (!in_array($bill->getCreditor(), $lenders)) {
                $lenders[] = $bill->getCreditor();
            }
        }

        return $lenders;
    }
}