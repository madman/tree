<?php

namespace Tree;

use Tree\Exception\TreeException;
use Tree\Exception\NotFoundException;
use Tree\Persistence\PersistenceInterface;
use Ramsey\Uuid\Uuid;

class TreeService {

    /**
     * @var IdentityMap
     */
    protected $map;

    /**
     * @var PersistenceInterface
     */
    protected $storage;

    public function __construct(PersistenceInterface $storage)
    {
        $this->map =  new IdentityMap();
        $this->storage = $storage;
    }

    public function create(Node $root)
    {
        // TODO: validation
        $this->storage->createRoot($root->getId(), $root->getName(), $root->getTitle(), $root->getContent());
        $metadata = new Metadata(1,2,1);

        $this->map->add($root, $metadata);
    }

    /**
     * @return Node
     */
    public function getRoot()
    {
        $raw = $this->storage->getRoot();

        $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
        $root = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

        $this->map->add($root, $metadata);

        return $root;
    }

    public function findById(Uuid $id)
    {
        if ($this->map->has($id)) {
            $node = $this->map->getNode($id);
        } else {
            $raw = $this->storage->findById($id->toString());

            $metadata = new Metadata($raw['left'], $raw['right'], $raw['level']);
            $node = new Node(Uuid::fromString($raw['id']), $raw['name'], $raw['title'], $raw['content']);

            $this->map->add($node, $metadata);
        }

        return $node;
    }



}
