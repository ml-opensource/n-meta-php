# N-Meta PHP sdk

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/nstack-io/laravel-sdk/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## 📝 Introduction

Core of N-Meta, parsing and DTO

## 📦 Installation

To install this package you will need:

* PHP 7.1+

Run

`composer require monstar-lab/n-meta`

or setup in composer.json

`monstar-lab/n-meta: 1.0.x`


## ⚙ Usage

```php

$header = 'ios;production;1.0.0;10.2;iphone-x';
$meta = new NMeta($header);

$meta->getPlatform();
$meta->getVersion();
```  

## 🏆 Credits

This package is developed and maintained by the PHP team at [Monstar Lab](http://monstar-lab.com)

## 📄 License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
