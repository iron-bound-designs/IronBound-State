<?php

/**
 * Utility functions.
 *
 * @author      Iron Bound Designs
 * @since       1.0
 * @copyright   2019 (c) Iron Bound Designs.
 * @license     GPLv2
 */

declare(strict_types=1);

namespace IronBound\State;

/**
 * Key a list of items by a property.
 *
 * @param iterable $items
 * @param string   $keyName
 *
 * @return array
 */
function keyList(iterable $items, string $keyName): array
{
    $list = [];

    foreach ($items as $item) {
        if (is_array($item)) {
            $key = $item[ $keyName ];
        } elseif ($item instanceof \JsonSerializable) {
            $key = $item->jsonSerialize()[ $keyName ];
        } elseif (is_object($item)) {
            $key = get_object_vars($item)[ $keyName ];
        } else {
            $key = $item[ $keyName ];
        }

        $list[ $key ] = $item;
    }

    return $list;
}

/**
 * Check if the given string ends with the given needle.
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function endsWith(string $haystack, string $needle): bool
{
    return substr($haystack, -strlen($needle)) === $needle;
}

/**
 * Check if the given string starts with the given needle.
 *
 * @param string $haystack
 * @param string $needle
 *
 * @return bool
 */
function startsWith(string $haystack, string $needle): bool
{
    return strpos($haystack, $needle) === 0;
}

/**
 * Inserts a new key/value before the key in the array.
 *
 * @param string $key      The key to insert before.
 * @param array  $array    An array to insert in to.
 * @param string $newKey   The key to insert.
 * @param mixed  $newValue The value to insert.
 *
 * @return array
 */
function arrayInsertBefore(string $key, array $array, string $newKey, $newValue): array
{
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
            if ($k === $key) {
                $new[ $newKey ] = $newValue;
            }
            $new[ $k ] = $value;
        }

        return $new;
    }

    $array[ $newKey ] = $newValue;

    return $array;
}

/**
 * Inserts a new key/value after the key in the array.
 *
 * @param string $key      The key to insert after.
 * @param array  $array    An array to insert in to.
 * @param string $newKey   The key to insert.
 * @param mixed  $newValue The value to insert.
 *
 * @return array
 */
function arrayInsertAfter(string $key, array $array, string $newKey, $newValue): array
{
    if (array_key_exists($key, $array)) {
        $new = array();
        foreach ($array as $k => $value) {
            $new[ $k ] = $value;
            if ($k === $key) {
                $new[ $newKey ] = $newValue;
            }
        }

        return $new;
    }

    $array[ $newKey ] = $newValue;

    return $array;
}

/**
 * Apply a callback over an iterable list.
 *
 * @param iterable $iterable
 * @param callable $callback
 *
 * @return array
 */
function map(iterable $iterable, callable $callback): array
{
    $mapped = [];

    foreach ($iterable as $key => $item) {
        $mapped[ $key ] = $callback($item, $key);
    }

    return $mapped;
}

/**
 * Call a method on each object in a given list.
 *
 * @param iterable $iterable
 * @param string   $method
 *
 * @return array
 */
function mapMethod(iterable $iterable, string $method): array
{
    $mapped = [];

    foreach ($iterable as $key => $item) {
        $mapped[ $key ] = ([ $item, $method ])();
    }

    return $mapped;
}

/**
 * Build an array of keys and values by mapping over a set of values.
 *
 * @param iterable $iterable
 * @param callable $callback Return a tuple of key, value.
 *
 * @return array
 */
function mapCombine(iterable $iterable, callable $callback): array
{
    $arr = [];

    foreach ($iterable as $k => $item) {
        [ $key, $value ] = $callback($item, $k);

        $arr[ $key ] = $value;
    }

    return $arr;
}

/**
 * Applies the callback for each item in the list.
 *
 * @param iterable $iterable
 * @param callable $callback
 */
function each(iterable $iterable, callable $callback)
{
    foreach ($iterable as $key => $value) {
        $callback($value, $key);
    }
}

/**
 * Filter the list to contain only entries that pass the given predicate.
 *
 * @param iterable $iterable
 * @param callable $predicate
 *
 * @return array
 */
function filter(iterable $iterable, callable $predicate): array
{
    $filtered = [];

    foreach ($iterable as $key => $value) {
        if ($predicate($value, $key)) {
            $filtered[ $key ] = $value;
        }
    }

    return $filtered;
}

/**
 * At least one item passes the given predicate.
 *
 * @param iterable $iterable
 * @param callable $predicate
 *
 * @return bool
 */
