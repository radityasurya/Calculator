<?php

namespace Tests;

use App\Calculator;
use App\BillCollection;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    public function setUp(): void
    {
        $lines = [
            '40.00 Thijs Danny,Danny,Thijs,Stefan,Den',
            '45.00 Danny Danny,Thijs,Stefan,Den',
            '36.00 Stefan Danny,Thijs,Stefan',
            '40.00 Stefan Danny,Thijs,stefan,Den',
            '40.00 Danny Danny,Thijs,Stefan,Den',
            '12.00 Stefan Thijs,Stefan,Den',
            '44.00 Danny Danny,Thijs,Stefan,Den',
            '42.40 Den Danny,Stefan,Den,Den',
            '40.00 danny Danny,Thijs,Stefan,Den',
            '50.40 Thijs Danny,Thijs,Den',
            '48.00 Den Danny,thijs,Stefan,Den',
            '84.00 Thijs Thijs,Stefan,den'
        ];

        $Bills = new BillCollection($lines);
        $this->calculator = new Calculator($Bills);
    }

    public function tearDown(): void
    {
        unset($this->calculator);
    }

    public function test__construct()
    {
        $this->assertInstanceOf(Calculator::class, $this->calculator);
    }

    public function testCalculation()
    {
        $expected = "Thijs pays Danny 9.45" . PHP_EOL .
            "Stefan pays Thijs 10.00" . PHP_EOL .
            "Stefan pays Danny 20.25" . PHP_EOL .
            "Stefan pays Den 8.60" . PHP_EOL .
            "Den pays Thijs 40.80" . PHP_EOL .
            "Den pays Danny 19.65" . PHP_EOL;

        $result = $this->calculator->printBill();

        $this->expectOutputString($expected, $result);
    }

    public function testOptimizedCalculation()
    {
        $expected = "Stefan pays Thijs 9.15" . PHP_EOL .
            "Stefan pays Danny 29.70" . PHP_EOL .
            "Den pays Thijs 32.20" . PHP_EOL .
            "Den pays Danny 19.65" . PHP_EOL;

        $this->calculator->setIsOptimized(true);
        $result = $this->calculator->printBill();

        $this->expectOutputString($expected, $result);
    }


}
