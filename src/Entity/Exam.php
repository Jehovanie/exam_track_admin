<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\ExamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\OpenApi\Model as OA;
use Doctrine\ORM\Event\PreUpdateEventArgs;

#[ORM\Entity(repositoryClass: ExamRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    stateless : true,
    operations : [
        new GetCollection(
            uriTemplate: "/exams",
            normalizationContext: [
                "groups" => ['exam:lists'],
            ],
            paginationEnabled: true,
            paginationItemsPerPage : 10,
            paginationClientItemsPerPage:true,
            paginationMaximumItemsPerPage: 20,
            provider: \App\State\ExamCollectionProvider::class,
            openapi: new OA\Operation(
                summary: 'Liste paginée des examens',
                responses: [
                    '200' => new OA\Response(
                        description: 'OK',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['total','items'],
                                    'properties' => [
                                        'total'        => ['type' => 'integer', 'example' => 50],
                                        'page'         => ['type' => 'integer', 'example' => 1],
                                        'itemsPerPage' => ['type' => 'integer', 'example' => 10],
                                        'items'        => [
                                            'type'  => 'array',
                                            'items' => ['$ref' => '#/components/schemas/Exam-exam.lists']
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ]
            )
        ),
        new Post(
            uriTemplate: "/exams",
            // security: "is_granted('ROLE_USER')",
            input: \App\Dto\ExamInput::class,
            processor: \App\State\ExamProcessor::class,
            denormalizationContext: [ 
                'groups' => ['exam:write'],
                'allow_extra_attributes' => false,
                'collect_denormalization_errors' => true
            ],
            validationContext: ['groups' => ['exam:assert:write']],
            inputFormats: ['json' => ['application/json']],
        ),
        new Get(
            uriTemplate: '/exams/stats',
            provider: \App\State\ExamStatsProvider::class,
            output: \App\Dto\ExamStatsOutput::class,
            normalizationContext: ['groups' => ['exam:stats']],
            openapi: new OA\Operation(
                summary: 'Statistiques des examens par statut',
                responses: [
                    '200' => new OA\Response(
                        description: 'OK',
                        content: new \ArrayObject([
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'total' => ['type' => 'integer', 'example' => 42],
                                        'byStatus' => [
                                            'type' => 'object',
                                            'additionalProperties' => ['type' => 'integer'],
                                            'example' => [
                                                'in_search_place' => 10,
                                                'confirm'         => 8,
                                                'in_organised'    => 20,
                                                'canceled'        => 4,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ])
                    ),
                ]
            )
        ),
    ],
    formats : [
        "json" => [ 'application/json'],
    ]
)]
class Exam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['exam:lists'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['exam:lists', 'exam:write'])]
    private ?string $studentName = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Groups(['exam:lists', 'exam:write'])]
    private ?string $location = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['exam:lists', 'exam:write'])]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['exam:lists', 'exam:write'])]
    private ?\DateTime $time = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(length: 30)]
    #[Groups(['exam:lists', 'exam:write'])]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentName(): ?string
    {
        return $this->studentName;
    }

    public function setStudentName(string $studentName): static
    {
        $this->studentName = $studentName;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(\DateTime $time): static
    {
        $this->time = $time;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(PreUpdateEventArgs $args): void
    {
        $this->updatedAt = new \DateTimeImmutable();

        $args->setNewValue('updatedAt', $this->updatedAt);
    }

}
