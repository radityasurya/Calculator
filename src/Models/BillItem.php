<?php
namespace App\Models;

class BillItem{

    const DATA_STRUCT_COUNT = 3;

    private float $price;
    private string $creditor;
    private array $debtors = [];

    public function __construct($row){

        $data = explode(' ', $row);

        if ($this->isValid($data))
        {
            $this->setPrice((float) $data[0]);
            $this->setCreditor(strtolower($data[1]));
            $this->setDebtors($data[2]);
        } else {
            echo "Data is invalid";
            exit;
        }
    }

    /**
     * Set price of the BillItem.
     * 
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    /**
     * Set creditor of the BillItem.
     * 
     * @param string $creditor
     */
    public function setCreditor(string $creditor): void
    {
        $this->creditor = $creditor;
    }

    /**
     * Set the debtors and calculate the amount they owe.
     * 
     * @param string $attendees The list of the attendees of the current bill.
     */
    public function setDebtors(string $attendees): void
    {
        $attendees = explode(',', strtolower($attendees));
        $lender = $this->getCreditor();

        // Calculate debtors debt for the bill
        $debt = $this->getPrice() / count($attendees);

        // Take out creditor from attendees
        $debtors = array_diff($attendees, [$lender]);

        // Calculate total payment for each debtors
        $countPayments = array_count_values($debtors);

        foreach ($debtors as $debtor) {
            $this->debtors[$debtor][$lender] = $debt * $countPayments[$debtor];
        }
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getCreditor(): string
    {
        return $this->creditor;
    }

    public function getDebtors(): array
    {
        return $this->debtors;
    }

    /**
     * Check if the ingested data row is valid or not
     * 
     * @param array $data
     * @return bool
     */
    private function isValid(array $data): bool
    {
        return count($data) === self::DATA_STRUCT_COUNT;
    }
}