function atLeastOne(iterable $iterable, callable $predicate): bool
{
    foreach ($iterable as $item) {
        if ($predicate($item)) {
            return true;
        }
    }

    return false;
}

/**
 * Pick a list of values from an array.
 *
 * @param array $array
 * @param array $fields
 *
 * @return array
 */
function arrayPick(array $array, array $fields): array
{
    $picked = [];

    foreach ($fields as $field) {
        $picked[ $field ] = $array[ $field ] ?? null;
    }

    return $picked;
}

/**
 * Generate an array without the given fields.
 *
 * @param array $array
 * @param array $fields
 *
 * @return array
 */
function arrayWithoutKeys(array $array, array $fields): array
{
    $picked = [];
    $flip   = array_flip($fields);

    foreach ($array as $key => $value) {
        if (! isset($flip[ $key ])) {
            $picked[ $key ] = $value;
        }
    }

    return $picked;
}

/**
 * Find the first value that passes the given predicate.
 *
 * @param iterable $iterable
 * @param callable $predicate
 *
 * @return mixed|null
 */
function arrayFind(iterable $iterable, callable $predicate)
{
    foreach ($iterable as $value) {
        if ($predicate($value)) {
            return $value;
        }
    }

    return null;
}

/**
 * Return all items that are instances of the given class.
 *
 * @param iterable $iterable
 * @param string   $class
 *
 * @return array
 */
function instancesOf(iterable $iterable, string $class): array
{
    $instances = [];

    foreach ($iterable as $value) {
        if ($value instanceof $class) {
            $instances[] = $value;
        }
    }

    return $instances;
}

/**
 * If a function, return the result of the function, otherwise return the value.
 *
 * @param callable|mixed $value
 * @param mixed          ...$context
 *
 * @return mixed
 */
function result($value, ...$context)
{
    return is_callable($value) ? $value(...$context) : $value;
}

/**
 * Return a negated version of the given function.
 *
 * @param callable $function
 *
 * @return callable
 */
function negate(callable $function): callable
{
    return static function (...$args) use ($function) {
        return ! $function(...$args);
    };
}

/**
 * Cast an iterator to an array, without erroring if the value is an array already.
 *
 * @param iterable $iterable
 *
 * @return array
 */
function castArray(iterable $iterable): array
{
    return is_array($iterable) ? $iterable : iterator_to_array($iterable, true);
}

/**
 * Is the array numerically indexed starting at 0 with no gaps.
 *
 * @param array|mixed $array
 *
 * @return bool
 */
function isNumericArray($array): bool
{
    if (! $array || ! is_array($array)) {
        return false;
    }

    $next = 0;

    foreach ($array as $k => $v) {
        if ($k !== $next++) {
            return false;
        }
    }

    return true;
}

/**
 * Pluck fields from a list.
 *
 * @param iterable $iterable
 * @param string   $field
 *
 * @return array
 */
function listPluck(iterable $iterable, string $field): array
{
    $arr = [];

    foreach ($iterable as $key => $value) {
        $arr[ $key ] = $value[ $field ] ?? null;
    }

    return $arr;
}

/**
 * Pluck fields from a list using a method.
 *
 * @param iterable $iterable
 * @param string   $method
 *
 * @return array
 */
function listPluckByMethod(iterable $iterable, string $method): array
{
    $arr = [];

    foreach ($iterable as $key => $value) {
        $arr[ $key ] = $value->{$method}();
    }

    return $arr;
}

/**
 * Get the type of a variable.
 *
 * @param mixed $mixed
 *
 * @return string
 */
function varType($mixed): string
{
    if (is_object($mixed)) {
        return get_class($mixed);
    }

    return \gettype($mixed);
}

/**
 * Get the first value out of an iterator.
 *
 * @param iterable $iter
 *
 * @return mixed|null
 */
function first(iterable $iter)
{
    foreach ($iter as $v) {
        return $v;
    }

    return null;
}

/**
 * Get the last value out of an iterator.
 *
 * @param iterable $iter
 *
 * @return mixed|null
 */
function last(iterable $iter)
{
    $last = null;

    foreach ($iter as $v) {
        $last = $v;
    }

    return $last;
}

/**
 * Unique a list by a callback.
 *
 * @param iterable $list     List of items.
 * @param callable $callback Callback that accepts each member of the list and returns a string to unique them by.
 *
 * @return iterable
 */
function uniqueBy(iterable $list, callable $callback): array
{
    $uniqued = [];

    foreach ($list as $item) {
        $uniqued[ $callback($item) ] = $item;
    }

    return array_values($uniqued);
}
