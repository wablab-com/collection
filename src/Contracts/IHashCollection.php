<?php

namespace WabLab\Collection\Contracts;

/**
 * Interface IHashCollection
 * @package WabLab\Collection\Contracts
 */

interface IHashCollection
{

    public function insertFirst(string $hash, $data, bool $ignoreIfExists = false):bool;

    public function insertLast(string $hash, $data, bool $ignoreIfExists = false):bool;

    public function insertAfter(string $afterHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function insertBefore(string $beforeHash, string $newHash, $data, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function moveFirst(string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function moveLast(string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function moveAfter(string $afterHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function moveBefore(string $beforeHash, string $hashToMove, bool $ignoreIfFirstNotExists = false, bool $ignoreIfNewHasExists = false):bool;

    public function update(string $hash, $data, bool $ignoreIfNotExists = false):bool;

    public function updateOrInsert(string $hash, $data):bool;

    public function delete(string $hash, bool $ignoreIfNotExists = false):bool;

    public function pullOffStack(?string &$hash = null);

    public function pullOffQueue(?string &$hash = null);

    public function reHash(string $fromHash, string $toHash):bool;

    public function find(string $hash);

    public function offset(int $index, ?string &$hash = null);

    public function offsetHash(int $index):?string;

    public function isset(string $hash):bool;

    public function first(?string &$hash = null);

    public function firstHash():?string;

    public function last(?string &$hash = null);

    public function lastHash():?string;

    public function count():int;

    /**
     * @return \Generator
     */
    public function yieldAll(?string $initialHash = null);

    /**
     * @return \Generator
     */
    public function reverseYieldAll(?string $initialHash = null);

    public function seeker(?string $initHash = null):IHashCollectionSeeker;

}