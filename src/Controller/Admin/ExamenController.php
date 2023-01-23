<?php

namespace App\Controller\Admin;

use App\Entity\Examen;
use App\Form\ExamenType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\ExamenRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/examen')]
class ExamenController extends AbstractController
{
    private $repoManager;
    private $repoExamen;
    private $repoNiveau;
    private $repoAnneeUniversitaire;

    public function __construct(
        EntityManagerInterface $repoManager,
        ExamenRepository $repoExamen,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoManager = $repoManager;
        $this->repoExamen = $repoExamen;
        $this->repoNiveau = $repoNiveau;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
    }
    #[Route('/', name: 'app_examen_admin')]
    public function index(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $au = $this->repoAnneeUniversitaire->findOneByAU(new \DateTime('now'));
        $examens = null;
        if ($au) {
            $examens = $this->repoExamen->findBy([
                'anneeUniversitaire' => $au->getId()
            ]);
        }
        $examen = new Examen();
        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $auExamens = $examen->getAnneeUniversitaire()->getExamens();
            $typeExamenExist = false;
            $typeExamen = null;
            foreach ($auExamens as $auExamen) {
                if ($auExamen->getType() === $examen->getType()) {
                    $typeExamenExist = true;
                    $typeExamen = $examen->getType();
                }
            }
            if ($typeExamenExist) {
                $this->addFlash(
                    'danger',
                    "<b> Enregistrement échoué. </b><br>  Le type d'examen '$typeExamen' existe déjà!"
                );
            } else {
                $examen->setAnneeExamen(new \DateTime('now'));
                $this->repoManager->persist($examen);
                $this->repoManager->flush();
                $this->addFlash(
                    'success',
                    "<b> Enregistrement Ok. </b><br>  Vous avez ajouté un examen"
                );
            }
            return $this->redirectToRoute('app_examen_admin');
        }

        return $this->render('Admin/examen/index.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'examens' => $examens,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_examen_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Examen $examen): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($examen);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_examen_admin');
        }
        return $this->render('Admin/examen/edit.html.twig', [
            'examen' => $examen,
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }

    #[Route('/{id}', name: 'app_examen_delete', methods: ['POST'])]
    public function delete(Request $request, Examen $examen): Response
    {
        if ($this->isCsrfTokenValid('delete' . $examen->getId(), $request->request->get('_token'))) {
            $this->repoExamen->remove($examen, true);
        }
        $this->addFlash(
            'success',
            "<b> Supprimer Ok. </b><br>  Vous avez supprimé le examen {$examen->getNom()}"
        );

        return $this->redirectToRoute('app_examen_admin', [], Response::HTTP_SEE_OTHER);
    }
}
