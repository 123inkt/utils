[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF)](https://php.net/)

## DR Utility Classes

A library with everyday use classes and methods:

## Arrays

- `first` - Get the first element of an array, exception otherwise.
- `firstOrNull` - Get the first element of an array, null otherwise.
- `last` - Get the last element of an array, exception otherwise.
- `lastOrNull` - Get the last element of an array, null otherwise.
- `find` - Find the first element in an array that matches a predicate, exception otherwise.
- `tryFind` - Find the first element in an array that matches a predicate, null otherwise.
- `mapAssoc` - Map an array to a new array using a callback, preserving keys. Supports `EquatableInterface`.
- `reindex` - Reindex an array with new keys based on the result of the callback. Supports `EquatableInterface`.
- `remove` - Remove an element from an array based on the callback. Supports `EquatableInterface`.
- `search` - Find the key of an element in an array based on the callback. Supports `EquatableInterface`.
- `unique` - Remove duplicate values from an array. Supports `EquatableInterface`.
- `diff` - Get the difference between two arrays. Supports `ComparableInterface`.

## Assert

Fluent assertion methods, inspired by `webmozart/assert`:

- `notNull`
- `isArray`
- `isCallable`
- `integer`
- `float`
- `string`
- `boolean`
- `false`
- `notFalse`
- `isInstanceOf`

### Example
```php
$value = Assert::notNull($this->repository->find(123));
```

## Installation

```shell
composer require digitalrevolution/utils
```

## About us

At 123inkt (Part of Digital Revolution B.V.), every day more than 50 development professionals are working on improving our internal ERP
and our several shops. Do you want to join us? [We are looking for developers](https://www.werkenbij123inkt.nl/zoek-op-afdeling/it).
