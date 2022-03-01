<?php
declare(strict_types=1);

namespace ThenLabs\TestSnapshots\Comparator;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class Comparator
{
    public static function compare(array $before, array $after, ExpectationList $expectations): Result
    {
        return new Result();
    }
}
