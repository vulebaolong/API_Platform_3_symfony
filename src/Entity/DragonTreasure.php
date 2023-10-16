<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\PropertyFilter;
use App\Repository\DragonTreasureRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Validate;
use function Symfony\Component\String\u;

#[ORM\Entity(repositoryClass: DragonTreasureRepository::class)]
#[ApiResource(
    shortName: 'Treasure',
    description: 'A rare and valuable treasure.',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
    ],
    formats: [
        'json',
        'jsonld',
        'jsonopenapi' => 'application/vnd.openapi+json',
        'html' => 'text/html',
        'csv' => 'text/csv'
    ],
    normalizationContext: [
        'groups' => ['treasure:read'],
    ],
    denormalizationContext: [
        'groups' => ['treasure:write'],
    ],
    paginationItemsPerPage: 10
)]
#[ApiFilter(PropertyFilter::class)]
class DragonTreasure
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * hello hello hello hello hello hello hello
     */
    #[ORM\Column(length: 255)]
    #[SerializedName('okokok')]
    #[Groups(['treasure:read', 'treasure:write','user:read'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')] //exact
    #[Validate\NotBlank]
    #[Validate\Length(min: 2, max: 50, maxMessage: 'Describe your loot in 50 chars or less')]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[ApiFilter(RangeFilter::class)]
    #[Validate\NotBlank]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write', 'user:read'])]
    #[Validate\GreaterThanOrEqual(value: 0, message: 'Must greater then 0')]
    private ?int $value = 0;

    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[Validate\GreaterThanOrEqual(value: 0)]
    #[Validate\LessThanOrEqual(value: 10, message: 'Must less then 10')]
    private ?int $coolFator = 0;

    #[ORM\Column]
    #[Groups(['treasure:read'])]
    private \DateTimeImmutable $plunderedAt;

    #[ORM\Column]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[ApiFilter(BooleanFilter::class)]
    private ?bool $isPublished = null;

    #[ORM\ManyToOne(inversedBy: 'dragonTreasures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['treasure:read', 'treasure:write'])]
    #[Assert\Valid]
    private ?User $owner = null;

    public function __construct()
    {
        $this->plunderedAt = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    #[Groups(['treasure:read'])]
    public function getShortDescription(): ?string
    {
        return u($this->description)->truncate(50, '...');
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getCoolFator(): ?int
    {
        return $this->coolFator;
    }

    public function setCoolFator(int $coolFator): static
    {
        $this->coolFator = $coolFator;

        return $this;
    }

    public function getPlunderedAt(): \DateTimeImmutable
    {
        return $this->plunderedAt;
    }

    public function setPlunderedAt(\DateTimeImmutable $plunderedAt): self
    {
        $this->plunderedAt = $plunderedAt;

        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * A human-readable representation of when this treasure was plundered.
     */
    #[Groups(['treasure:read'])]
    #[SerializedName('getPlunderedAtAgoRename')]
    public function getPlunderedAtAgo(): string
    {
        return Carbon::instance($this->plunderedAt)->diffForHumans();
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}

