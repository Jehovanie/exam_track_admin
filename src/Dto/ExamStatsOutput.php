<?php

// src/Dto/ExamStatsOutput.php
namespace App\Dto;

use Symfony\Component\Serializer\Annotation\Groups;

final class ExamStatsOutput
{
    #[Groups(['exam:stats'])]
    public int $total = 0;

    /** @var array<string,int> */
    #[Groups(['exam:stats'])]
    public array $byStatus = [];
}
