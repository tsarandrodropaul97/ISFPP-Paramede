<?php

namespace App\Controller\Admin;

use App\Entity\Etudiant;
use App\Form\EtudiantType;
use App\Repository\EtudiantRepository;
use App\Repository\FraisRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/etudiant')]
class EtudiantController extends AbstractController
{
    private $repoManager;
    private $repoEtudiant;
    private $repoNiveau;
    private $repoFrais;
    private $repoInscription;

    public function __construct(
        EntityManagerInterface $repoManager,
        EtudiantRepository $repoEtudiant,
        NiveauRepository $repoNiveau,
        FraisRepository $repoFrais,
        InscriptionRepository $repoInscription
    ) {
        $this->repoEtudiant = $repoEtudiant;
        $this->repoManager = $repoManager;
        $this->repoNiveau = $repoNiveau;
        $this->repoFrais = $repoFrais;
        $this->repoInscription = $repoInscription;
    }

    #[Route('/', name: 'app_etudiant_admin')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }

    #[Route('/inscription', name: 'app_etudiant_inscription_admin')]
    public function inscription(Request $request): Response
    {
        /* information renvoyé au menu */
        $niveauxMenu = $this->repoNiveau->findAll();
        $etudiant = $this->repoEtudiant->findOneBy([], ['matricule' => 'DESC'], [1]);
        $inscription = $this->repoInscription->findOneBy([], ['createdAt' => 'DESC'], [1]);
        $dateFinInscription = $inscription->getDateFin()->format('d-m-Y');
        $dateDebutInscription = $inscription->getDateDebut()->format('d-m-Y');
        $matricule = null;
        if (empty($etudiant)) {
            $matricule = 1;
        } else {
            $matricule = $etudiant->getMatricule() + 1;
        }
        $etudiant = new Etudiant;
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $idNiveau = $form->getData()->getNiveau()->getId();
            $frai = $this->repoFrais->findOneBy(['niveau' => $idNiveau]);
            $etudiant->setMatricule($matricule);
            $etudiant->setFrais($frai);
            $this->repoManager->persist($etudiant);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez inscrire un nouvau etudiant"
            );
            return $this->redirectToRoute('app_etudiant_inscription_admin');
        }
        return $this->render('Admin/etudiant/inscription.html.twig', [
            'form' => $form->createView(),
            'matricule' => $matricule,
            'niveauxMenu' => $niveauxMenu,
            'dateFinInscription' => $dateFinInscription,
            'dateDebutInscription' => $dateDebutInscription
        ]);
    }

    #[Route('/{idNiveau}/liste', name: 'app_etudiant_liste_admin')]
    public function liste($idNiveau): Response
    {
        /* information renvoyé au menu */
        $niveauxMenu = $this->repoNiveau->findAll();

        $etudiants = $this->repoEtudiant->findBy(['niveau'  => $idNiveau]);
        $nbrEtudiant = count($etudiants);
        $niveau = null;
        foreach ($etudiants as $etudiant) {
            $niveau = $etudiant->getNiveau()->getNom();
            break;
        }
        return $this->render('Admin/etudiant/index.html.twig', [
            'niveauxMenu' => $niveauxMenu,
            'etudiants' => $etudiants,
            'niveau' => $niveau,
            'nbrEtudiant' => $nbrEtudiant
        ]);
    }

    #[Route('/{id}/edit', name: 'app_etudiant_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Etudiant $etudiant): Response
    {
        /* information renvoyé au menu */
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($etudiant);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_etudiant_liste_admin', ['idNiveau' => $etudiant->getNiveau()->getId()]);
        }
        return $this->render('Admin/etudiant/edit.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}', name: 'app_etudiant_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Etudiant $etudiant): Response
    {
        if ($this->isCsrfTokenValid('delete' . $etudiant->getId(), $request->request->get('_token'))) {
            $this->repoEtudiant->remove($etudiant, true);
            $this->addFlash(
                'success',
                "<b> Supprimer Ok. </b><br>  Vous avez supprimer l'information de {$etudiant->getNom()}"
            );
        }
        return $this->redirectToRoute('app_etudiant_liste_admin', ['idNiveau' => $etudiant->getNiveau()->getId()], Response::HTTP_SEE_OTHER);
    }
}
