<?php

declare(strict_types=1);

$packageRoot = dirname(__DIR__);
$autoload = "{$packageRoot}/vendor/autoload.php";
$projectRoot = dirname(__DIR__, 3);
$rootAutoload = "{$projectRoot}/vendor/autoload.php";

if (is_file($autoload)) {
	require_once $autoload;
} elseif (is_file($rootAutoload)) {
	require_once $rootAutoload;
} else {
	throw new RuntimeException(
		'Could not find vendor/autoload.php. Run "composer install" in packages/jpry-ynab before running tests.',
	);
}

spl_autoload_register(static function (string $class) use ($packageRoot): void {
	$testsDir = __DIR__;
	$prefixes = [
		'JPry\\YNAB\\Tests\\' => "{$testsDir}/",
		'JPry\\YNAB\\' => "{$packageRoot}/src/",
	];

	foreach ($prefixes as $prefix => $baseDir) {
		if (!str_starts_with($class, $prefix)) {
			continue;
		}

		$relative = substr($class, strlen($prefix));
		$relativePath = str_replace('\\', '/', $relative);
		$path = "{$baseDir}{$relativePath}.php";
		if (is_file($path)) {
			require_once $path;
		}
	}
});
