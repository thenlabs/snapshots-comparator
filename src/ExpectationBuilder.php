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
        'CREATED' => [],
        'UPDATED' => [],
        'DELETED' => [],
    ];

    public function getExpectations(): array
    {
        return $this->expectations;
    }

    public function expectCreated(array $expectations): void
    {
        $this->expectations['CREATED'] = array_replace_recursive(
            $this->expectations['CREATED'],
            $expectations
        );
    }

    public function expectUpdated(array $expectations): void
    {
        $this->expectations['UPDATED'] = array_replace_recursive(
            $this->expectations['UPDATED'],
            $expectations
        );
    }

    public function expectDeleted(array $expectations): void
    {
        $this->expectations['DELETED'] = array_replace_recursive(
            $this->expectations['DELETED'],
            $expectations
        );
    }
}
