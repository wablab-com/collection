<?php

namespace WabLab\Collection\Contracts;

/**
 * Interface IHashCollection
 * @package WabLab\Collection\Contracts
 * TODO: insert after / before entry
 * TODO: add insertPosition flag to the insert/updateOrInsert function, the default is LAST
 */

interface IHashCollection
{

    /**
     * @param string $hash
     * @param $data
     * @param bool $ignoreIfExists
     * @return bool
     */
    public function insert(string $hash, $data, bool $ignoreIfExists = false):bool;

    /**
     * @param string $hash
     * @param $data
     * @param bool $ignoreIfNotExists
     * @return bool
     */
    public function update(string $hash, $data, bool $ignoreIfNotExists = false):bool;

    /**
     * @param string $hash
     * @param $data
     * @return bool
     */
    public function updateOrInsert(string $hash, $data):bool;

    /**
     * @param string $hash
     * @param bool $ignoreIfNotExists
     * @return bool
     */
    public function delete(string $hash, bool $ignoreIfNotExists = false):bool;

    /**
     * @param string|null $hash
     * @return mixed
     */
    public function pullOffStack(?string &$hash = null);

    /**
     * @param string|null $hash
     * @return mixed
     */
    public function pullOffQueue(?string &$hash = null);

    /**
     * @param string $fromHash
     * @param string $toHash
     * @return bool
     */
    public function reHash(string $fromHash, string $toHash):bool;

    /**
     * @param string $hash
     * @return mixed
     */
    public function find(string $hash);

    /**
     * @param string $hash
     * @return bool
     */
    public function isset(string $hash):bool;

    /**
     * @param string|null $hash
     * @return mixed
     */
    public function first(?string &$hash = null);

    /**
     * @return string
     */
    public function firstHash():?string;

    /**
     * @param string|null $hash
     * @return mixed
     */
    public function last(?string &$hash = null);

    /**
     * @return string
     */
    public function lastHash():?string;

    /**
     * @return int
     */
    public function count():int;

    /**
     * @return \Generator
     */
    public function yieldAll();

    /**
     * @return \Generator
     */
    public function reverseYieldAll();

}