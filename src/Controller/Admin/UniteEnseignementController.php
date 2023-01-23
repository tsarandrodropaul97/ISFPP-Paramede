<?php

namespace App\Controller\Admin;

use App\Entity\UniteEnseignement;
use App\Form\UniteEnseignementType;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UniteEnseignementRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnneeUniversitaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/unite_enseignement')]
class UniteEnseignementController extends AbstractController
{
    private $repoManager;
    private $repoAnneeUniversitaire;
    private $repoUnite;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        UniteEnseignementRepository $repoUnite,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoManager = $repoManager;
        $this->repoUnite = $repoUnite;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }
    #[Route('/', name: 'app_unite_enseignement_admin')]
    public function index(Request $request): Response
    {
          /* information renvoyé au menu */
          $niveauxMenu = $this->repoNiveau->findAll();
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $unitesEnseignements = $this->repoUnite->findAll();

          $uniteEnseignement = new UniteEnseignement();
        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uniteEnseignement->setStatut(false);
            $this->repoManager->persist($uniteEnseignement);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez ajouté une unité d'enseignement"
            );
            return $this->redirectToRoute('app_unite_enseignement_admin');
        }

        return $this->render('Admin/unite_enseignement/index.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu,
            'anneeUniversitaires' => $anneeUniversitaires,
            'unitesEnseignements' => $unitesEnseignements,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_unite_enseignement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UniteEnseignement $uniteEnseignement): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(UniteEnseignementType::class, $uniteEnseignement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($uniteEnseignement);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_unite_enseignement_admin');
        }
        return $this->render('Admin/unite_enseignement/edit.html.twig', [
            'unitesEnseignement' => $uniteEnseignement,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_unite_enseignement_delete', methods: ['POST'])]
    public function delete(Request $request, UniteEnseignement $uniteEnseignement, UniteEnseignementRepository $repoUnite): Response
    {
        if ($this->isCsrfTokenValid('delete' . $uniteEnseignement->getId(), $request->request->get('_token'))) {
            $repoUnite->remove($uniteEnseignement, true);
        }
        $this->addFlash(
            'success',
            "<b> Supprimer Ok. </b><br>  Vous avez supprimé l'UE {$uniteEnseignement->getNom()}"
        );

        return $this->redirectToRoute('app_unite_enseignement_admin', [], Response::HTTP_SEE_OTHER);
    }
}
