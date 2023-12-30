# PhdExceptionalValidationBundle

🧰 Provides [Symfony Messenger](https://symfony.com/doc/current/messenger.html) middleware to capture any given exception
and map it into [Symfony Validator](https://symfony.com/doc/current/validation.html) violations format.

[![Build Status](https://github.com/phphd/exceptional-validation-bundle/actions/workflows/ci.yaml/badge.svg?branch=main)](https://github.com/phphd/exceptional-validation-bundle/actions?query=branch%3Amain)
[![Psalm level](https://shepherd.dev/github/phphd/exceptional-validation-bundle/level.svg)](https://shepherd.dev/github/phphd/exceptional-validation-bundle)
[![Psalm coverage](https://shepherd.dev/github/phphd/exceptional-validation-bundle/coverage.svg)](https://shepherd.dev/github/phphd/exceptional-validation-bundle)
[![Codecov](https://codecov.io/gh/phphd/exceptional-validation-bundle/graph/badge.svg?token=GZRXWYT55Z)](https://codecov.io/gh/phphd/exceptional-validation-bundle)
[![Licence](https://img.shields.io/github/license/phphd/exceptional-validation-bundle.svg)](https://github.com/phphd/exceptional-validation-bundle/blob/main/LICENSE)

## Installation 📥

1. Install via composer

    ```sh
    composer require phphd/exceptional-validation-bundle
    ```

2. Enable the bundle in the `bundles.php`

    ```php
    PhPhD\ExceptionalValidationBundle\PhdExceptionalValidationBundle::class => ['all' => true],
    ```

## Configuration ⚒️

To leverage features of this bundle, you should add `phd_exceptional_validation` middleware to the list:

```diff
framework:
    messenger:
        buses:
            command.bus:
                middleware:
                    - validation
+                   - phd_exceptional_validation
                    - doctrine_transaction
```

## Usage 🚀

Here is an example of message that is dispatched to the bus.
It must define `#[ExceptionalValidation]` attribute on the class itself and `#[Capture]` attributes on the properties.

```php
use PhPhD\ExceptionalValidation;

#[ExceptionalValidation]
final readonly class CreateVacationRequestCommand
{
    public function __construct(
        public Employee $employee,
        
        #[ExceptionalValidation\Capture(VacationTypeNotFoundException::class, 'vacation.type_not_found')]
        public int $vacationTypeId,
        
        #[Assert\DateTime]
        public string $startDate,

        #[Assert\DateTime]
        #[ExceptionalValidation\Capture(InsufficientVacationBalanceException::class, 'vacation.insufficient_balance')]
        public string $endDate,
    ) {
    }
}
```

In addition to standard validator constraints, certain properties also have `#[Capture]` attributes. These attributes
specify the particular exception class to be intercepted and the corresponding validation message to be shown when that
exception occurs.
