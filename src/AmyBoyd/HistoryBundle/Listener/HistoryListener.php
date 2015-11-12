<?php

namespace AmyBoyd\HistoryBundle\Listener;

use AmyBoyd\HistoryBundle\Document\HistoryDiffableInterface;
use AmyBoyd\HistoryBundle\Document\HistoryEvent;
use AmyBoyd\HistoryBundle\Enum\HistoryEventType;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;

class HistoryListener
{
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $dm = $eventArgs->getDocumentManager();
        $unitOfWork = $dm->getUnitOfWork();

        foreach ($unitOfWork->getScheduledDocumentUpdates() as $document) {
            if (!method_exists($document, 'addHistoryEvent')) {
                continue;
            }

            $historyRecord = $this->createEvent();
            $historyRecord->setType(HistoryEventType::RECORD_UPDATED);
            $historyRecord->setTitle('Record updated');

            $changeSet = $unitOfWork->getDocumentChangeSet($document);
            $historyRecord->setInfo(substr($this->getDetailedUpdateInfo($changeSet), 0, 100000));

            $document->addHistoryEvent($historyRecord);
            $dm->persist($historyRecord);

            // Prevent a recursion of the history events change becoming another unit of work to
            // record as history again and again.
            $unitOfWork->recomputeSingleDocumentChangeSet($dm->getClassMetadata(get_class($document)), $document);
        }
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $document = $eventArgs->getDocument();

        if (!method_exists($document, 'addHistoryEvent')) {
            return;
        }

        $historyRecord = $this->createEvent();
        $historyRecord->setType(HistoryEventType::RECORD_CREATED);
        $historyRecord->setTitle('Record created');

        $originalValues = $eventArgs->getDocumentManager()->getUnitOfWork()->getDocumentActualData($document);
        $historyRecord->setInfo(substr($this->getDetailedCreateInfo($originalValues), 0, 100000));

        $document->addHistoryEvent($historyRecord);
        $eventArgs->getDocumentManager()->persist($historyRecord);
    }

    private function createEvent()
    {
        $event = new HistoryEvent();

        return $event;
    }

    private function getDetailedCreateInfo(array $originalValues)
    {
        unset($originalValues['id'], $originalValues['historyEvents']);

        $updateInfo = "Original values:\n";

        foreach ($originalValues as $key => $value) {
            $keyHumanized = $this->humanizeKey($key);
            $valueRepresentation = $this->getStringRepresentation($value);

            $updateInfo .= "$keyHumanized: '$valueRepresentation'\n";
        }

        return $updateInfo;
    }

    private function getDetailedUpdateInfo(array $changeSet)
    {
        $updateInfo = "Changed values:\n";

        foreach ($changeSet as $key => $values) {
            $keyHumanized = $this->humanizeKey($key);
            $oldValueRepresentation = $this->getStringRepresentation($values[0]);
            $newValueRepresentation = $this->getStringRepresentation($values[1]);

            $updateInfo .= "$keyHumanized: from: '$oldValueRepresentation', to: '$newValueRepresentation'\n";
        }

        return $updateInfo;
    }

    private function humanizeKey($key)
    {
        $keyHumanized = preg_replace('/([A-Z])/', ' $1', $key);
        $keyHumanized = strtolower($keyHumanized);
        $keyHumanized = ucfirst($keyHumanized);

        return $keyHumanized;
    }

    private function getStringRepresentation($value)
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif (is_scalar($value)) {
            return $value;
        } elseif (is_null($value)) {
            return 'null';
        } elseif ($value instanceof HistoryDiffableInterface) {
            return $value->getHistoryDiffRepresentation();
        } elseif ($value instanceof \DateTime) {
            return $value->format('Y-m-d H:iO');
        } else {
            return '(cannot be displayed)';
        }
    }
}
