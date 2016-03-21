# StatisticoBundle

Wrapper bundle for the [Statistico](https://github.com/fieg/statistico) library.

[![Build Status](https://travis-ci.org/fieg/statistico-bundle.svg?branch=master)](https://travis-ci.org/fieg/statistico-bundle)
[![Code Coverage](https://scrutinizer-ci.com/g/fieg/statistico-bundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/fieg/statistico-bundle/)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fieg/statistico-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fieg/statistico-bundle/)

## Installation

Using composer:

```sh
composer require fieg/statistico-bundle:dev-master
```

Add to `AppKernel.php`:

```php
$bundles = [
  new Fieg\StatisticoBundle\FiegStatisticoBundle(),
];
```

Configuration in `app/config/config.yml`:

```yaml
fieg_statistico:
  driver:
    redis:
      client: 'your_redis_service'
```

## Usage

Inject statistico into some service:

```yaml
some_service:
    class: Acme\SomeService
    arguments: [@statistico]
```

Usage:

```php
namespace Acme;

class SomeService
{
    public function __construct(Statistico $statistico)
    {
        $this->statistico = $statistico;
    }
    
    public function someAction()
    {
        $this->statistico->increment('some.statistic.indentitier');  // increases the statistic with 1
    }
}
```
