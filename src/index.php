<?php

require __DIR__ .'/../vendor/autoload.php';

use App\BillCollection;
use App\Calculator;

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
$Calculator = new Calculator($Bills);
$Calculator->printBill();

echo PHP_EOL . "Optimized calculation:" . PHP_EOL;
$Calculator->setIsOptimized(true);
$Calculator->printBill();