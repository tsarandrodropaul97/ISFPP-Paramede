<?php

namespace App\Entity;

use App\Repository\ArchiveNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ArchiveNoteRepository::class)]
class ArchiveNote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $valeur;

    #[ORM\ManyToOne(targetEntity: Etudiant::class, inversedBy: 'archiveNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $etudiant;

    #[ORM\ManyToOne(targetEntity: ArchiveResultat::class, inversedBy: 'archiveNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $resultat;

    #[ORM\ManyToOne(targetEntity: Examen::class, inversedBy: 'archiveNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $examen;

    #[ORM\ManyToOne(targetEntity: UniteEnseignement::class, inversedBy: 'archiveNotes')]
    #[ORM\JoinColumn(nullable: false)]
    private $uniteEnseignement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValeur(): ?float
    {
        return $this->valeur;
    }

    public function setValeur(float $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }

    public function getEtudiant(): ?Etudiant
    {
        return $this->etudiant;
    }

    public function setEtudiant(?Etudiant $etudiant): self
    {
        $this->etudiant = $etudiant;

        return $this;
    }

    public function getResultat(): ?ArchiveResultat
    {
        return $this->resultat;
    }

    public function setResultat(?ArchiveResultat $resultat): self
    {
        $this->resultat = $resultat;

        return $this;
    }

    public function getExamen(): ?Examen
    {
        return $this->examen;
    }

    public function setExamen(?Examen $examen): self
    {
        $this->examen = $examen;

        return $this;
    }

    public function getUniteEnseignement(): ?UniteEnseignement
    {
        return $this->uniteEnseignement;
    }

    public function setUniteEnseignement(?UniteEnseignement $uniteEnseignement): self
    {
        $this->uniteEnseignement = $uniteEnseignement;

        return $this;
    }
}
