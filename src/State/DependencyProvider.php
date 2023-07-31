<?php

namespace App\State;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Dependency;
use App\Repository\DependencyRepository;

class DependencyProvider implements ProviderInterface
{

    public function __construct(private readonly DependencyRepository $repository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): Dependency|array|null
    {

        if ($operation instanceof CollectionOperationInterface) {

            return $this->repository->findAll();
        }

        return $this->repository->findByUuid($uriVariables['uuid']);
    }
}