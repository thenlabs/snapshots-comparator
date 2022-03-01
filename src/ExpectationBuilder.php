<?php
declare(strict_types=1);

namespace ThenLabs\SnapshotsComparator;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class ExpectationBuilder
{
    /**
     * @var array
     */
    protected $expectations = [
        'created' => [],
        'updated' => [],
        'deleted' => [],
    ];

    public function getExpectations(): array
    {
        return $this->expectations;
    }

    public function expectCreated(array $expectations): void
    {
        $this->expectations['created'] = array_replace_recursive(
            $this->expectations['created'],
            $expectations
        );
    }

    public function expectUpdated(array $expectations): void
    {
        $this->expectations['updated'] = array_replace_recursive(
            $this->expectations['updated'],
            $expectations
        );
    }

    public function expectDeleted(array $expectations): void
    {
        $this->expectations['deleted'] = array_replace_recursive(
            $this->expectations['deleted'],
            $expectations
        );
    }
}
