<?php

namespace Tests;

use App\BillCollection;
use App\Models\BillItem;
use PHPUnit\Framework\TestCase;

class BillCollectionTest extends TestCase
{
    private BillCollection $billCollection;

    public function setUp(): void
    {
        $lines = [
            '40.00 Thijs Danny,Danny,Thijs,Stefan,Den',
            '45.00 Danny Danny,Thijs,Stefan,Den',
            '36.00 Stefan Danny,Thijs,Stefan'
        ];

        $this->billCollection = new BillCollection($lines);
    }

    public function tearDown(): void
    {
        unset($this->$billCollection);
    }

    public function test__construct()
    {
        $this->assertInstanceOf(BillCollection::class, $this->billCollection);
    }

    public function testGetCreditors()
    {
        $expected = [
            0 => 'thijs',
            1 => 'danny',
            2 => 'stefan'
        ];
        $result = $this->billCollection->getCreditors();

        $this->assertEquals($expected, $result);
    }

    public function testGetBillItems()
    {
        $lines = [
            '40.00 Thijs Danny,Danny,Thijs,Stefan,Den',
            '45.00 Danny Danny,Thijs,Stefan,Den',
            '36.00 Stefan Danny,Thijs,Stefan'
        ];
        $expected = [];
        foreach ($lines as $line) {
            array_push($expected, new BillItem($line));
        }

        $result = $this->billCollection->getBillItems();

        $this->assertEquals($expected, $result);
    }

    public function testAddBill()
    {
        $lines = [
            '40.00 Thijs Danny,Danny,Thijs,Stefan,Den',
            '45.00 Danny Danny,Thijs,Stefan,Den',
            '36.00 Stefan Danny,Thijs,Stefan',
            '40.00 Stefan Danny,Thijs,stefan,Den'
        ];
        $expected = [];
        foreach ($lines as $line) {
            array_push($expected, new BillItem($line));
        }

        $bill = '40.00 Stefan Danny,Thijs,stefan,Den';
        $this->billCollection->addBill($bill);

        $result = $this->billCollection->getBillItems();

        $this->assertEquals($expected, $result);

    }
}
