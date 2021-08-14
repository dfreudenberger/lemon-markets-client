<?php
declare(strict_types=1);

if (!is_file(__DIR__ . '/../vendor/autoload.php')) {
    throw new LogicException('Could not find autoload.php in vendor/. Did you run "composer install"?');
}
