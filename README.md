debver - Debian/Ubuntu packager version helper
==============================================
*Version 1.0*

Simple PHP helper class for comparing Debian/Ubuntu package version strings.

Usage
-----

The easiest way to get started with **debver** is to add it to the `require` section of your `./composer.json` config, and then run `./composer.phar install` or `./composer.phar update`.

```json
{
    "require": {
        "valorin/debver": "*"
    }
}
```

([What is Composer?](http://getcomposer.org/))

Once it is installed, Composer should handle the Autoloading, so you can go ahead and use it. Static methods are provided to make usage nice and quick.

**Compare two version strings:**

```php
$result = \Debver\Version::compare($version1, $version2);

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
$string = \Debver\Version::getCompareString($version);
```
