<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use App\State\DependencyProcessor;
use App\State\DependencyProvider;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    operations: [

        new GetCollection(
            paginationEnabled: false
        ),
        new \ApiPlatform\Metadata\Post(),
        new Put(
            denormalizationContext: [
                'groups' => ['put:Dependency']
            ]
        ),
        new Delete(),
        new Get()
    ],
    provider: DependencyProvider::class,
    processor: DependencyProcessor::class
)]
class Dependency
{
    #[ApiProperty(
        identifier: true
    )]
    private string $uuid;

    #[
        ApiProperty(
            description: "Nom de la dépendance."
        ),
        Length(min: 2),
        NotBlank
    ]
    private string $name;

    #[
        ApiProperty(
            description: "Version de la dépendance",
            openapiContext: [
                "example" => "5.4.*"
            ]
        ),
        Length(min: 2),
        NotBlank,
        Groups(['put:Dependency'])
    ]
    private string $version;

    /**
     * @param string $name
     * @param string $version
     */
    public function __construct(string $name, string $version)
    {
        $this->uuid = Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }


}
