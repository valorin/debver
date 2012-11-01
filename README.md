debver - Debian/Ubuntu packager version helper
==============================================
*Version 1.1*  [![Build Status](https://secure.travis-ci.org/valorin/debver.png)](http://travis-ci.org/valorin/debver)

Simple PHP helper class for working with Debian/Ubuntu package version strings.

Installing
----------

The easiest way to install **Debver** is to use [Composer](http://getcomposer.org/download/), the awesome dependency manager for PHP. Once Composer is installed, run `composer.phar require valorin/debver:1.*` and composer will do all the hard work for you.

Usage
-----

If you are using the autoloader in Composer (or your framework ties into it), then all you need to do is add a `use Debver\Version;` statement at the top of each file you wish to use **Debver** in and us e it like a normal class:

```php
<?php
namespace YourApplication;
use Debver\Version;

$version1 = "5.1.2-1ubuntu3.9";
$version2 = "3.9.4-4ubuntu0.2";

$compare = Version::compare($version1, $version2);
```

**Compare two version strings:**

```php
$result = Version::compare($version1, $version2);

if ($result == -1) {
    echo "{$version1} < {$version2}";
} elseif ($result == 0) {
    echo "{$version1} == {$version2}";
} elseif ($result == 1) {
    echo "{$version1} > {$version2}";
}
```

**Extract version string components:**

The three components to a version string can be extracted easily: *Epoch*, *Upstream Version*, and *Debian Revision*:

```php
$version = new Version($version1);

$epoch    = $version->getEpoch();
$upstream = $version->getUpstream();
$revision = $version->getRevision();
```

[Ubuntu manual: deb-version](http://manpages.ubuntu.com/manpages/deb-version.5.html)

**Compare two version strings using dpkg:**

If you are running on a Ubuntu/Debian box, you can use `dpkg` directly to compare two packages (100% accuracy for all of the really wacky version strings).
This option was added for testing the custom functions, and I decided to leave it in just in case.
Internally it uses `dpkg --compare-versions {$version1} lt {$version2}` via a `system()` call.

```php
$result = Version::compareWithDpkg($version1, $version2);

if ($result == -1) {
    echo "{$version1} < {$version2}";
} elseif ($result == 0) {
    echo "{$version1} == {$version2}";
} elseif ($result == 1) {
    echo "{$version1} > {$version2}";
}
```

**Retrieve a *"compare string"* for storing in a database**

Occasionally you need to store a large amount of version numbers in a database and then compare them in bulk using the database itself, rather than extracting the data and manipulating it in code. A *"compare string"* is a verbose version of the version string that can be compared using basic string comparison functions (`>` `<` `==`), making it perfect for use in a database..

```php
$string = Version::getCompareString($version);
```
