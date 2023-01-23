<?php

namespace App\Entity;

use App\Repository\NormeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NormeRepository::class)]
class Norme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $moyenneAdmis;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoyenneAdmis(): ?float
    {
        return $this->moyenneAdmis;
    }

    public function setMoyenneAdmis(float $moyenneAdmis): self
    {
        $this->moyenneAdmis = $moyenneAdmis;

        return $this;
    }
}
