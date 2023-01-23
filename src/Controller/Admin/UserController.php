<?php

namespace App\Controller\Admin;

use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use App\Form\UserType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\NiveauRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormError;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends AbstractController
{
    private $repoManager;
    private $repoNiveau;
    private $repoUser;
    private $repoAnneeUniversitaire;


    public function __construct(
        UserRepository $repoUser,
        EntityManagerInterface $repoManager,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        NiveauRepository $repoNiveau
    ) {
        $this->repoUser = $repoUser;
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoNiveau = $repoNiveau;
    }

    #[Route('/admin/user', name: 'app_user')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->repoManager->persist($user);
            $this->repoManager->flush();
            return $this->redirectToRoute('app_user');
        }

        $userConnect = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $formSecurity = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $formSecurity->handleRequest($request);
        if ($formSecurity->isSubmitted() && $formSecurity->isValid()) {
            if (!password_verify($passwordUpdate->getOldPassword(), $userConnect->getPassword())) {
                $formSecurity->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
                $this->addFlash(
                    'danger',
                    "Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"
                );
            } else {
                $newPassword = $passwordUpdate->getNewPassword();

                $userConnect->setPassword($passwordHasher->hashPassword($userConnect, $newPassword));

                $this->repoManager->persist($user);
                $this->repoManager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien modifier"
                );
            }
        }
        return $this->render('Admin/user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'formSecurity' => $formSecurity->createView(),
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu

        ]);
    }

    #[Route('/admin/developeur/', name: 'app_developpeur_admin')]
    public function developpeur(): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        return $this->render('Admin/user/developpeur.html.twig', [

            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu

        ]);
    }
}
