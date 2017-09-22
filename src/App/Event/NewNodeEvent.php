<?php

namespace App\Event;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class NewNodeEvent {

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

/*
    static public function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank());
        $metadata->addPropertyConstraint('name', new Assert\Length(array('min' => 3)));
        $metadata->addPropertyConstraint('title', new Assert\NotBlank());
        $metadata->addPropertyConstraint('title', new Assert\Length(array('min' => 3)));
    }*/
}