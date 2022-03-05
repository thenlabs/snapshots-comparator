<?php
declare(strict_types=1);

namespace ThenLabs\SnapshotsComparator;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 * @abstract
 */
abstract class Comparator
{
    public static function compare(array $before, array $after, ExpectationBuilder $expectationBuilder = null): Result
    {
        $result = new Result();

        if (! $expectationBuilder) {
            $expectationBuilder = new ExpectationBuilder();
        }

        $diff = self::arrayRecursiveDiff($before, $after);

        // It's required becouse 'arrayRecursiveDiff' perform the comparision in only one sense.
        $diff = array_replace_recursive($diff, self::arrayRecursiveDiff($after, $before));

        if (empty($diff)) {
            return $result;
        }

        $created = [];
        $updated = [];
        $deleted = [];

        self::classifyDiff($before, $after, $diff, $created, $updated, $deleted);

        $result->setCreated($created);
        $result->setUpdated($updated);
        $result->setDeleted($deleted);

        $unexpectations = [];
        $expectations = $expectationBuilder->getExpectations();

        if (! empty($created)) {
            $unexpectations['CREATED'] = $created;

            self::filterUnexpectations($unexpectations['CREATED'], $expectations['CREATED']);

            if (empty($unexpectations['CREATED'])) {
                unset($unexpectations['CREATED']);
            }
        }

        if (! empty($updated)) {
            $unexpectations['UPDATED'] = $updated;

            self::filterUnexpectations($unexpectations['UPDATED'], $expectations['UPDATED']);

            if (empty($unexpectations['UPDATED'])) {
                unset($unexpectations['UPDATED']);
            }
        }

        if (! empty($deleted)) {
            $unexpectations['DELETED'] = $deleted;

            self::filterUnexpectations($unexpectations['DELETED'], $expectations['DELETED']);

            if (empty($unexpectations['DELETED'])) {
                unset($unexpectations['DELETED']);
            }
        }

        $result->setUnexpectations($unexpectations);

        return $result;
    }

    private static function classifyDiff(array $before, array $after, array $diff, array &$created, array &$updated, array &$deleted): void
    {
        foreach ($diff as $key => $value) {
            if (! array_key_exists($key, $before) && array_key_exists($key, $after)) {
                $created[$key] = $value;
            } elseif (array_key_exists($key, $before) && ! array_key_exists($key, $after)) {
                $deleted[$key] = $value;
            } elseif (is_array($diff[$key])) {
                $created[$key] = [];
                $updated[$key] = [];
                $deleted[$key] = [];

                self::classifyDiff($before[$key], $after[$key], $diff[$key], $created[$key], $updated[$key], $deleted[$key]);

                if (empty($created[$key])) {
                    unset($created[$key]);
                }

                if (empty($updated[$key])) {
                    unset($updated[$key]);
                }

                if (empty($deleted[$key])) {
                    unset($deleted[$key]);
                }
            } else {
                $updated[$key] = $after[$key];
            }
        }
    }

    /**
     * @param array $unexpectations
     * @param array|callable $expectations
     * @return mixed
     */
    private static function filterUnexpectations(array &$unexpectations, $expectations)
    {
        if (is_callable($expectations)) {
            return $expectations($unexpectations);
        }

        if (is_array($expectations)) {
            if (empty($expectations)) {
                return;
            }

            foreach ($unexpectations as $key => $value) {
                if (is_array($value) && !empty($value)) {
                    if (true == self::filterUnexpectations($unexpectations[$key], $expectations[$key])) {
                        unset($unexpectations[$key]);
                    }

                    if (empty($unexpectations[$key])) {
                        unset($unexpectations[$key]);
                    }
                } else {
                    $expectationValue = $expectations[$key];

                    if ((is_callable($expectationValue) && true == $expectationValue($value)) ||
                        $expectationValue === $value
                    ) {
                        unset($unexpectations[$key]);
                    }
                }
            }
        }
    }

    /**
     * @see https://www.php.net/manual/en/function.array-diff.php#91756
     */
    public static function arrayRecursiveDiff($aArray1, $aArray2)
    {
        $aReturn = array();

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = self::arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }
}
