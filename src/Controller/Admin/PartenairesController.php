<?php

namespace App\Controller\Admin;

use App\Entity\Partenaires;
use App\Form\PartenairesType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\NiveauRepository;
use App\Repository\PartenairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/partenaires')]
class PartenairesController extends AbstractController
{
    private $repoManager;
    private $repoPartenaires;
    private $repoNiveau;
    private $repoAnneeUniversitaire;

    public function __construct(
        EntityManagerInterface $repoManager,
        PartenairesRepository $repoPartenaires,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau

    ) {
        $this->repoManager = $repoManager;
        $this->repoPartenaires = $repoPartenaires;
        $this->repoNiveau = $repoNiveau;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
    }

    #[Route('/', name: 'app_partenaires_admin', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $partenaire = new Partenaires();
        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($partenaire);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_partenaires_admin');
        }

        $partenaires = $this->repoPartenaires->findAll();
        return $this->render('Admin/partenaires/index.html.twig', [
            'partenaires' => $partenaires,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}/edit', name: 'app_partenaires_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partenaires $partenaire, PartenairesRepository $partenairesRepository): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(PartenairesType::class, $partenaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($partenaire);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_partenaires_admin');
        }
        return $this->render('Admin/partenaires/edit.html.twig', [
            'partenaire' => $partenaire,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_partenaires_delete', methods: ['POST'])]
    public function delete(Request $request, Partenaires $partenaire, PartenairesRepository $partenairesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $partenaire->getId(), $request->request->get('_token'))) {
            $partenairesRepository->remove($partenaire, true);
        }

        return $this->redirectToRoute('app_partenaires_admin', [], Response::HTTP_SEE_OTHER);
    }
}
