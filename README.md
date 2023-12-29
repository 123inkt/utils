[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF)](https://php.net/)

## DR Utility Classes

A library with everyday use classes and methods:

## Arrays

- `first` - Get the first element of an array, exception otherwise.
- `firstOrNull` - Get the first element of an array, null otherwise.
- `last` - Get the last element of an array, exception otherwise.
- `lastOrNull` - Get the last element of an array, null otherwise.
- `find` - Find the first element in an array that matches a predicate, exception otherwise.
- `findOrNull` - Find the first element in an array that matches a predicate, null otherwise.
- `contains` - Test if the given value is contained within items. Supports `EquatableInterface`.
- `diff` - Get the difference between two arrays. Supports `ComparableInterface`.
- `explode` - Explode a string or null into an array, with exploding empty string to empty array.
- `mapAssoc` - Map an array to a new array using a callback, preserving keys. 
- `reindex` - Reindex an array with new keys based on the result of the callback.
- `groupBy` - Group items by the given return value of the callback.
- `remove` - Remove an element from an array based on the callback. Supports `EquatableInterface`.
- `removeKey` - Remove an element from an array based on the key.
- `removeKeys` - Remove multiple elements from an array based on the keys.
- `removeTypes` - Filters an array by removing all values that match the provided types.
- `removeNull` - Filters an array by removing all null values.
- `search` - Find the key of an element in an array or false otherwise. Supports `EquatableInterface`.
- `unique` - Remove duplicate values from an array. Supports `EquatableInterface`.
- `wrap` - Wrap a value in an array, unless it is already an array.

## Assert

Fluent assertion methods, inspired by `webmozart/assert`:

- `null`
- `notNull`
- `isArray`
- `inArray`
- `isCallable`
- `resource`
- `object`
- `scalar`
- `integer`
- `float`
- `string`
- `startsWith`
- `notStartsWith`
- `endsWith`
- `notEndsWith`
- `boolean`
- `true`
- `false`
- `notFalse`
- `nonEmptyString`
- `isInstanceOf`
- `fileExists`
- `file`
- `directory`
- `readable`
- `writable`

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
