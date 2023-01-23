<?php

namespace App\Entity;

use App\Repository\EtudiantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: EtudiantRepository::class)]
#[Vich\Uploadable()]
class Etudiant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $matricule;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $prenom;

    #[ORM\Column(type: 'date')]
    private $dateNaissance;

    #[ORM\Column(type: 'string', length: 255)]
    private $lieuNaissace;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\Column(type: 'bigint')]
    #[Assert\Length(
        min: 12,
        max: 12,
        minMessage: 'Le numero de CIN est 12 Chiffres',
        maxMessage: 'Le numero de CIN est 12 Chiffres',
    )]
    private $cin;

    #[ORM\Column(type: 'string', length: 255)]
    private $fait;

    #[ORM\Column(type: 'string', length: 255)]
    private $situationMatrimoniale;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'date')]
    private $delivre;

    #[ORM\Column(type: 'date')]
    private $createdAt;

    #[ORM\Column(type: 'boolean')]
    private $releveNoteBacc = false;

    #[ORM\Column(type: 'boolean')]
    private $bNaissance = false;

    #[ORM\Column(type: 'boolean')]
    private $pIdentite = false;

    #[ORM\Column(type: 'string', length: 255)]
    private $serie;

    #[ORM\Column(type: 'string', length: 255)]
    private $numeroInscriptionBacc;

    #[ORM\Column(type: 'string', length: 255)]
    private $mention;

    #[ORM\Column(type: 'string', length: 255)]
    private $centreBacc;

    #[ORM\Column(type: 'integer')]
    private $anneeBacc;

    #[ORM\Column(type: 'string', length: 255)]
    private $etablissement;

    #[ORM\ManyToOne(targetEntity: Niveau::class, inversedBy: 'etudiants')]
    private $niveau;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private $filename;

    #[ORM\Column(type: 'datetime')]
    private $updated_at;



    #[Vich\UploadableField(mapping: 'etudiant_image', fileNameProperty: 'filename')]
    /**
     * 
     * 
     * @var File|null
     */
    private $imageFile;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Paiement::class)]
    private $paiements;

    #[ORM\ManyToOne(targetEntity: Frais::class, inversedBy: 'etudiants')]
    private $frais;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Note::class)]
    private $notes;

    #[ORM\ManyToOne(targetEntity: ListeDesEtudiants::class, inversedBy: 'etudiant')]
    private $listeDesEtudiants;

    #[ORM\Column(type: 'boolean')]
    private $statut;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: Archive::class)]
    private $archives;

    #[ORM\OneToMany(mappedBy: 'etudiant', targetEntity: ArchiveNote::class)]
    private $archiveNotes;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->paiements = new ArrayCollection();
        $this->notes = new ArrayCollection();
        $this->archives = new ArrayCollection();
        $this->archiveNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatricule(): ?string
    {
        return $this->matricule;
    }

    public function setMatricule(string $matricule): self
    {
        $this->matricule = $matricule;

        return $this;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getLieuNaissace(): ?string
    {
        return $this->lieuNaissace;
    }

    public function setLieuNaissace(string $lieuNaissace): self
    {
        $this->lieuNaissace = $lieuNaissace;

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


    public function getFait(): ?string
    {
        return $this->fait;
    }

    public function setFait(string $fait): self
    {
        $this->fait = $fait;

        return $this;
    }

    public function getSituationMatrimoniale(): ?string
    {
        return $this->situationMatrimoniale;
    }

    public function setSituationMatrimoniale(string $situationMatrimoniale): self
    {
        $this->situationMatrimoniale = $situationMatrimoniale;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDelivre(): ?\DateTimeInterface
    {
        return $this->delivre;
    }

    public function setDelivre(\DateTimeInterface $delivre): self
    {
        $this->delivre = $delivre;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function isReleveNoteBacc(): ?bool
    {
        return $this->releveNoteBacc;
    }

    public function setReleveNoteBacc(bool $releveNoteBacc): self
    {
        $this->releveNoteBacc = $releveNoteBacc;

        return $this;
    }

    public function isBNaissance(): ?bool
    {
        return $this->bNaissance;
    }

    public function setBNaissance(bool $bNaissance): self
    {
        $this->bNaissance = $bNaissance;

        return $this;
    }

    public function isPIdentite(): ?bool
    {
        return $this->pIdentite;
    }

    public function setPIdentite(bool $pIdentite): self
    {
        $this->pIdentite = $pIdentite;

        return $this;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function setSerie(string $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getNumeroInscriptionBacc(): ?string
    {
        return $this->numeroInscriptionBacc;
    }

    public function setNumeroInscriptionBacc(string $numeroInscriptionBacc): self
    {
        $this->numeroInscriptionBacc = $numeroInscriptionBacc;

        return $this;
    }

    public function getMention(): ?string
    {
        return $this->mention;
    }

    public function setMention(string $mention): self
    {
        $this->mention = $mention;

        return $this;
    }

    public function getCentreBacc(): ?string
    {
        return $this->centreBacc;
    }

    public function setCentreBacc(string $centreBacc): self
    {
        $this->centreBacc = $centreBacc;

        return $this;
    }

    public function getAnneeBacc(): ?int
    {
        return $this->anneeBacc;
    }

    public function setAnneeBacc(int $anneeBacc): self
    {
        $this->anneeBacc = $anneeBacc;

        return $this;
    }

    public function getEtablissement(): ?string
    {
        return $this->etablissement;
    }

    public function setEtablissement(string $etablissement): self
    {
        $this->etablissement = $etablissement;

        return $this;
    }

    public function getNiveau(): ?Niveau
    {
        return $this->niveau;
    }

    public function setNiveau(?Niveau $niveau): self
    {
        $this->niveau = $niveau;

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
     * @return Etudiant
     */
    public function setFilename(?string $filename): Etudiant
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
     * @return Etudiant
     */
    public function setImageFile(?File $imageFile): Etudiant
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
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setEtudiant($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getEtudiant() === $this) {
                $paiement->setEtudiant(null);
            }
        }

        return $this;
    }

    public function getFrais(): ?Frais
    {
        return $this->frais;
    }

    public function setFrais(?Frais $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setEtudiant($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getEtudiant() === $this) {
                $note->setEtudiant(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->nom . ' ' . $this->prenom;
    }

    public function getListeDesEtudiants(): ?ListeDesEtudiants
    {
        return $this->listeDesEtudiants;
    }

    public function setListeDesEtudiants(?ListeDesEtudiants $listeDesEtudiants): self
    {
        $this->listeDesEtudiants = $listeDesEtudiants;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Archive>
     */
    public function getArchives(): Collection
    {
        return $this->archives;
    }

    public function addArchive(Archive $archive): self
    {
        if (!$this->archives->contains($archive)) {
            $this->archives[] = $archive;
            $archive->setEtudiant($this);
        }

        return $this;
    }

    public function removeArchive(Archive $archive): self
    {
        if ($this->archives->removeElement($archive)) {
            // set the owning side to null (unless already changed)
            if ($archive->getEtudiant() === $this) {
                $archive->setEtudiant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ArchiveNote>
     */
    public function getArchiveNotes(): Collection
    {
        return $this->archiveNotes;
    }

    public function addArchiveNote(ArchiveNote $archiveNote): self
    {
        if (!$this->archiveNotes->contains($archiveNote)) {
            $this->archiveNotes[] = $archiveNote;
            $archiveNote->setEtudiant($this);
        }

        return $this;
    }

    public function removeArchiveNote(ArchiveNote $archiveNote): self
    {
        if ($this->archiveNotes->removeElement($archiveNote)) {
            // set the owning side to null (unless already changed)
            if ($archiveNote->getEtudiant() === $this) {
                $archiveNote->setEtudiant(null);
            }
        }

        return $this;
    }
}
