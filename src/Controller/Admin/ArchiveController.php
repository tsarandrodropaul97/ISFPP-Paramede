<?php

namespace App\Controller\Admin;

use App\Repository\NoteRepository;
use App\Repository\NormeRepository;
use App\Repository\ExamenRepository;
use App\Repository\NiveauRepository;
use App\Repository\EtudiantRepository;
use App\Repository\ResultatRepository;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArchiveNoteRepository;
use App\Repository\ArchiveResultatRepository;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\ListeDesEtudiantsRepository;
use App\Repository\UniteEnseignementRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnneeUniversitaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/archive')]
class ArchiveController extends AbstractController
{
    private $repoManager;
    private $repoExamen;
    private $repoNiveau;
    private $repoAnneeUniversitaire;
    private $repoEtudiant;
    private $repoSemestre;
    private $repoArchiveResultat;
    private $repoUnite;
    private $repoNote;
    private $repoNorme;
    private $repoListe;
    private $repoArchiveNote;

    public function __construct(
        EntityManagerInterface $repoManager,
        ArchiveResultatRepository $repoArchiveResultat,
        NiveauRepository $repoNiveau,
        ExamenRepository $repoExamen,
        UniteEnseignementRepository $repoUnite,
        SemestreRepository $repoSemestre,
        NoteRepository $repoNote,
        NormeRepository $repoNorme,
        ListeDesEtudiantsRepository $repoListe,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        ArchiveNoteRepository $repoArchiveNote,
        EtudiantRepository $repoEtudiant
    ) {
        $this->repoArchiveResultat = $repoArchiveResultat;
        $this->repoManager = $repoManager;
        $this->repoEtudiant = $repoEtudiant;
        $this->repoNiveau = $repoNiveau;
        $this->repoExamen = $repoExamen;
        $this->repoSemestre = $repoSemestre;
        $this->repoUnite = $repoUnite;
        $this->repoNote = $repoNote;
        $this->repoNorme = $repoNorme;
        $this->repoListe = $repoListe;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoArchiveNote = $repoArchiveNote;
    }
    #[Route('/{idNiveau}/{idAnneeUniversitaire}', name: 'app_archive_admin', requirements: ['idAnneeUniversitaire' => '\d+', 'idNiveau' => '\d+'])]
    public function index($idNiveau, $idAnneeUniversitaire): Response
    {
        /* information renvoyé au menu */
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $anneeUniversitaire = $this->repoAnneeUniversitaire->findOneBy(['id' => $idAnneeUniversitaire]);
        
        $etudiants = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau
        ]);
        $examens = $this->repoExamen->findBy([
            'anneeUniversitaire' => $idAnneeUniversitaire
        ]);
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $idNiveau
        ]);

        return $this->render('Admin/archive/index.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'examens' => $examens,
            'semestres' => $semestres,
            'anneeUniversitaire' => $anneeUniversitaire,
            'anneeUniversitaires' => $anneeUniversitaires,
        ]);

    }

    #[Route('/{idEtudiant}/{idAnneeUniversitaire}/afficher', name: 'app_archive_afficher_admin', methods: ['GET', 'POST'], requirements: ['idEtudiant' => '\d+', 'idAnneeUniversitaire' => '\d+'])]
    public function afficher(int $idEtudiant, $idAnneeUniversitaire): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $anneeUniversitaire = $this->repoAnneeUniversitaire->findOneBy(['id' => $idAnneeUniversitaire]);
        $norme = $this->repoNorme->findAll()[0];
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $etudiant = $this->repoEtudiant->findOneBy([
            'id' => $idEtudiant
        ]);

        $archiveNotes = $this->repoArchiveNote->findBy([
            'etudiant' => $idEtudiant
        ]);
        $archiveResultats = $this->repoArchiveResultat->findBy([
            'anneeUniversitaire' => $anneeUniversitaire->getId(),
            'niveau' => $etudiant->getNiveau()->getId()
        ]);
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $etudiant->getNiveau()->getId()
        ]);
        $examens = $this->repoExamen->findBy([
            'anneeUniversitaire' => $anneeUniversitaire->getId(),
        ]);


        return $this->render('Admin/archive/afficher.html.twig', [
            'archiveResultats' => $archiveResultats,
            'niveauxMenu' => $niveauxMenu,
            'etudiant' => $etudiant,
            'archiveNotes' => $archiveNotes,
            'examens' => $examens,
            'anneeUniversitaires' => $anneeUniversitaires,
            'semestres' => $semestres,
            'norme' => $norme,
        ]);
    }
}
