<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPath(__DIR__ . '/app/Providers/TelescopeServiceProvider.php', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPackages([
        'league/flysystem-aws-s3-v3',
        'league/flysystem-path-prefixing',
        'symfony/postmark-mailer',
        'vagebond/aannemer',
        'vagebond/beeld',
    ], [ErrorType::UNUSED_DEPENDENCY]);
