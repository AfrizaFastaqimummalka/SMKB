<?php
define("LARAVEL_START", microtime(true));
require __DIR__ . "/vendor/autoload.php";
$app = require_once __DIR__ . "/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use App\Services\ExcelService;
use Carbon\Carbon;

$svc = app(ExcelService::class);
echo get_class($svc) . " berhasil di-instantiate\n";

// Check methods
$methods = get_class_methods($svc);
echo "Methods: " . implode(", ", $methods) . "\n";
