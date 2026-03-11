<?php

declare(strict_types=1);

use JPry\YNAB\Model\Budget;
use JPry\YNAB\Model\Plan;
use JPry\YNAB\Model\Transaction;

it('maps plan rows into typed objects', function () {
	$plan = Plan::fromArray(['id' => 'P1', 'name' => 'Main']);

	expect($plan)->not->toBeNull();
	expect($plan?->id)->toBe('P1');
	expect($plan?->name)->toBe('Main');
});

it('keeps budget model mapping while warning about deprecation', function () {
	$warnings = [];
	$handler = static function (int $errno, string $errstr) use (&$warnings): bool {
		if ($errno === E_USER_DEPRECATED) {
			$warnings[] = $errstr;
			return true;
		}

		return false;
	};

	set_error_handler($handler);
	try {
		$budget = Budget::fromArray(['id' => 'B1', 'name' => 'Main']);
	} finally {
		restore_error_handler();
	}

	expect($budget)->not->toBeNull();
	expect($budget?->id)->toBe('B1');
	expect($budget?->name)->toBe('Main');
	expect(implode("\n", $warnings))->toContain('JPry\\YNAB\\Model\\Budget is deprecated');
});

it('maps transaction rows and keeps raw payload', function () {
	$row = [
		'id' => 'T1',
		'account_id' => 'A1',
		'amount' => -1234,
		'is_pending' => false,
		'memo' => 'Note',
	];

	$tx = Transaction::fromArray($row);

	expect($tx)->not->toBeNull();
	expect($tx?->id)->toBe('T1');
	expect($tx?->amount)->toBe(-1234);
	expect($tx?->raw)->toBe($row);
});
