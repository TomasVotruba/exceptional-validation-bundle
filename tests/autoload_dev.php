<?php

declare(strict_types=1);

/**
 * This require_once statement is necessary to make static analysis (phpstan, psalm) work correctly.
 * The same is possible to achieve with bootstrapFiles phpstan configuration and autoload psalm attribute,
 * though to keep things unified it is easier just to add it to files section in composer autoload-dev config.
 */
require_once __DIR__.'/../bin/phpunit.phar';
