<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

final class ExamInput
{
    #[Assert\NotBlank(groups: ['exam:assert:write'])]
    #[Assert\Length(max: 180, groups: ['exam:assert:write'])]
    #[Groups(['exam:write'])]
    public string $studentName;


    #[Groups(['exam:write'])]
    public string $location;

    #[Assert\NotBlank(groups: ['exam:assert:write'])]
    #[Assert\Choice(choices: ['in_search_place','confirm','in_organised', 'canceled'], groups: ['exam:assert:write'])]
    #[Groups(['exam:write'])]
    public string $status = 'in_organised';

    #[Assert\Type(\DateTime::class, groups: ['exam:assert:write'])]
    // #[Assert\GreaterThan('now', groups: ['exam:assert:write'], message: 'La date doit être future.')]
    #[Groups(['exam:write'])]
    public ?\DateTime $date = null;


    #[Assert\Type(\DateTime::class, groups: ['exam:assert:write'])]
    #[Groups(['exam:write'])]
    public ?\DateTime $time = null;
}
