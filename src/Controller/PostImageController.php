<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostImageController
{
    public function __invoke(Post $post, Request $request)
    {
        $post->setFile($request->files->get('image'));
        $post->setUpdatedAt(new \DateTimeImmutable());
        return $post;
    }
}