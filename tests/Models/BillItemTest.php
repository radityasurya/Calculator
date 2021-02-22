<?php

namespace Tests\Models;

use App\Models\BillItem;
use PHPUnit\Framework\TestCase;

class BillItemTest extends TestCase
{
    private BillItem $billItem;

    public function setUp(): void
    {
        $testRow = '84.00 Thijs Thijs,Stefan,Den';
        $this->billItem = new BillItem($testRow);
    }

    public function tearDown(): void
    {
        unset($this->billItem);
    }

    public function test__construct()
    {
        $testLine = '84.00 Thijs Thijs,Stefan,den';
        $result = new BillItem($testLine);

        $this->assertInstanceOf(BillItem::class, $result);
    }

    public function testGetPrice()
    {
        $expected = 84.00;
        $result = $this->billItem->getPrice();

        $this->assertEquals($expected, $result);
    }

    public function testGetCreditor()
    {
        $expected = "thijs";
        $result = $this->billItem->getCreditor();

        $this->assertEquals($expected, $result);
    }

    public function testGetDebtors()
    {
        $expected = [
            "stefan" => [
                "thijs" => 28.0
            ],
            "den" => [
                "thijs" => 28.0
            ]
        ];
        $result = $this->billItem->getDebtors();

        $this->assertEquals($expected, $result);
    }

    public function testSetDebtors()
    {
        $expected = [
            "stefan" => [
                "thijs" => 21.0
            ],
            "den" => [
                "thijs" => 21.0
            ],
            "danny" => [
                "thijs" => 21.0
            ]
        ];
        $this->billItem->setDebtors("Thijs,Stefan,Den,Danny");
        $result = $this->billItem->getDebtors();

        $this->assertEquals($expected, $result);
    }

    public function testSetPrice()
    {
        $expected = "99.0";
        $this->billItem->setPrice(99.0);
        $result = $this->billItem->getPrice();

        $this->assertEquals($expected, $result);
    }

    public function testSetCreditor()
    {
        $expected = "Den";
        $this->billItem->setCreditor("Den");
        $result = $this->billItem->getCreditor();

        $this->assertEquals($expected, $result);
    }
}
