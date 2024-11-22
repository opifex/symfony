<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Traversable;

#[Exclude]
class AccountCollection implements Countable, IteratorAggregate
{
    /** @var Account[] */
    private readonly array $accounts;

    /** @var int<0, max> */
    private readonly int $count;

    /**
     * @param Account[] $accounts
     * @param int<0, max>|null $count
     */
    public function __construct(array $accounts, ?int $count = null)
    {
        $this->accounts = $accounts;
        $this->count = $count ?? count($this->accounts);
    }

    #[Override]
    public function count(): int
    {
        return $this->count;
    }

    /**
     * @return Traversable<int, Account>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->accounts);
    }
}
