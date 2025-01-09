<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPath(__DIR__ . '/app/Providers/AppServiceProvider.php', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPath(__DIR__ . '/app/Providers/TelescopeServiceProvider.php', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPath(__DIR__ . '/app/Http/Controllers/Admin/System/DatabaseController.php', [ErrorType::SHADOW_DEPENDENCY])
    ->ignoreErrorsOnPackages([
        'league/flysystem-aws-s3-v3',
        'league/flysystem-path-prefixing',
        'symfony/postmark-mailer',
        'vagebond/aannemer',
    ], [ErrorType::UNUSED_DEPENDENCY])
    ->disableExtensionsAnalysis();
