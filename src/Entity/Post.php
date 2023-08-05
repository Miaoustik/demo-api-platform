<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Attribute\ApiAuthGroups;
use App\Controller\PostCountController;
use App\Controller\PostPublishController;
use App\Repository\PostRepository;
use App\Security\Voter\UserOwnedVoter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[
    ApiResource(
        operations: [

            new GetCollection(
                normalizationContext: [
                    'groups' => [Post::COLLECTION]
                ],
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
                    'groups' => [Post::DETAIL],

                ]
            ),
            new Patch(
                denormalizationContext: [
                    'groups' => [Post::MODIFY],

                ]
            ),
            new \ApiPlatform\Metadata\Post(

                normalizationContext: [
                    'groups' => [Post::CREATE_RETURN]
                ],
                denormalizationContext: [
                    'groups' => [Post::CREATE]
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
                    ],

                ],
                name: "publish"
            )
        ]
    ),
    ApiAuthGroups([
        UserOwnedVoter::EDIT => Post::USER_COLLECTION
    ])
]
class Post implements UserOwnedInterface
{
    public const DETAIL = 'ReadDetail:Post';
    public const COLLECTION = 'ReadCollection:Post';
    public const USER_COLLECTION = 'User:ReadCollection:Post';
    public const MODIFY = 'Modify:Post';
    public const REPLACE = 'Replace:Post';
    public const DELETE = 'Delete:Post';
    public const CREATE = 'Create:Post';
    public const CREATE_RETURN = 'Create:Return:Post';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::COLLECTION, self::CREATE, self::DETAIL, self::CREATE_RETURN])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[
        Groups([self::COLLECTION, self::MODIFY, self::CREATE, self::DETAIL]),
        Length(min: 5, groups: [self::CREATE])
    ]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Groups([self::USER_COLLECTION, self::CREATE, self::DETAIL])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups([self::DETAIL, self::CREATE])]
    #[NotNull]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups([self::DETAIL])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'posts')]
    #[
        Groups([self::DETAIL, self::MODIFY, self::CREATE]),
        Valid()
    ]
    private ?Category $category = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups([self::DETAIL, self::USER_COLLECTION])]
    private ?bool $online = false;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $author = null;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }
}
