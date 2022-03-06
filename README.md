
# SnapshotsComparator

Comparison of state arrays with expectations.

#### If you like this project gift us a ⭐.

<hr />

## Installation.

    composer require thenlabs/snapshots-comparator dev-main

## Usage example.

We have the next two status vars:

```php
$before = [
    'document1' => [
        'id' => '1',
        'title' => 'The document 1',
        'customData1' => 'custom data 1',
    ]
];

$after = [
    'document1' => [
        'id' => '1',
        'title' => 'The document one',
        'anotherData' => 'another data',
    ]
];
```

When we compare both(without declaring expectations) we obtain the next results:

```php
use ThenLabs\SnapshotsComparator\Comparator;

$result = Comparator::compare($before, $after);

false === $result->isSuccessful(); // becouse there are unexpectations.

$result->getUnexpectations() === [
    'CREATED' => [
        'document1' => [
            // 'anotherData' was created.
            'anotherData' => 'another data',
        ]
    ],
    'UPDATED' => [
        'document1' => [
            // 'title' was updated
            'title' => 'The document one',
        ]
    ],
    'DELETED' => [
        'document1' => [
            // 'customData1' was deleted
            'customData1' => 'custom data 1',
        ]
    ],
];
```

We can see that we obtain all the information about the difference between before and after, as example, which data they was created, updated or deleted.

All these changes they was unexpected becouse we not declaring expectations, and that is the reason for which `$result->isSuccessful()` returns `false.`

In the next example we declare all the expectations and that way accomplish a successful result without unexpectations.

```php
use ThenLabs\SnapshotsComparator\Comparator;
use ThenLabs\SnapshotsComparator\ExpectationBuilder;

$expectations = new ExpectationBuilder();

$expectations->expectCreated([
    'document1' => [
        'anotherData' => 'another data',
    ]
]);

$expectations->expectUpdated([
    'document1' => [
        'title' => function ($value) { // custom comparator
            return is_string($value);
        },
    ]
]);

$expectations->expectDeleted([
    'document1' => [
        'customData1' => 'custom data 1',
    ]
]);

$result = Comparator::compare($before, $after, $expectations);

true === $result->isSuccessful();
[] === $result->getUnexpectations();
```
