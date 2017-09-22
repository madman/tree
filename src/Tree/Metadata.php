<?php

namespace Tree;

class Metadata {

    protected $left;
    protected $right;
    protected $level;

    public function __construct($left, $right, $level)
    {
        $this->left = $left;
        $this->right = $right;
        $this->level = $level;
    } 

    public function getLeft()
    {
        return $this->left;
    }

    public function getRight()
    {
        return $this->right;
    }

    public function getLevel()
    {
        return $this->level;
    }

    public static function fromArray($raw)
    {
        return new static($raw['left'], $raw['right'], $raw['level']);
    }

    public function isRoot()
    {
        return 1 == $this->left;
    }
}