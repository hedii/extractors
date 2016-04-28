[![Build Status](https://travis-ci.org/hedii/extractors.svg?branch=master)](https://travis-ci.org/hedii/extractors)

# Extractors

Extractor is a package that find targeted types of resources in html dom.
Currently, only 2 types of resources are available: urls and emails.
The found resources are returned as an array.

The `Extractor` class can be extended to add more resources types.

### Install

Via Composer

``` bash
composer require hedii/extractors
```

### Usage

Currently, only 2 types of resources are available: urls and emails.

``` php
// require composer autoloader
require '/path/to/vendor/autoload.php';

// instantiate 
$extractor = new \Hedii\Extractors\Extractor();

// get all the urls on example.com page dom
$urls = $extractor->searchFor(['urls'])
    ->at('http://example.com')
    ->get();

// get all the emails on example.com page dom
$emails = $extractor->searchFor(['emails'])
    ->at('http://example.com')
    ->get();

// get all the urls and emails on example.com page dom
$urlsAndEmails = $extractor->searchFor(['urls', 'emails'])
    ->at('http://example.com')
    ->get();
```


### Testing

``` bash
composer test
```

