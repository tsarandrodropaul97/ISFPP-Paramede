<?php

namespace App\Controller\Admin;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NiveauRepository;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\AnneeUniversitaireRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/note')]
class NoteController extends AbstractController
{
    private $repoManager;
    private $repoNote;
    private $repoAnneeUniversitaire;
    private $repoNiveau;

    public function __construct(
        EntityManagerInterface $repoManager,
        NoteRepository $repoNote,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoNote = $repoNote;
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }

    // #[Route('/', name: 'app_note_admin')]
    // public function index(): Response
    // {
    //     /* information renvoyé au menu */
    //     $niveauxMenu = $this->repoNiveau->findAll();

    //     $notes = $this->repoNote->findAll();
    //     return $this->render('Admin/resultat/index.note.html.twig', [
    //         'notes' => $notes,
    //         'niveauxMenu' => $niveauxMenu
    //     ]);
    // }

    // #[Route('/creer', name: 'app_note_creer_admin')]
    // public function new(Request $request): Response
    // {
    //     /* information renvoyé au menu */
    //     $niveauxMenu = $this->repoNiveau->findAll();
        
    //     $note = new Note();
    //     $form = $this->createForm(NoteType::class, $note);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $this->repoManager->persist($note);
    //         $this->repoManager->flush();
    //         $this->addFlash(
    //             'success',
    //             "<b> Enregistrement Ok. </b><br>  Vous avez ajouté un nouvau note"
    //         );
    //         return $this->redirectToRoute('app_note_admin');
    //     }
    //     return $this->render('Admin/resultat/creer.note.html.twig', [
    //         'form' => $form->createView(),
    //         'niveauxMenu' => $niveauxMenu
    //     ]);
    // }


    #[Route('/{id}/edit', name: 'app_note_edit_admin', methods: ['GET', 'POST'])]
    public function edit(Request $request, Note $note): Response
    {
          /* information renvoyé au menu */
          $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
          $niveauxMenu = $this->repoNiveau->findAll();

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($note);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_resultat_admin');
        }
        return $this->render('Admin/resultat/edit.note.html.twig', [
            'form' => $form->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu
        ]);
    }


    #[Route('/{id}', name: 'app_note_delete_admin', methods: ['POST'])]
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete' . $note->getId(), $request->request->get('_token'))) {
            $this->repoNote->remove($note, true);
            $this->addFlash(
                'success',
                "<b> Supprimer Ok. </b><br>  Vous avez supprimé le note"
            );
        }
        return $this->redirectToRoute('app_note_admin', [], Response::HTTP_SEE_OTHER);
    }
}
