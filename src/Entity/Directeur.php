<?php

namespace App\Entity;

use App\Repository\DirecteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: DirecteurRepository::class)]
#[Vich\Uploadable()]
class Directeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $motDirecteur;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $filename;

    #[Vich\UploadableField(mapping: 'etudiant_image', fileNameProperty: 'filename')]
    /**
     * 
     * 
     * @var File|null
     */
    private $imageFile;

    #[ORM\Column(type: 'datetime')]
    private $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotDirecteur(): ?string
    {
        return $this->motDirecteur;
    }

    public function setMotDirecteur(string $motDirecteur): self
    {
        $this->motDirecteur = $motDirecteur;

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
     * @return Directeur
     */
    public function setFilename(?string $filename): Directeur
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
     * @return Directeur
     */
    public function setImageFile(?File $imageFile): Directeur
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

}
