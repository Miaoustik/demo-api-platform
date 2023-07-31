<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\DependencyRepository;

class DependencyProcessor implements ProcessorInterface
{

    public function __construct(private readonly DependencyRepository $repository)
    {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // TODO: Implement process() method.

        if ($operation instanceof DeleteOperationInterface) {
            $this->repository->remove($data);
            return null;
        }

        $this->repository->persist($data);
        return $data;

        //dd($data, $operation, $uriVariables, $context);
    }
}