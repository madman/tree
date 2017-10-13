<?php

namespace App\Event;

use Tree\Node;

class UpdateNodeEvent {

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $content;

    public function __construct($name, $title, $content)
    {
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
    }

    public static function fromNode(Node $node)
    {
        return new self($node->getName(), $node->getTitle(), $node->getContent());
    }

}