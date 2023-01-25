<?php

namespace App\Entity;


use App\Repository\BlameRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: BlameRepository::class)]
class Blame
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['blames:read'])]
    private $date;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['blames:read'])]
    #[Assert\NotBlank(message: 'Please enter a reason')]
    private $reason;

    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EAGER', inversedBy: 'blames')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(message: 'Please select an eagle')]
    private $eagle;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getEagle(): ?User
    {
        return $this->eagle;
    }

    public function setEagle(User $eagle): self
    {
        $this->eagle = $eagle;

        return $this;
    }
}
