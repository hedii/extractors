[![Build Status](https://travis-ci.org/hedii/extractors.svg?branch=master)](https://travis-ci.org/hedii/extractors)

# Extractors

Extractor is a package that find targeted types of resources in html dom.
Currently, only 2 types of resources are available: urls and emails.
The `Extractor` class can be extended to add more resources types.

### Install

Via Composer

``` bash
composer require hedii/extractors
```

### Usage

Currently, only 2 types of resources are available: urls and emails.

``` php
$extractor = new \Hedii\Extractors\Extractor();
$urls = $extractor->searchFor(['urls'])->at('http://example.com')->get();
$emails = $extractor->searchFor(['emails'])->at('http://example.com')->get();
$urlsAndEmails = $extractor->searchFor(['urls', 'emails'])->at('http://example.com')->get();
```


### Testing

``` bash
composer test
```

