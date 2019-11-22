<?php

/**
 * VAT
 * Handle vat calculations
 * Configure the percentage with:
 * calculate set vat 20%
 */

require __DIR__ . '/functions.php';

$result = '...';
$query = get_var($argv, 1);
$percent = get_setting('vat_percentage', '16%');
$response = [];
$processed = false;
$strings = get_translation('vat');
$query = preg_replace('/[^\\d.]+/', '', $query);
if (!empty($query) && $percent) {
	$percent = (int) $percent;
	$amount = cleanup_number($query);

	$result = ($percent / 100) * $amount;
	$result = (fmod($result, 1) !== 0.00 ? bcdiv($result, 1, 2) : $result);

	if ($result && $result > 0) {
		$processed = true;
		$plustaxt = $amount + $result;
		$minustax = $amount / ((float) "1.$percent");

		$amount = format_number($amount);
		$result = format_number($result);
		$plustaxt = format_number($plustaxt, -1, true);
		$minustax = format_number($minustax, -1, true);
	}
}

if (!$processed) {
	$response[] = [
		'title' => $result,
		'subtitle' => get_text('action_copy'),
		'valid' => false,
	];
	echo '{"items": ' . json_encode($response) . ' }';
	exit(0);
}

$response[] = [
	'title' => sprintf($strings['result'], $amount, $result),
	'subtitle' => sprintf($strings['subtitle'], "{$percent}%"),
	'arg' => $result,
];

$response[] = [
	'title' => sprintf($strings['plus'], $amount, $plustaxt),
	'subtitle' => sprintf($strings['plus_subtitle'], $amount, "{$percent}%"),
	'arg' => $plustaxt,
];

$response[] = [
	'title' => sprintf($strings['minus'], $amount, $minustax),
	'subtitle' => sprintf($strings['minus_subtitle'], $amount, "{$percent}%"),
	'arg' => $minustax,
];

echo '{"items": ' . json_encode($response).' }';
