Fshare get direct link with PHP
==========

## About
The library for get direct download link of fshare(vip account required).

## Requirement
* PHP >= 5.5

## Install

You can install and manage FshareDirectLink by using `Composer`

```
composer require xtrung/fshare-directlink
```

Or add `xtrung/fshare-directlink` into the require section of your `composer.json` file then run `composer update`

## Usage

```php
use xtrung\DirectLink\FshareDirectLink;

$fshare = new FshareDirectLink('email@example.com', '123456');

echo $fshare->getDownloadLink('https://www.fshare.vn/file/XXXXXXXXXXXX');

```