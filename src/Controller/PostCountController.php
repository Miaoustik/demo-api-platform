<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class PostCountController extends AbstractController
{
    public function __construct(private readonly PostRepository $repository)
    {
    }

    public function __invoke(Request $request): int
    {
        $online = $request->query->get('online');
        $conditions = [];

        if ($online !== null) {
            $conditions = ['online' => $online];
        }

        return $this->repository->count($conditions);
    }
}