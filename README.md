FiegStatisticoBundle
====================

Wrapper bundle for the [Statistico](https://github.com/fieg/statistico) library.

[![Build Status](https://travis-ci.org/fieg/statistico-bundle.png?branch=master)](https://travis-ci.org/fieg/statistico-bundle)

Installation
------------

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

Usage
-----

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
