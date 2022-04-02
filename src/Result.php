<?php
declare(strict_types=1);

namespace ThenLabs\SnapshotsComparator;

/**
 * @author Andy Daniel Navarro Taño <andaniel05@gmail.com>
 */
class Result
{
    /**
     * @var array
     */
    protected $created = [];

    /**
     * @var array
     */
    protected $updated = [];

    /**
     * @var array
     */
    protected $deleted = [];

    /**
     * @var array
     */
    protected $unexpectations = [];

    /**
     * @var array
     */
    protected $unusedExpectations = [];

    public function isSuccessful(): bool
    {
        if (empty($this->unexpectations) && empty($this->unusedExpectations)) {
            return true;
        }

        return false;
    }

    public function getCreated(): array
    {
        return $this->created;
    }

    public function getUpdated(): array
    {
        return $this->updated;
    }

    public function getDeleted(): array
    {
        return $this->deleted;
    }

    public function setCreated(array $created): void
    {
        $this->created = $created;
    }

    public function setUpdated(array $updated): void
    {
        $this->updated = $updated;
    }

    public function setDeleted(array $deleted): void
    {
        $this->deleted = $deleted;
    }

    public function getUnexpectations(): array
    {
        return $this->unexpectations;
    }

    public function setUnexpectations(array $unexpectations): void
    {
        $this->unexpectations = $unexpectations;
    }

    public function getUnusedExpectations(): array
    {
        return $this->unusedExpectations;
    }

    public function setUnusedExpectations(array $unusedExpectations): void
    {
        $this->unusedExpectations = $unusedExpectations;
    }
}
