<?php

namespace App\Controller\Admin;

use App\Form\ListeEtudiantsType;
use App\Entity\ListeDesEtudiants;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\ListeDesEtudiantsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/liste_etudiant')]
class ListeDesEtudiantsController extends AbstractController
{
    private $repoManager;
    private $repoListe;
    private $repoAnneeUniversitaire;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        ListeDesEtudiantsRepository $repoListe,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoManager = $repoManager;
        $this->repoListe = $repoListe;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }
    #[Route('/', name: 'app_liste_etudiant_admin')]
    public function index(Request $request): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

          $listeDesEtudiants = $this->repoListe->findAll();

          $listeEtudiant = new ListeDesEtudiants();
        $form = $this->createForm(ListeEtudiantsType::class, $listeEtudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($listeEtudiant);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez ajouté une liste d'étudiant"
            );
            return $this->redirectToRoute('app_liste_etudiant_admin');
        }

        return $this->render('Admin/liste_etudiant/index.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'listeDesEtudiants' => $listeDesEtudiants,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_liste_etudiant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ListeDesEtudiants $listeEtudiant): Response
    {
        /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(ListeEtudiantsType::class, $listeEtudiant);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($listeEtudiant);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_liste_etudiant_admin');
        }
        return $this->render('Admin/liste_etudiant/edit.html.twig', [
            'unitesEnseignement' => $listeEtudiant,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_liste_etudiant_delete', methods: ['POST'])]
    public function delete(Request $request, ListeDesEtudiants $listeEtudiant, ListeDesEtudiantsRepository $repoListe): Response
    {
        if ($this->isCsrfTokenValid('delete' . $listeEtudiant->getId(), $request->request->get('_token'))) {
            $repoListe->remove($listeEtudiant, true);
        }
        $this->addFlash(
            'success',
            "<b> Supprimer Ok. </b><br>  Vous avez supprimé l'UE {$listeEtudiant->getNom()}"
        );

        return $this->redirectToRoute('app_liste_etudiant_admin', [], Response::HTTP_SEE_OTHER);
    }
}
