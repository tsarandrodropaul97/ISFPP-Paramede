<?php

namespace App\Controller\Admin;

use App\Entity\Parcours;
use App\Form\ParcoursType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\NiveauRepository;
use App\Repository\ParcoursRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/parcours')]
class ParcoursController extends AbstractController
{
    private $repoManager;
    private $repoUser;
    private $repoNiveau;
    private $repoParcours;
    private $repoAnneeUniversitaire;

    public function __construct(
        UserRepository $repoUser,
        EntityManagerInterface $repoManager,
        ParcoursRepository $repoParcours,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoUser = $repoUser;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoManager = $repoManager;
        $this->repoParcours = $repoParcours;
        $this->repoNiveau = $repoNiveau;
    }

    #[Route('/', name: 'app_parcours_admin')]
    public function index(): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $parcours = $this->repoParcours->findAll();
        return $this->render('Admin/parcours/index.html.twig', [
            'parcours' => $parcours,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/creer/parcours', name: 'app_directeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $parcours = new Parcours();
        $form = $this->createForm(ParcoursType::class, $parcours);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($parcours);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_parcours_admin');
        }
        return $this->render('Admin/parcours/creer.html.twig', [
            'parcours' => $parcours,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}/edit', name: 'app_parcours_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parcours $parcours): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(ParcoursType::class, $parcours);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($parcours);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_parcours_admin');
        }
        return $this->render('Admin/directeur/edit.html.twig', [
            'parcours' => $parcours,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu

        ]);
    }

    #[Route('/{id}/delete', name: 'app_parcours_delete', methods: ['POST'])]
    public function delete(Request $request, Parcours $parcours): Response
    {
        if ($this->isCsrfTokenValid('delete' . $parcours->getId(), $request->request->get('_token'))) {
            $this->repoManager->remove($parcours, true);
            $this->repoManager->flush();
        }

        return $this->redirectToRoute('app_parcours_admin', [], Response::HTTP_SEE_OTHER);
    }
}
