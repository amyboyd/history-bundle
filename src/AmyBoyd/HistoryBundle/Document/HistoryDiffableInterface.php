<?php

namespace AmyBoyd\HistoryBundle\Document;

interface HistoryDiffableInterface
{
    /**
     * @return string A string to represent the object's value in the history diff.
     */
    public function getHistoryDiffRepresentation();
}
