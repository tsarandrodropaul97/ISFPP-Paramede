<?php

namespace App\Controller\Admin;

use App\Entity\Note;
use App\Entity\Examen;
use App\Entity\Niveau;
use App\Entity\Resultat;
use App\Form\ResultatType;
use App\Entity\ArchiveNote;
use App\Entity\ArchiveResultat;
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
use Symfony\Component\HttpFoundation\Request;
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
#[Route('/admin/resultat')]
class ResultatController extends AbstractController
{
    private $repoManager;
    private $repoResultat;
    private $repoNiveau;
    private $repoEtudiant;
    private $repoExamen;
    private $repoSemestre;
    private $repoUnite;
    private $repoNote;
    private $repoNorme;
    private $repoListe;
    private $repoAnneeUniversitaire;
    private $repoArchiveNote;

    public function __construct(
        EntityManagerInterface $repoManager,
        ResultatRepository $repoResultat,
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
        $this->repoResultat = $repoResultat;
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

    #[Route('/{idNiveau}', name: 'app_resultat_admin', methods: ['GET', 'POST'], requirements: ['idNiveau' => '\d+'])]
    public function index(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));

        $examens = null;
        $auNull = false;
        if ($au) {
            $examens = $this->repoExamen->findBy([
                'anneeUniversitaire' => $au->getId()
            ]);
        }
        $listeEtudiantsNonAdmisPS = $this->repoListe->findOneBy(['code' => 'NA-PS']);
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $niveau = $this->repoNiveau->findOneBy([
            'id' => $idNiveau
        ]);
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $idNiveau
        ]);
        $unitesEnseignements = $niveau->getUniteEnseignements();
        $etudiants = $this->repoEtudiant->findBy([
            'statut' => true,
            'niveau' => $idNiveau
        ]);
        $resultats = $this->repoResultat->findBy([
            'niveau' => $idNiveau
        ]);
        $examensPS = null;
        $examensDS = null;
        $resultatsPS = null;
        $resultatsDS = null;
        if ($au) {
            $examenPS = $this->repoExamen->findOneBy([
                'type' => 'Première session',
                'anneeUniversitaire' => $au->getId()
            ]);
            $examenDS = $this->repoExamen->findOneBy([
                'type' => 'Deuxième session',
                'anneeUniversitaire' => $au->getId()
            ]);
            $resultatsPS = $this->repoResultat->findBy([
                'examen' => $examenPS->getId(),
                'anneeUniversitaire' => $au->getId(),
                'niveau' => $niveau->getId(),
            ]);
            $resultatsDS = $this->repoResultat->findBy([
                'examen' => $examenDS->getId(),
                'anneeUniversitaire' => $au->getId(),
                'niveau' => $niveau->getId()
            ]);
        } else {
            $auNull = true;
        }


        //Liste des UEs de toute l'année
        $uesNiveau = [];
        foreach ($semestres as $sem) {
            foreach ($sem->getUniteEnseignements() as $ueSem) {
                $uesNiveau[] = $ueSem;
            }
        }

        //Afficher l'examen DS à l'index
        $afficherExamenDS = false;
        if ($au) {
            if (count($uesNiveau) === count($resultatsPS)) {
                $afficherExamenDS = true;
            } else {
                $afficherExamenDS = false;
            }
        }

        //Afficher le bouton ajouter des notes
        if ($etudiants && $semestres && (count($semestres) > 1)) {
            if ((count($etudiants[0]->getNotes()) / 2 === (count($semestres[0]->getUniteEnseignements()) + count($semestres[1]->getUniteEnseignements())))) {
                $afficherLeBoutonAjouter = false;
            } else {
                $afficherLeBoutonAjouter = true;
            }
        } else {
            $afficherLeBoutonAjouter = false;
        }

        return $this->render('Admin/resultat/index.html.twig', [
            'semestres' => $semestres,
            'resultats' => $resultats,
            'examens' => $examens,
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'unitesEnseignements' => $unitesEnseignements,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'afficherLeBoutonAjouter' => $afficherLeBoutonAjouter,
            'listeEtudiantsNonAdmisPS' => $listeEtudiantsNonAdmisPS,
            'listeEtudiantsAdmisPS' => $listeEtudiantsAdmisPS,
            'anneeUniversitaires' => $anneeUniversitaires,
            'auNull' => $auNull,
            'afficherExamenDS' => $afficherExamenDS
        ]);
    }


    #[Route('/{idNiveau}/listePS', name: 'app_resultat_liste_PS_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function listeDesEtudiantsAdmisPS(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $etudiants = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsAdmisPS
        ]);

        return $this->render('Admin/resultat/liste.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'anneeUniversitaires' => $anneeUniversitaires,
            'titreDuListe' => "admis en Première session"
        ]);
    }

    #[Route('/{idNiveau}/listeDS', name: 'app_resultat_liste_DS_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function listeDesEtudiantsAdmisDS(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $listeEtudiantsAdmisDS = $this->repoListe->findOneBy(['code' => 'A-DS']);
        $etudiants = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsAdmisDS
        ]);

        return $this->render('Admin/resultat/liste.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'anneeUniversitaires' => $anneeUniversitaires,
            'titreDuListe' => "admis en Deuxième session"
        ]);
    }

    #[Route('/{idNiveau}/listeCandidatDS', name: 'app_resultat_liste_candidats_DS_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function listeDesCandidatsDS(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $listeEtudiantsNonAdmisPS = $this->repoListe->findOneBy(['code' => 'NA-PS']);
        $etudiants = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsNonAdmisPS
        ]);

        return $this->render('Admin/resultat/liste.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'anneeUniversitaires' => $anneeUniversitaires,
            'idNiveau' => $idNiveau,
            'titreDuListe' => "à passer la Deuxième session"
        ]);
    }

    #[Route('/{idNiveau}/resultatNote', name: 'app_resultat_note_DS_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function resultatAvecNotes(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $etudiants = $this->repoEtudiant->findBy([
            'statut' => true,
            'niveau' => $idNiveau
        ]);
        $examens = $this->repoExamen->findBy([
            'type' => 'Première session',
            'anneeUniversitaire' => $au->getId()
        ]);
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $idNiveau
        ]);

        return $this->render('Admin/resultat/liste.examen.ps.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'examens' => $examens,
            'anneeUniversitaires' => $anneeUniversitaires,
            'semestres' => $semestres,
        ]);
    }

    #[Route('/{idNiveau}/listeNonAdmis', name: 'app_resultat_liste_non_admis_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function listeDesEtudiantsNonAdmis(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $listeEtudiantsNonAdmisDS = $this->repoListe->findOneBy(['code' => 'NA-DS']);
        $etudiants = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsNonAdmisDS
        ]);

        return $this->render('Admin/resultat/liste.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'titreDuListe' => "non admis",
            'anneeUniversitaires' => $anneeUniversitaires
        ]);
    }

    #[Route('/{idNiveau}/listeAdmis', name: 'app_resultat_liste_admis_admin', methods: ['GET'], requirements: ['idNiveau' => '\d+'])]
    public function listeDesEtudiantsAdmis(int $idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $niveau = $this->repoNiveau->findOneBy(['id' => $idNiveau]);
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $listeEtudiantsAdmisDS = $this->repoListe->findOneBy(['code' => 'A-DS']);
        $etudiantsPS = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsAdmisPS
        ]);
        $etudiantsDS = $this->repoEtudiant->findBy([
            'niveau' => $idNiveau,
            'statut' => true,
            'listeDesEtudiants' => $listeEtudiantsAdmisDS
        ]);
        $etudiants = array_merge($etudiantsDS, $etudiantsPS);

        return $this->render('Admin/resultat/liste.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'idNiveau' => $idNiveau,
            'titreDuListe' => "admis",
            'anneeUniversitaires' => $anneeUniversitaires
        ]);
    }

    #[Route('/creer/{idNiveau}/{idSemestre}/{idExamen}', name: 'app_resultat_creer_admin', methods: ['GET', 'POST'], requirements: ['idNiveau' => '\d+', 'idExamen' => '\d+', 'idSemestre' => '\d+'])]
    public function new(Request $request, int $idNiveau, int $idExamen, int $idSemestre): Response
    {
        $resultat = new Resultat();
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        //Niveau actuel
        $niveau = $this->repoNiveau->findOneBy([
            'id' => $idNiveau
        ]);
        //Liste des étudiants du niveau
        $etudiants = $this->repoEtudiant->findBy([
            'statut' => true,
            'niveau' => $idNiveau
        ]);
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        $examen = $this->repoExamen->findOneBy([
            'id' => $idExamen,
            'anneeUniversitaire' => $au->getId()
        ]);
        $semestre = $this->repoSemestre->findOneBy(['id' => $idSemestre]);

        //Initialisation des notes pour chaque étudiant
        foreach ($etudiants as $etudiant) {
            if (($etudiant->getListeDesEtudiants() === $listeEtudiantsAdmisPS)) {
                unset($etudiants[array_search($etudiant, $etudiants)]);
                $etudiants = array_values($etudiants);
            } else {
                $note = new Note();
                $note->setEtudiant($etudiant);
                $resultat->addNote($note);
            }
        }
        //N'afficher que les UE du niveau           
        $unitesEnseignementsNiveau = $niveau->getUniteEnseignements();
        $unitesDispo = [];

        foreach ($unitesEnseignementsNiveau as $uniteEnseignement) {
            if ($uniteEnseignement->getSemestre()->contains($semestre)) {
                $resultatFindBy = $this->repoResultat->findOneBy([
                    'semestre' => $idSemestre,
                    'niveau' => $idNiveau,
                    'examen' => $idExamen,
                    'uniteEnseignement' => $uniteEnseignement->getId()
                ]);
                if ($resultatFindBy === null) {
                    $unitesDispo[] = $uniteEnseignement;
                }
            }
        }
        if ($unitesEnseignementsNiveau !== null) {
            foreach ($this->repoUnite->findAll() as $uniteEnseignement) {
                $uniteEnseignement->setStatut(false);
                $this->repoManager->persist($uniteEnseignement);
                $this->repoManager->flush();
            }
            foreach ($unitesDispo as $unite) {
                $unite->setStatut(true);
                $this->repoManager->persist($unite);
                $this->repoManager->flush();
            }
        }
        /************************************************************* */
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
            $resultat->setNiveau($niveau);
            $resultat->setSemestre($semestre);
            $resultat->setExamen($examen);
            $resultat->setAnneeUniversitaire($au);
            $archiveResultat = $this->cloneResultat($resultat);

            //Enregistrement des notes à chaque Etudiant
            foreach ($resultat->getNotes() as $key => $note) {
                foreach ($etudiants as $i => $etudiant) {
                    if ($etudiant->getListeDesEtudiants() != $listeEtudiantsAdmisPS) {
                        if ($key === $i && $note != null) {
                            $note->setEtudiant($etudiants[$i]);
                            $note->setResultat($resultat);
                            $note->setUniteEnseignement($resultat->getUniteEnseignement());
                            $note->setExamen($examen);
                            $resultat->addNote($note);
                            $archiveNote = $this->cloneNote($note, $archiveResultat);
                            $archiveResultat->addArchiveNote($archiveNote);
                            $this->repoManager->persist($note);
                            $this->repoManager->persist($archiveNote);
                        }
                    }
                }
            }
            $resultatsEnregistres = $this->repoResultat->findBy([
                'niveau' => $idNiveau,
                'semestre' => $resultat->getSemestre()->getId(),
                'examen' => $resultat->getExamen()->getId()
            ]);
            //Définir les UE déjà enregistrés
            $uesEnregistres = [];
            foreach ($resultatsEnregistres as $resultatEnregistre) {
                $uesEnregistres[] = $resultatEnregistre->getUniteEnseignement();
            }
            if (in_array($resultat->getUniteEnseignement(), $uesEnregistres)) {
                $this->addFlash(
                    'danger',
                    "<b> Enregistrement échoué. </b><br>  Unité d'enseignement déjà utilisé"
                );
            } else {
                if ($resultat->getSemestre()->getUniteEnseignements()->contains($resultat->getUniteEnseignement())) {
                    $this->repoManager->persist($resultat);
                    $this->repoManager->persist($archiveResultat);
                    //Ajouter dans une liste
                    $this->repoManager->flush();
                    $this->ajoutDansUneListe($examen, $etudiants, $niveau);
                    $this->remplirNotesDS($etudiants, $niveau);
                    $this->repoManager->flush();
                    $this->addFlash(
                        'success',
                        "<b> Enregistrement Ok. </b><br>  Vous avez ajouté des nouvelles notes"
                    );
                    // return $this->redirectToRoute('app_resultat_admin', ['idNiveau' => $idNiveau]);
                } else {
                    $this->addFlash(
                        'danger',
                        "<b> Enregistrement échoué. </b><br>  Cet unité d'enseignement ne figure pas da la semestre séléctionnée!"
                    );
                }
            }
            $form = $this->createForm(ResultatType::class, $resultat);
        }
        return $this->render('Admin/resultat/creer.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'anneeUniversitaires' => $anneeUniversitaires,
            'semestre' => $semestre
        ]);
    }

    #[Route('/{id}/edit', name: 'app_resultat_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Resultat $resultat): Response
    {
        $idNiveau = $resultat->getNiveau()->getId();
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        //Niveau actuel
        $niveau = $this->repoNiveau->findOneBy([
            'id' => $idNiveau
        ]);
        //Liste des étudiants du niveau
        $etudiants = $this->repoEtudiant->findBy([
            'statut' => true,
            'niveau' => $idNiveau
        ]);
        //N'afficher que les semestres du niveau
        try {
            $semestres = $this->repoSemestre->findBy([
                'niveau' => $idNiveau
            ]);
        } catch (\Throwable $th) {
            $semestres = null;
        }
        if ($semestres !== null) {
            foreach ($this->repoSemestre->findAll() as $semestre) {
                $semestre->setStatut(false);
                $this->repoManager->persist($semestre);
                $this->repoManager->flush();
            }
            foreach ($semestres as $semestre) {
                $semestre->setStatut(true);
                $this->repoManager->persist($semestre);
                $this->repoManager->flush();
            }
        }

        //N'afficher que les UE courant           
        try {
            $unitesEnseignements = $niveau->getUniteEnseignements();
        } catch (\Throwable $th) {
            $unitesEnseignements = null;
        }
        if ($unitesEnseignements !== null) {
            foreach ($this->repoUnite->findAll() as $uniteEnseignement) {
                $uniteEnseignement->setStatut(false);
                $this->repoManager->persist($uniteEnseignement);
                $this->repoManager->flush();
            }

            $uniteEnseignementCourant = $resultat->getUniteEnseignement();
            $uniteEnseignementCourant->setStatut(true);
            $this->repoManager->persist($uniteEnseignementCourant);
            $this->repoManager->flush();
        }
        /************************************************************* */
        $form = $this->createForm(ResultatType::class, $resultat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Enregistrement des notes à chaque Etudiant
            foreach ($resultat->getNotes() as $key => $note) {
                $archiveNote = $this->repoArchiveNote->findOneBy(['id' => $note->getId()]);
                $archiveNote->setValeur($note->getValeur());

                $this->repoManager->persist($note);
                $this->repoManager->persist($archiveNote);
            }

            $resultatsEnregistres = $this->repoResultat->findBy([
                'niveau' => $idNiveau,
                'semestre' => $resultat->getSemestre()->getId(),
                'uniteEnseignement' => $resultat->getUniteEnseignement()
            ]);

            if ($resultatsEnregistres == null) {
                $this->addFlash(
                    'danger',
                    "<b> Mis à jour échoué. </b><br>  Unité d'enseignement vide. Veuillez ajouter d'abord les notes de cet unité d'enseignement."
                );
            } else {
                $this->repoManager->persist($resultat);
                $examens = $this->repoExamen->findBy([
                    'anneeUniversitaire' => $au->getId()
                ]);
                $niveau = $resultat->getNiveau();
                $this->ajoutDansUneListe($resultat->getExamen(), $etudiants, $niveau);
                // $this->remplirNotesDS($etudiants, $niveau);
                $this->repoManager->flush();
                $this->addFlash(
                    'success',
                    "<b> Mis à jour Ok. </b><br>  Vous avez mis à jour des notes"
                );
            }
            // return $this->redirectToRoute('app_resultat_admin', ['idNiveau' => $idNiveau]);
        }
        $semestre = $resultat->getSemestre();
        return $this->render('Admin/resultat/edit.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveau' => $niveau,
            'semestre' => $semestre
        ]);
    }

    #[Route('/{id}', name: 'app_resultat_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Resultat $resultat): Response
    {
        if (
            $this->isCsrfTokenValid(
                'delete' . $resultat->getId(),
                $request->request->get('_token')
            )
        ) {
            $this->repoResultat->remove($resultat, true);
            $this->addFlash(
                'success',
                '<b> Supprimer Ok. </b><br>  Vous avez supprimé le resultat'
            );
        }
        return $this->redirectToRoute(
            'app_resultat__admin',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
    #[Route('/{idEtudiant}/afficher', name: 'app_resultat_afficher_admin', methods: ['GET', 'POST'], requirements: ['idEtudiant' => '\d+'])]
    public function afficher(int $idEtudiant): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        $norme = $this->repoNorme->findAll()[0];
        $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
        $etudiant = $this->repoEtudiant->findOneBy([
            'id' => $idEtudiant
        ]);

        $notes = $this->repoNote->findBy([
            'etudiant' => $idEtudiant
        ]);
        $resultats = $this->repoResultat->findBy([
            'anneeUniversitaire' => $au->getId(),
            'niveau' => $etudiant->getNiveau()->getId()
        ]);
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $etudiant->getNiveau()->getId()
        ]);
        $examens = $this->repoExamen->findBy([
            'anneeUniversitaire' => $au->getId(),
        ]);
        if ($etudiant->getListeDesEtudiants() === $listeEtudiantsAdmisPS) {
            $examens = $this->repoExamen->findBy([
                'anneeUniversitaire' => $au->getId(),
                'type' => 'Première session',
            ]);
        } else {
            $examens = $this->repoExamen->findBy([
                'anneeUniversitaire' => $au->getId(),
            ]);
        }

        return $this->render('Admin/resultat/afficher.html.twig', [
            'resultats' => $resultats,
            'niveauxMenu' => $niveauxMenu,
            'etudiant' => $etudiant,
            'notes' => $notes,
            'examens' => $examens,
            'anneeUniversitaires' => $anneeUniversitaires,
            'semestres' => $semestres,
            'norme' => $norme,
        ]);
    }

    public function remplirNotesDS(array $etudiants, Niveau $niveau)
    {
        $semestres = $this->repoSemestre->findBy([
            'niveau' => $niveau->getId()
        ]);
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        $listeEtudiantsNonAdmisPS = $this->repoListe->findOneBy(['code' => 'NA-PS']);
        //Tout les UEs de l'examen
        $uesNiveau = [];
        foreach ($semestres as $sem) {
            foreach ($sem->getUniteEnseignements() as $ueSem) {
                $uesNiveau[] = $ueSem;
            }
        }
        $examenPS = $this->repoExamen->findOneBy([
            'anneeUniversitaire' => $au->getId(),
            'type' => 'Première session',
        ]);
        $resultatsPS = $this->repoResultat->findBy([
            'examen' => $examenPS->getId(),
            'anneeUniversitaire' => $au->getId(),
            'niveau' => $niveau->getId()
        ]);
        $examenDS = $this->repoExamen->findOneBy([
            'anneeUniversitaire' => $au->getId(),
            'type' => 'Deuxième session',
        ]);

        $notesPS = [];
        //Toutes les notes des étudiants à l'examen PS
        if (count($uesNiveau) === count($resultatsPS)) {
            foreach ($resultatsPS as $resultatPS) {
                foreach ($resultatPS->getNotes() as $notePS) {
                    $notesPS[] = $notePS;
                }
            }
            foreach ($semestres as $semestre) {
                $uesSemestre = $semestre->getUniteEnseignements();
                foreach ($uesSemestre as $ueSemestre) {
                    $resultat = new Resultat();
                    $resultat->setNiveau($niveau);
                    $resultat->setSemestre($semestre);
                    $resultat->setExamen($examenDS);
                    $resultat->setAnneeUniversitaire($au);
                    $resultat->setUniteEnseignement($ueSemestre);
                    $archiveResultat = $this->cloneResultat($resultat);
                    foreach ($notesPS as $notePS) {
                        foreach ($etudiants as $etudiant) {
                            if (($etudiant->getListeDesEtudiants() === $listeEtudiantsNonAdmisPS) && ($notePS->getEtudiant() === $etudiant) && ($notePS->getUniteEnseignement() === $ueSemestre) && ($notePS->getResultat()->getSemestre() === $semestre)) {
                                $noteDS = new Note();
                                $noteDS->setEtudiant($etudiant);
                                $noteDS->setResultat($resultat);
                                $noteDS->setExamen($examenDS);
                                $noteDS->setUniteEnseignement($ueSemestre);
                                $noteDS->setValeur($notePS->getValeur());
                                $archiveNoteDS = $this->cloneNote($noteDS, $archiveResultat);
                                $this->repoManager->persist($noteDS);
                                $this->repoManager->persist($archiveNoteDS);
                            }
                        }
                    }
                    $this->repoManager->persist($resultat);
                    $this->repoManager->persist($archiveResultat);
                    $this->repoManager->flush();
                }
            }
        }
    }

    public function ajoutDansUneListe(Examen $examen, array $etudiants, Niveau $niveau)
    {
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        //Pour chaque étudiant
        foreach ($etudiants as $etudiant) {
            $listeEtudiantsAdmisPS = $this->repoListe->findOneBy(['code' => 'A-PS']);
            $listeEtudiantsAdmisDS = $this->repoListe->findOneBy(['code' => 'A-DS']);
            $listeEtudiantsNonAdmisPS = $this->repoListe->findOneBy(['code' => 'NA-PS']);
            $listeEtudiantsNonAdmisDS = $this->repoListe->findOneBy(['code' => 'NA-DS']);
            $moyenneSemestre1 = 0;
            $totaleNotesSemestre1 = 0;
            $totaleNotesSemestre2 = 0;
            $moyenneSemestre2 = 0;
            $semestres = $this->repoSemestre->findBy([
                'niveau' => $niveau->getId()
            ]);
            //Pour chaque semestre
            foreach ($semestres as $semestre) {
                //Tout les UEs de l'examen
                $uesNiveau = [];
                foreach ($semestres as $sem) {
                    foreach ($sem->getUniteEnseignements() as $ueSem) {
                        $uesNiveau[] = $ueSem;
                    }
                }
                $examenPS = $this->repoExamen->findOneBy([
                    'anneeUniversitaire' => $au->getId(),
                    'type' => 'Première session',
                ]);

                $resultatsPS = $this->repoResultat->findBy([
                    'examen' => $examenPS->getId(),
                    'anneeUniversitaire' => $au->getId(),
                    'niveau' => $niveau->getId()
                ]);


                $notesPS = [];
                //Récupération de toutes les notes PS
                if (count($uesNiveau) === count($resultatsPS)) {
                    foreach ($resultatsPS as $resultatPS) {
                        foreach ($resultatPS->getNotes() as $notePS) {
                            if ($notePS->getEtudiant() === $etudiant) {
                                $notesPS[] = $notePS;
                            }
                        }
                    }
                }

                $notes = []; //Notes de semestre
                // Si chaque UE a une note
                $resultatsSemestre = $this->repoResultat->findBy([
                    'examen' => $examen->getId(),
                    'niveau' => $niveau->getId(),
                    'anneeUniversitaire' => $au->getId(),
                    'semestre' => $semestre->getId()
                ]);

                foreach ($resultatsSemestre as $resultatSemestre) {
                    foreach ($resultatSemestre->getNotes() as $noteEtudiant) {
                        if ($noteEtudiant->getEtudiant() === $etudiant) {
                            $notes[] = $noteEtudiant;
                        }
                    }
                }

                $uniteEnseignements = $semestre->getUniteEnseignements();

                $uesEgalsNotes = count($uniteEnseignements) === count($notes); //Si l'UEs de semestre sont égals aux notes de semestre

                //Récuperer seulement les notes de l'examen et du semestre courant
                foreach ($notes as $note) {
                    if (($note->getResultat()->getExamen() === $examen) && ($note->getResultat()->getSemestre() === $semestre)) {
                        if ($semestre->getType() === '1ère semestre') {
                            $totaleNotesSemestre1 += $note->getValeur();
                        } else if ($semestre->getType() === '2ème semestre') {
                            $totaleNotesSemestre2 += $note->getValeur();
                        }
                    }
                }

                if ($uesEgalsNotes) {
                    if ($semestre->getType() === '1ère semestre') {
                        $moyenneSemestre1 = $totaleNotesSemestre1 / count($uniteEnseignements);
                    } else if ($semestre->getType() === '2ème semestre') {
                        $moyenneSemestre2 = $totaleNotesSemestre2 / count($uniteEnseignements);
                    }
                    if (count($uesNiveau) === count($resultatsPS)) {
                        if (($moyenneSemestre1 >= 10) && ($moyenneSemestre2 >= 10)) {
                            if ($examen->getType() === 'Première session') {
                                $etudiant->setListeDesEtudiants($listeEtudiantsAdmisPS);
                                $this->repoManager->persist($etudiant);
                            } else if ($examen->getType() === 'Deuxième session') {
                                $etudiant->setListeDesEtudiants($listeEtudiantsAdmisDS);
                                $this->repoManager->persist($etudiant);
                            }
                        } else {
                            if ($examen->getType() === 'Première session') {
                                $etudiant->setListeDesEtudiants($listeEtudiantsNonAdmisPS);
                                $this->repoManager->persist($etudiant);
                            } else if ($examen->getType() === 'Deuxième session') {
                                $etudiant->setListeDesEtudiants($listeEtudiantsNonAdmisDS);
                                $this->repoManager->persist($etudiant);
                            }
                        }
                    }
                }
            }
        }
    }

    public function cloneNote(Note $note, ArchiveResultat $archiveResultat)
    {
        $archiveNote = new ArchiveNote();

        $archiveNote->setValeur($note->getValeur());
        $archiveNote->setEtudiant($note->getEtudiant());
        $archiveNote->setResultat($archiveResultat);
        $archiveNote->setExamen($note->getExamen());
        $archiveNote->setUniteEnseignement($note->getUniteEnseignement());

        return $archiveNote;
    }

    public function cloneResultat(Resultat $resultat)
    {
        $archiveResultat = new ArchiveResultat();

        $archiveResultat->setNiveau($resultat->getNiveau());
        $archiveResultat->setSemestre($resultat->getSemestre());
        $archiveResultat->setExamen($resultat->getExamen());
        $archiveResultat->setUniteEnseignement($resultat->getUniteEnseignement());
        $archiveResultat->setAnneeUniversitaire($resultat->getAnneeUniversitaire());

        return $archiveResultat;
    }
}
