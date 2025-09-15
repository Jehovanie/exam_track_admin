<?php  

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ExamInput;
use App\Entity\Exam;
use Doctrine\ORM\EntityManagerInterface;

final class ExamProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    /** @param ExamInput $data */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var Exam|null $exam */
        $exam = $context['previous_data'] ?? null;
        $isCreate = !$exam;

        if ($isCreate) {
            $exam = new Exam();
        }

        $exam->setStudentName($data->studentName);
        $exam->setLocation($data->location);
        $exam->setDate($data->date);
        $exam->setTime($data->time);
        $exam->setStatus($data->status);


        $this->em->persist($exam);
        $this->em->flush();

        return $exam; // sera normalisé avec tes groupes 'exam:lists'
    }
}
