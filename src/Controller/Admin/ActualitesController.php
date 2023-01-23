<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Form\ActualiteType;
use App\Repository\ActualiteRepository;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\NiveauRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/actualites')]
class ActualitesController extends AbstractController
{
    private $manager;
    private $repoActualite;
    private $repoNiveau;
    private $repoAnneeUniversitaire;

    public function __construct(
        ActualiteRepository $repoActualite,
        EntityManagerInterface $manager,
        NiveauRepository $repoNiveau,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,

    ) {
        $this->repoActualite = $repoActualite;
        $this->manager = $manager;
        $this->repoNiveau = $repoNiveau;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
    }

    #[Route('/', name: 'app_actualites_admin')]
    public function index(): Response
    {

        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $actualites = $this->repoActualite->findAll();
        $dateNow = new DateTime();
        $nbActualite = $this->repoActualite->count([]);
        foreach ($actualites as $actualite) {
            if ($dateNow->format('d-m-y') === $actualite->getDatePublication()->format('d-m-y')) {
                $actualite->setStatut(true);
                $this->manager->flush($actualite);
            } elseif ($dateNow->format('d-m-y') > $actualite->getDateFinPublication()->format('d-m-y')) {
                $actualite->setStatut(false);
                $this->manager->flush($actualite);
            } elseif ($dateNow->format('d-m-y') < $actualite->getDatePublication()->format('d-m-y')) {
                $actualite->setStatut(true);
                $this->manager->flush($actualite);
            }
        }
        return $this->render('Admin/actualites/index.html.twig', [
            'actualites' => $actualites,
            'niveauxMenu' => $niveauxMenu,
            'anneeUniversitaires' => $anneeUniversitaires,
        ]);
    }

    #[Route('creer/', name: 'app_actualite_creer_admin')]
    public function creer(Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $actualite = new Actualite();
        $dateNow = new DateTime();
        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $datePublication = $form->getData()->getDatePublication();
            $dateFinPublication = $form->getData()->getDateFinPublication();

            if ($datePublication->format('d-m-y') === $dateNow->format('d-m-y')) {
                $actualite->setStatut(true);
                $this->manager->persist($actualite);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    "Votre publication apparaît déjà du côté du visiteur."
                );
                return $this->redirectToRoute('app_actualite_creer_admin');
            } elseif ($datePublication->format('d-m-y') > $dateNow->format('d-m-y') and $datePublication->format('d-m-y') > $dateFinPublication->format('d-m-y')) {
                $this->addFlash(
                    'danger',
                    "La date de fin de la publication doit être supérieure aux dates de publication "
                );
                return $this->redirectToRoute('app_actualite_creer_admin');
            } elseif ($datePublication->format('d-m-y') > $dateNow->format('d-m-y')) {
                $actualite->setStatut(false);
                $this->manager->persist($actualite);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    "Votre publication sera visible au coté visiteur à partir de <b>{$actualite->getDatePublication()->format('d F Y')}</b>"
                );
                return $this->redirectToRoute('app_actualites_admin');
            } else {
                $actualite->setStatut(false);
                $this->manager->persist($actualite);
                $this->manager->flush();
            }
        }
        return $this->render('Admin/actualites/creer.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu,
            'anneeUniversitaires' => $anneeUniversitaires
        ]);
    }

    #[Route('/{id}/edit', name: 'app_actualite_edit_admin')]
    public function edit(Actualite $actualite, Request $request): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        $form = $this->createForm(ActualiteType::class, $actualite);
        $form->handleRequest($request);
        $actualites = $this->repoActualite->findAll();
        $dateNow = new DateTime();
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($actualites as $actualite) {
                if ($dateNow->format('d-m-y') > $actualite->getDatePublication()->format('d-m-y')) {
                    $actualite->setStatut(true);
                    $this->manager->flush($actualite);
                } elseif ($dateNow->format('d-m-y') > $actualite->getDateFinPublication()->format('d-m-y')) {
                    dd($actualite);
                    $actualite->setStatut(0);
                    $this->manager->flush($actualite);
                    $this->addFlash(
                        'success',
                        "Vous avez modifier quelque information sur \"ACTUALITE\""
                    );
                }
            }
            return $this->redirectToRoute('app_actualites_admin');
        }
        return $this->render('Admin/actualites/edit.html.twig', [
            'form' => $form->createView(),
            'niveauxMenu' => $niveauxMenu,
            'anneeUniversitaires' => $anneeUniversitaires
        ]);
    }

    #[Route('/{id}/delete', name: 'app_actualites_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Actualite $actualite): Response
    {
        if ($this->isCsrfTokenValid('delete' . $actualite->getId(), $request->request->get('_token'))) {
            $this->repoActualite->remove($actualite, true);
            $this->addFlash(
                'success',
                "Vous avez supprimer une \"ACTUALITE\""
            );
        }

        return $this->redirectToRoute('app_actualites_admin', [], Response::HTTP_SEE_OTHER);
    }
}
