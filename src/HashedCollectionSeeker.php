<?php

namespace WabLab\Collection;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Contracts\IHashCollectionSeeker;


class HashedCollectionSeeker implements IHashCollectionSeeker
{

    protected IHashCollection $collection;

    protected ?string $currentHash = null;

    public function __construct(IHashCollection $collection, ?string $initHash = null)
    {
        $this->collection = $collection;
        $this->currentHash = $initHash ?? $collection->firstHash();
    }

    public function current(string $hash): ?IHashCollectionSeeker
    {
        $this->currentHash = $hash;
        return $this;
    }

    public function next(): ?IHashCollectionSeeker
    {
        $generator = $this->collection->yieldAll($this->currentHash);
        if($generator) {
            $generator->next();
            $this->currentHash = $generator->key();
        }
        return $this;
    }

    public function prev(): ?IHashCollectionSeeker
    {
        $generator = $this->collection->reverseYieldAll($this->currentHash);
        if($generator) {
            $generator->next();
            $this->currentHash = $generator->key();
        }
        return $this;
    }

    public function first(): ?IHashCollectionSeeker
    {
        $this->currentHash = $this->collection->firstHash();
        return $this;
    }

    public function last(): ?IHashCollectionSeeker
    {
        $this->currentHash = $this->collection->lastHash();
        return $this;
    }

    public function hash(): ?string
    {
        return $this->currentHash;
    }

    public function value()
    {
        return $this->currentHash ? $this->collection->find($this->currentHash) : null;
    }
}