<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Put;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            controller: NotFoundAction::class,
            openapiContext: [
                'summary' => "hidden"
            ],
            output: false,
            read: false
        ),
        new GetCollection(),
        new \ApiPlatform\Metadata\Post(),
        new Patch(),
        new Put(),
        new Delete()
    ]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['get:Post'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(['get:Post', 'post:Post']),
        Length(min: 3, groups: ['post:Post'])
    ]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Post::class)]
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }
}
