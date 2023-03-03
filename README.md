# N-Meta PHP sdk

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/badges/build.png?b=master)](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/monstar-lab-oss/n-meta-php/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## 📝 Introduction

Core of N-Meta, parsing and DTO, A User-Agent header for android and ios apps.

Used to parse the N-Meta headers using following format:

`Meta: [PLATFORM];[ENVIRONMENT];[APP_VERSION];[DEVICE_OS];[DEVICE]`

#### PLATFORM:
Description of the requesting platform, expects values `android`, `ios` or `web`

#### ENVIRONMENT:
Description of the environment the request is targeted at, normally `staging` or `production`

#### APP_VERSION:
Semantic version sting of the requesting client `x.y.z`. Not required if platform is web.

#### DEVICE_OS:
Version of the operating system the request was made from, eg. `10.2`. Not required if platform is web.

#### DEVICE:
Description of the device making the request, eg. `iphone-x`. Not required if platform is web.

## 📦 Installation

To use this package you will need:

* PHP 8.0+

Run

`composer require monstar-lab/n-meta`

or setup in composer.json

`monstar-lab/n-meta: 2.0.x`


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
