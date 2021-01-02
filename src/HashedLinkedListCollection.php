<?php

namespace WabLab\Collection;

use WabLab\Collection\Contracts\IHashCollection;
use WabLab\Collection\Exception\HashKeyAlreadyExists;
use WabLab\Collection\Exception\HashKeyDoesNotExists;
use WabLab\HashedLinkedList\Node;

class HashedLinkedListCollection implements IHashCollection
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

    public function insert(string $hash, $data, bool $ignoreIfExists = false):bool {
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

    public function update(string $hash, $data, bool $ignoreIfNotExists = false):bool {
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

    public function updateOrInsert(string $hash, $data):bool {
        if($this->rootHashLinkedList->issetRight($hash)) {
            return $this->update($hash, $data);
        } else {
            return $this->insert($hash, $data);
        }
    }

    public function delete(string $hash, bool $ignoreIfNotExists = false):bool {
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

    public function pullOffStack(?string &$hash = null) {
        if($this->lastNode) {
            $hash = $this->lastNode->getHash();
            $value = $this->lastNode->getPayload();
            $this->delete($this->lastNode->getHash());
            return $value;
        }
        return null;
    }

    public function pullOffQueue(?string &$hash = null) {
        if($this->firstNode) {
            $hash = $this->firstNode->getHash();
            $value = $this->firstNode->getPayload();
            $this->delete($this->firstNode->getHash());
            return $value;
        }
        return null;
    }

    public function reHash(string $fromHash, string $toHash):bool {
        if($this->rootHashLinkedList->issetRight($fromHash)) {
            $node = $this->rootHashLinkedList->getRight($fromHash);
            $this->rootHashLinkedList->unsetRight($fromHash);

            $node->setHash($toHash);
            $this->rootHashLinkedList->setRight($node);
            return true;
        }
        return false;
    }


    public function find(string $hash) {
        $node = $this->rootHashLinkedList->getRight($hash);
        return $node ? $node->getPayload(): null;
    }

    public function isset(string $hash): bool
    {
        return $this->rootHashLinkedList->issetRight($hash);
    }

    public function first(?string &$hash = null) {
        if($this->firstNode) {
            $hash = $this->firstNode->getHash();
            return $this->firstNode->getPayload();
        }
        return null;
    }

    public function firstHash():?string {
        if($this->firstNode) {
            return $this->firstNode->getHash();
        }
        return null;
    }

    public function last(?string &$hash = null) {
        if($this->lastNode) {
            $hash = $this->lastNode->getHash();
            return $this->lastNode->getPayload();
        }
        return null;
    }

    public function lastHash() : ?string {
        if($this->lastNode) {
            return $this->lastNode->getHash();
        }
        return null;
    }

    public function count(): int {
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
     * @return \Generator
     */
    public function reverseYieldAll() {
        $current = $this->lastNode;
        while($current) {
            yield $current->getHash() => $current->getPayload();
            $current = $current->firstLeft();
        }
    }

}