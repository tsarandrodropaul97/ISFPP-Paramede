<?php

namespace App\Entity;

use App\Repository\ProfesseurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: ProfesseurRepository::class)]
#[Vich\Uploadable()]
class Professeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $prenoms;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\Column(type: 'string', length: 255)]
    private $cin;

    #[ORM\Column(type: 'string', length: 255)]
    private $tel;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $filename;

    #[ORM\Column(type: 'datetime')]
    private $updated_at;

    #[Vich\UploadableField(mapping: 'professeur_image', fileNameProperty: 'filename')]
    /**
     * 
     * 
     * @var File|null
     */
    private $imageFile;

    #[ORM\OneToMany(mappedBy: 'professeur', targetEntity: UniteEnseignement::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $unitesEnseignements;

    public function __construct()
    {
        $this->unitesEnseignements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): self
    {
        $this->prenoms = $prenoms;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(string $cin): self
    {
        $this->cin = $cin;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getFilename(): ?string
    {
        return $this->filename;
    }

    /**
     * @param null|string $filename
     * @return Professeur
     */
    public function setFilename(?string $filename): Professeur
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return null|File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     * @return Professeur
     */
    public function setImageFile(?File $imageFile): Professeur
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {

            $this->updated_at = new \DateTime('now');
        }
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, UniteEnseignement>
     */
    public function getUnitesEnseignements(): Collection
    {
        return $this->unitesEnseignements;
    }

    public function addUnitesEnseignement(UniteEnseignement $unitesEnseignement): self
    {
        if (!$this->unitesEnseignements->contains($unitesEnseignement)) {
            $this->unitesEnseignements[] = $unitesEnseignement;
            $unitesEnseignement->setProfesseur($this);
        }

        return $this;
    }

    public function removeUnitesEnseignement(UniteEnseignement $unitesEnseignement): self
    {
        if ($this->unitesEnseignements->removeElement($unitesEnseignement)) {
            // set the owning side to null (unless already changed)
            if ($unitesEnseignement->getProfesseur() === $this) {
                $unitesEnseignement->setProfesseur(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom . ' ' . $this->prenoms;
    }
}
