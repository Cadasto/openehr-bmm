# openEHR BMM (Cadasto)

Opinionated PHP library implementing the **openEHR Basic Meta-Model (BMM)**. 
It serves primarily as an intermediate representation (IR) of [P_BMM](https://specifications.openehr.org/releases/LANG/latest/bmm_persistence.html) specifications, providing typed PHP objects for schemas, packages, classes, properties, types, and functions.

## Features

- Parse openEHR BMM JSON schemas into strongly-typed PHP objects (`BmmSchema`, `BmmClass`, `BmmPackage`, etc.)
- Serialize models back to JSON (`JsonSerializable`)
- Support for all P_BMM class variants: classes, interfaces, enumerations (string and integer)
- Property types: single, container, generic, and open single properties
- Function definitions with parameters, pre/post-conditions, and result types
- Generic parameter definitions and generic types
- Typed collections with alias support

## Installation

```bash
composer require cadasto/openehr-bmm
```

## Quick start

```php
use Cadasto\OpenEHR\BMM\Model\BmmSchema;

// Load a BMM schema from a JSON file
$json = json_decode(file_get_contents('openehr_rm_1.2.0.bmm.json'), true);
$schema = BmmSchema::fromArray($json);

// Access schema metadata
echo $schema->getSchemaId(); // "openehr_rm_1.2.0"

// Navigate packages and classes
foreach ($schema->packages as $package) {
    echo $package->name . PHP_EOL;
}

// Serialize back to JSON
echo json_encode($schema, JSON_PRETTY_PRINT);
```

## Development

### With Docker

```bash
make build
make install
make ci
```

### Without Docker

```bash
composer install
composer ci
```

### Composer scripts

| Script | Description |
|--------|-------------|
| `composer test` | Run PHPUnit |
| `composer test:dox` | PHPUnit with testdox output |
| `composer test:coverage` | PHPUnit with HTML coverage report |
| `composer check:lint` | Parallel-lint (syntax) |
| `composer check:cs` | PHPCS (PSR-12) |
| `composer check:phpstan` | PHPStan (level 8) |
| `composer rector` | Run Rector refactoring |
| `composer ci` | Run all checks (lint, CS, PHPStan, tests) |

### Standards

- **Style**: PSR-12 (PHPCS)
- **Static analysis**: PHPStan level 8
- **Tests**: PHPUnit 12
- **Refactoring**: Rector (local only)

See [docs/development.md](docs/development.md) for details.

## Releases

Tag with SemVer (no `v` prefix), e.g. `git tag 1.0.0 && git push origin 1.0.0`. See [docs/releases.md](docs/releases.md).

## License

MIT — see [LICENSE](LICENSE).
