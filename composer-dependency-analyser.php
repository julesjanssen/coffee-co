<?php

declare(strict_types=1);

use ShipMonk\ComposerDependencyAnalyser\Config\Configuration;
use ShipMonk\ComposerDependencyAnalyser\Config\ErrorType;

$config = new Configuration();

return $config
    ->ignoreErrorsOnPath(__DIR__ . '/app/Providers/TelescopeServiceProvider.php', [ErrorType::DEV_DEPENDENCY_IN_PROD])
    ->ignoreErrorsOnPackage('league/flysystem-aws-s3-v3', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('league/flysystem-path-prefixing', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('symfony/postmark-mailer', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('vagebond/aannemer', [ErrorType::UNUSED_DEPENDENCY])
    ->ignoreErrorsOnPackage('vagebond/beeld', [ErrorType::UNUSED_DEPENDENCY])
;
