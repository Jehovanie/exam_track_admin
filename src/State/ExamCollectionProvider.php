<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Exam;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

final class ExamCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $page  = max(1, (int)($context['filters']['page'] ?? 1));
        $itemsPerPage = (int)($context['filters']['itemsPerPage'] ?? 10);

        $qb = $this->em->getRepository(Exam::class)->createQueryBuilder('e');

        // Pagination
        $qb->setFirstResult(($page - 1) * $itemsPerPage)
           ->setMaxResults($itemsPerPage);

        // Paginator Doctrine (compte total filtré)
        $doctrinePaginator = new DoctrinePaginator($qb->getQuery(), true);
        $total = count($doctrinePaginator);       // total filtré
        $items = iterator_to_array($doctrinePaginator); // page courante

        return [
            'total' => $total,
            'items' => $items,
            'page'  => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
    }
}
