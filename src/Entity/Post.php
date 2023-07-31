<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Response;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[
    ApiResource(
        operations: [

            new GetCollection(
                filters: ['post.search_filter']
            ),
            new GetCollection(
                uriTemplate: "/posts/count",
                controller: PostCountController::class,
                openapiContext: [
                    'summary' => 'Récupère le nombre total d\'article.',
                    'description' => 'Récupère le nombre total d\'article.',
                    "parameters" => [
                        [
                            "name" => 'online',
                            "description" => "Filtre les articles en ligne",
                            "in" => "query",
                            "schema" => [
                                "type" => "boolean"
                            ]
                        ]
                    ],
                    "responses" => [
                        '200' => [
                            'description' => "Ok",
                            "content" => [
                                'application/json' => [
                                    "schema" => [
                                        "type" => 'integer',
                                        "exemple" => 3
                                    ]
                                ]
                            ]
                        ],
                    ],

                ],
                paginationEnabled: false,
                filters: ['post.boolean_filter'],
                read: false,
                name: "count"
            ),
            new Get(
                normalizationContext: [
                    'groups' => ['get:Post', 'get:Posts'],
                    "openapi_definition_name" => "Detail"
                ]
            ),
            new Put(
                denormalizationContext: [
                    'groups' => ['put:Post']
                ]
            ),
            new \ApiPlatform\Metadata\Post(

                denormalizationContext: [
                    'groups' => ['post:Post']
                ],
                validationContext: [
                    'groups' => ['post:Post']
                ]
            ),
            new \ApiPlatform\Metadata\Post(
                uriTemplate: "/posts/{id}/publish",
                requirements: ['id' => '\d+'],
                controller: PostPublishController::class,
                openapiContext: [
                    'summary' => "Permet de publier un article.",
                    "requestBody" => [
                        'content' => [
                            'application/json' => [
                                "schema" => [
                                    "type" => "object"
                                ]
                            ],
                        ]
                    ]
                ],
                name: "publish"
            )
        ],
        paginationItemsPerPage: 2
    )
]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["get:Posts", "post:Post"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups(["get:Posts", "put:Post", "post:Post"]),
        Length(min: 5, groups: ["post:Post"])
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups(["get:Posts", "post:Post"])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(["get:Post", "post:Post"])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(["get:Post"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'posts')]
    #[
        Groups(["get:Post", "put:Post", "post:Post"]),
        Valid()
    ]
    private ?Category $category = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['get:Post'])]
    private ?bool $online = false;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): static
    {
        $this->online = $online;

        return $this;
    }
}
