<?php

namespace App\Controller\Admin;

use App\Entity\AnneeUniversitaire;
use App\Form\AnneeUniversitaireType;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnneeUniversitaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/annee_universitaire')]
class AnneeUniversitaireController extends AbstractController
{
    private $repoManager;
    private $repoAnneeUniversitaire;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }
    
    #[Route('/', name: 'app_annee_universitaire_admin')]
    public function index(): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        return $this->render('Admin/annee_universitaire/index.html.twig', [
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/creer', name: 'app_annee_universitaire_creer_admin')]
    public function new(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        
        $anneeUniversitaire = new AnneeUniversitaire();
        $form = $this->createForm(AnneeUniversitaireType::class, $anneeUniversitaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($anneeUniversitaire);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez ajouté un nouvau année universitaire : {$anneeUniversitaire->getNom()}"
            );
            return $this->redirectToRoute('app_annee_universitaire_admin');
        }
        return $this->render('Admin/annee_universitaire/creer.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}/edit', name: 'app_annee_universitaire_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnneeUniversitaire $anneeUniversitaire): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(AnneeUniversitaireType::class, $anneeUniversitaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($anneeUniversitaire);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_annee_universitaire_admin');
        }
        return $this->render('Admin/annee_universitaire/edit.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}', name: 'app_annee_universitaire_delete_admin', methods: ['POST'])]
    public function delete(Request $request, AnneeUniversitaire $anneeUniversitaire): Response
    {
        if ($this->isCsrfTokenValid('delete' . $anneeUniversitaire->getId(), $request->request->get('_token'))) {
            $this->repoAnneeUniversitaire->remove($anneeUniversitaire, true);
            $this->addFlash(
                'success',
                "<b> Supprimer Ok. </b><br>  Vous avez supprimé le anneeUniversitaire {$anneeUniversitaire->getNom()}"
            );
        }
        return $this->redirectToRoute('app_annee_universitaire_admin', [], Response::HTTP_SEE_OTHER);
    }
}
