<?php

namespace Tree;

use Ramsey\Uuid\Uuid;

class Node implements \JsonSerializable {

    /**
     * @var Uuid
     */
    protected $id;
    protected $name;
    protected $title;
    protected $content;

    public function __construct(Uuid $id, $name, $title, $content)
    {
        $this->id = $id;
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function jsonSerialize() {
        return [
            'id'        => $this->id->toString(),
            'name'      => $this->name,
            'title'     => $this->title,
            'content'   => $this->content,
        ];
    }

    /**
     * @param  string $name
     * @param  string $title
     * @param  string $content
     * @return Node
     */
    public static function create($name, $title, $content = '')
    {
        return new static(Uuid::uuid4(), $name, $title, $content);
    }

}