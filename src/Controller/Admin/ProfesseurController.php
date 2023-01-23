<?php

namespace App\Controller\Admin;

use App\Entity\Professeur;
use App\Form\ProfesseurType;
use App\Repository\NiveauRepository;
use App\Repository\ProfesseurRepository;
use App\Repository\AnneeUniversitaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/professeur')]
class ProfesseurController extends AbstractController
{
    private $repoManager;
    private $repoProfesseur;
    private $repoAnneeUniversitaire;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        ProfesseurRepository $repoProfesseur,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoProfesseur = $repoProfesseur;
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }

    #[Route('/', name: 'app_professeur_admin')]
    public function index(): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

        $professeurs = $this->repoProfesseur->findAll();
        return $this->render('Admin/professeur/index.html.twig', [
            'professeurs' => $professeurs,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/creer', name: 'app_professeur_creer_admin')]
    public function new(Request $request): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();
        
        $professeur = new Professeur();
        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($professeur);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez ajouté un nouvau professeur : {$professeur->getNom()} {$professeur->getPrenoms()}"
            );
            return $this->redirectToRoute('app_professeur_admin');
        }
        return $this->render('Admin/professeur/creer.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}/edit', name: 'app_professeur_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Professeur $professeur): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(ProfesseurType::class, $professeur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($professeur);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_professeur_admin');
        }
        return $this->render('Admin/professeur/edit.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}', name: 'app_professeur_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Professeur $professeur): Response
    {
        if ($this->isCsrfTokenValid('delete' . $professeur->getId(), $request->request->get('_token'))) {
            $this->repoProfesseur->remove($professeur, true);
            $this->addFlash(
                'success',
                "<b> Supprimer Ok. </b><br>  Vous avez supprimé le professeur {$professeur->getNom()} {$professeur->getPrenoms()}"
            );
        }
        return $this->redirectToRoute('app_professeur_admin', [], Response::HTTP_SEE_OTHER);
    }
}
