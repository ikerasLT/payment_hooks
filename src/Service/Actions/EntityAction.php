<?php

namespace App\Service\Actions;

abstract class EntityAction implements Action
{
    abstract protected function validate(): void;

    abstract protected function getObject();

    abstract protected function fillData($object): void;

    abstract protected function persist($object): void;

    public function handle(): void
    {
        $this->validate();
        $object = $this->getObject();
        $this->fillData($object);
        $this->persist($object);
    }
}
