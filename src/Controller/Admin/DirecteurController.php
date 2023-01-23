<?php

namespace App\Controller\Admin;

use App\Entity\Directeur;
use App\Form\DirecteurType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\DirecteurRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/directeur')]
class DirecteurController extends AbstractController
{
    private $repoNiveau;
    private $repoManager;
    private $repoDirecteur;
    private $repoAnneeUniversitaire;

    public function __construct(
        EntityManagerInterface $repoManager,
        NiveauRepository $repoNiveau,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        DirecteurRepository $repoDirecteur
    ) {
        $this->repoManager = $repoManager;
        $this->repoNiveau = $repoNiveau;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoDirecteur = $repoDirecteur;
    }
    #[Route('/', name: 'app_directeur_index', methods: ['GET'])]
    public function index(): Response
    {

        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $directeur = $this->repoDirecteur->findOneBy([]);
        return $this->render('Admin/directeur/index.html.twig', [
            'directeur' => $directeur,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/new', name: 'app_directeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {

        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $directeur = new Directeur();
        $form = $this->createForm(DirecteurType::class, $directeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoDirecteur->add($directeur, true);

            return $this->redirectToRoute('app_directeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Admin/directeur/new.html.twig', [
            'directeur' => $directeur,
            'form' => $form,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu

        ]);
    }

    #[Route('/{id}/edit', name: 'app_directeur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Directeur $directeur): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $form = $this->createForm(DirecteurType::class, $directeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoDirecteur->add($directeur, true);

            return $this->redirectToRoute('app_directeur_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('Admin/directeur/edit.html.twig', [
            'directeur' => $directeur,
            'form' => $form,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_directeur_delete', methods: ['POST'])]
    public function delete(Request $request, Directeur $directeur, DirecteurRepository $directeurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $directeur->getId(), $request->request->get('_token'))) {
            $directeurRepository->remove($directeur, true);
        }

        return $this->redirectToRoute('app_directeur_index', [], Response::HTTP_SEE_OTHER);
    }
}
