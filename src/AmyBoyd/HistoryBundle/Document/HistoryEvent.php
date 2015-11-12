<?php

namespace AmyBoyd\HistoryBundle\Document;

use AmyBoyd\HistoryBundle\Enum\HistoryEventType;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\EmbeddedDocument
 */
class HistoryEvent implements \JsonSerializable
{
    /**
     * @MongoDB\Boolean
     */
    private $isAddedByListener;

    /**
     * @MongoDB\String
     */
    private $type;

    /**
     * @MongoDB\String
     */
    private $title;

    /**
     * @MongoDB\String
     */
    private $info;

    /**
     * @MongoDB\Date
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'info' => $this->getInfo(),
            'date' => date_format($this->getDate(), 'c'),
        ];
    }

    public function isAddedByListener()
    {
        return $this->isAddedByListener;
    }

    public function setIsAddedByListener($isAddedByListener)
    {
        $this->isAddedByListener = $isAddedByListener;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        HistoryEventType::validateValue($type);

        $this->type = $type;

        return $this;
    }

    public function getInfo()
    {
        return $this->info;
    }

    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
