<?php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Dto\ExamStatsOutput;
use App\Entity\Exam;
use Doctrine\ORM\EntityManagerInterface;

final class ExamStatsProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ExamStatsOutput
    {
        $rows = $this->em->createQueryBuilder()
            ->select('e.status AS status, COUNT(e.id) AS cnt')
            ->from(Exam::class, 'e')
            ->groupBy('e.status')
            ->getQuery()
            ->getArrayResult();

        $out = new ExamStatsOutput();
        foreach ($rows as $r) {
            $out->byStatus[$r['status']] = (int) $r['cnt'];
            $out->total += (int) $r['cnt'];
        }

        // Assure une clé présente même si 0 (optionnel)
        foreach (['in_search_place','confirm','in_organised','canceled'] as $status) {
            $out->byStatus[$status] = $out->byStatus[$status] ?? 0;
        }

        ksort($out->byStatus);

        return $out;
    }
}
