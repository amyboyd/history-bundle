<?php

namespace AmyBoyd\HistoryBundle\Document;

trait HasHistoryTrait
{
    /**
     * @MongoDB\EmbedMany(
     *     targetDocument="AmyBoyd\HistoryBundle\Document\HistoryEvent"
     * )
     */
    private $historyEvents;

    public function addHistoryEvent(HistoryEvent $historyEvent)
    {
        $this->historyEvents[] = $historyEvent;
    }

    public function getHistoryEvents()
    {
        return $this->historyEvents;
    }
}
