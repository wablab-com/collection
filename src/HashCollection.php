<?php

namespace WabLab\Collection;

use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;
use WabLab\HashedLinkedList\Node;

class HashCollection extends AbstractCollection
{
    /**
     * @var Node
     */
    protected Node $rootHashLinkedList;

    /**
     * @var Node
     */
    protected ?Node $firstNode = null;

    /**
     * @var Node
     */
    protected ?Node $lastNode = null;

    public function __construct() {
        $this->rootHashLinkedList = new Node('root', null);
    }

    public function insert(string $hash, $data, bool $ignoreIfExists = false) {
        if(!$this->rootHashLinkedList->getRight($hash)) {
            $node = new Node($hash, $data);
            $this->rootHashLinkedList->setRight($node);
            if(!$this->firstNode) {
                $this->firstNode = $node;
            }
            if($this->lastNode) {
                Node::chainNodes($this->lastNode, $node);
            }
            $this->lastNode = $node;
            return true;
        } else {
            if(!$ignoreIfExists) {
                throw new HashKeyAlreadyExists();
            }
        }
        return false;
    }

    public function update(string $hash, $data, bool $ignoreIfNotExists = false) {
        if($this->rootHashLinkedList->issetRight($hash)) {
            $this->rootHashLinkedList->getRight($hash)->setPayload($data);
            return true;
        } else {
            if(!$ignoreIfNotExists) {
                throw new HashKeyDoesNotExists();
            }
        }
        return false;
    }

    public function updateOrInsert(string $hash, $data) {
        if($this->rootHashLinkedList->issetRight($hash)) {
            return $this->update($hash, $data);
        } else {
            return $this->insert($hash, $data);
        }
    }

    public function delete(string $hash, bool $ignoreIfNotExists = false) {
        if($this->rootHashLinkedList->issetRight($hash)) {
            $node = $this->rootHashLinkedList->getRight($hash);

            // check first node
            if($this->firstNode && $this->firstNode->getHash() == $node->getHash()) {
                $this->firstNode = $node->firstRight();
            }

            // check last node
            if($this->lastNode && $this->lastNode->getHash() == $node->getHash()) {
                $this->lastNode = $node->firstLeft();
            }
            $this->rootHashLinkedList->unsetRight($node->getHash());
            $node->delete(Node::DELETE_STRATEGY_MERGE);
            return true;
        } else {
            if(!$ignoreIfNotExists) {
                throw new HashKeyDoesNotExists();
            }
        }
        return false;
    }

    public function pullOffStack(&$hash = null) {
        if($this->lastNode) {
            $hash = $this->lastNode->getHash();
            $value = $this->lastNode->getPayload();
            $this->delete($this->lastNode->getHash());
            return $value;
        }
        return null;
    }


    public function pullOffQueue(&$hash = null) {
        if($this->firstNode) {
            $hash = $this->firstNode->getHash();
            $value = $this->firstNode->getPayload();
            $this->delete($this->firstNode->getHash());
            return $value;
        }
        return null;
    }

    public function reHash(string $fromHash, string $toHash) {
        if($this->rootHashLinkedList->issetRight($fromHash)) {
            $node = $this->rootHashLinkedList->getRight($fromHash);
            $this->rootHashLinkedList->unsetRight($fromHash);

            $node->setHash($toHash);
            $this->rootHashLinkedList->setRight($node);
        }
    }

    /**
     * Get row by primary key
     *
     * @param array $condition
     */
    public function find(string $hash) {
        $node = $this->rootHashLinkedList->getRight($hash);
        return $node ? $node->getPayload(): null;
    }

    public function first(&$hash = null) {
        if($this->firstNode) {
            $hash = $this->firstNode->getHash();
            return $this->firstNode->getPayload();
        }
        return null;
    }

    public function firstHash() {
        if($this->firstNode) {
            return $this->firstNode->getHash();
        }
        return null;
    }

    public function last(&$hash = null) {
        if($this->lastNode) {
            $hash = $this->lastNode->getHash();
            return $this->lastNode->getPayload();
        }
        return null;
    }

    public function lastHash() {
        if($this->lastNode) {
            return $this->lastNode->getHash();
        }
        return null;
    }

    public function count() {
        return $this->rootHashLinkedList->countRights();
    }

    /**
     * @return \Generator
     */
    public function yieldAll() {
        $current = $this->firstNode;
        while($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->firstRight();
        }
    }

    /**
     * @return array
     */
    public function all() {
        $toReturn = [];
        foreach($this->yieldAll() as $hash => $value) {
            $toReturn[$hash] = $value;
        }
        return $toReturn;
    }

    /**
     * @return \Generator
     */
    public function reverseYieldAll() {
        $current = $this->lastNode;
        while($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->firstLeft();
        }
    }

    /**
     * @return array
     */
    public function reverseAll() {
        $toReturn = [];
        foreach($this->reverseYieldAll() as $hash => $value) {
            $toReturn[$hash] = $value;
        }
        return $toReturn;
    }


    /**
     * @return string
     */
    public function toJson():string {
        return json_encode($this->all());
    }

    /**
     * @param string $json
     */
    public function fromJson(string $json, $ignoreIfExists = false) {
        $jsonRows = json_decode($json, true);
        foreach($jsonRows as $hash => $data) {
            $this->insert($hash, $data, $ignoreIfExists, true);
        }
    }

    /**
     * @param resource $stream
     */
    public function toJsonStream($stream) {
        fwrite($stream, '[');
        $counter = 0;
        foreach ($this->yieldAll() as $hash => $row) {
            $counter++;
            fwrite($stream, json_encode([$hash => $row]) );
            if($counter < $this->rootHashLinkedList->countRights()) {
                fwrite($stream, ',' );
            }
        }
        fwrite($stream, ']');
    }

}