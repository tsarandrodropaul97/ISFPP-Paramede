<?php

namespace App\Controller\Admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\UserRepository;
use App\Repository\NiveauRepository;
use App\Repository\FormationRepository;
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
#[Route('/formation')]
class FormationController extends AbstractController
{
    private $repoManager;
    private $repoFormation;
    private $repoAnneeUniversitaire;
    private $repoUser;
    private $repoNiveau;

    public function __construct(
        UserRepository $repoUser,
        EntityManagerInterface $repoManager,
        FormationRepository $repoFormation,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoUser = $repoUser;
        $this->repoManager = $repoManager;
        $this->repoFormation = $repoFormation;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }

    #[Route('/', name: 'app_formation_admin', methods: ['GET'])]
    public function index(): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $formation = $this->repoFormation->findOneBy([]);
        return $this->render('Admin/formation/index.html.twig', [
            'formation' => $formation,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/creer', name: 'app_formation_admin_creer', methods: ['GET', 'POST'])]
    public function creer(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($formation);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_formation_admin');
        }
        return $this->render('Admin/formation/creer.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}/edit', name: 'app_formation_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($formation);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_formation_admin');
        }

        return $this->render('Admin/formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_formation_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, FormationRepository $formationRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $formation->getId(), $request->request->get('_token'))) {
            $formationRepository->remove($formation, true);
        }
        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
