<?php

namespace App\Controller\Admin;

use App\Entity\Semestre;
use App\Form\SemestreType;
use App\Repository\NiveauRepository;
use App\Repository\SemestreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\AnneeUniversitaireRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/semestre')]
class SemestreController extends AbstractController
{
    private $repoManager;
    private $repoSemestre;
    private $repoAnneeUniversitaire;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        SemestreRepository $repoSemestre,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoSemestre = $repoSemestre;
        $this->repoNiveau = $repoNiveau;
    }

    #[Route('/', name: 'app_semestre_admin')]
    public function index(): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $semestres = $this->repoSemestre->findAll();
        return $this->render('Admin/semestre/index.html.twig', [
            'semestres' => $semestres,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/creer', name: 'app_semestre_creer_admin')]
    public function new(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $semestre = new Semestre();
        $form = $this->createForm(SemestreType::class, $semestre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $semestre->setStatut(true);
            $this->repoManager->persist($semestre);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez ajouté un nouvau semestre : {$semestre->getNom()}"
            );
            return $this->redirectToRoute('app_semestre_admin');
        }
        return $this->render('Admin/semestre/creer.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}/edit', name: 'app_semestre_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Semestre $semestre): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(SemestreType::class, $semestre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($semestre);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_semestre_admin');
        }
        return $this->render('Admin/semestre/edit.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}', name: 'app_semestre_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Semestre $semestre): Response
    {
        if ($this->isCsrfTokenValid('delete' . $semestre->getId(), $request->request->get('_token'))) {
            $this->repoSemestre->remove($semestre, true);
            $this->addFlash(
                'success',
                "<b> Supprimer Ok. </b><br>  Vous avez supprimé le semestre {$semestre->getNom()}"
            );
        }
        return $this->redirectToRoute('app_semestre_admin', [], Response::HTTP_SEE_OTHER);
    }
}
