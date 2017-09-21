<?php

namespace Tree;

use Tree\IdentityMap\NotFoundException;
use Ramsey\Uuid\Uuid;

class IdentityMap {

    protected $map = [];

    public function add(Node $node, Metadata $metadata)
    {
        $id = $node->getId();

        $this->map[$id->toString()] = [
            'node' => $node,
            'metadata' => $metadata,
        ];
    }

    public function has(Uuid $id)
    {
        return array_key_exists($id->toString(), $this->map);
    }

    public function getNode(Uuid $id)
    {
        if (!$this->exists($id->toString())) {
            throw new NotFoundException();
        }

        return $this->map[$id->toString()]['node'];
    }

    public function getMetadata(Uuid $id)
    {
        if (!$this->exists($id->toString())) {
            throw new NotFoundException();
        }

        return $this->map[$id->toString()]['metadata'];
    }

}