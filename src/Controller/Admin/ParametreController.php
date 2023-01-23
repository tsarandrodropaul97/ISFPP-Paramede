<?php

namespace App\Controller\Admin;

use App\Entity\Frais;
use App\Entity\Inscription;
use App\Entity\Niveau;
use App\Form\FraisType;
use App\Form\InscriptionType;
use App\Form\NiveauType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\FraisRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NiveauRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/parametre')]
class ParametreController extends AbstractController
{
    private $repoManager;
    private $repoInscription;
    private $repoNiveau;
    private $repoFrais;
    private $repoAnneeUniversitaire;
    public function __construct(
        EntityManagerInterface $repoManager,
        InscriptionRepository $repoInscription,
        NiveauRepository $repoNiveau,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        FraisRepository $repoFrais
    ) {
        $this->repoManager = $repoManager;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoInscription = $repoInscription;
        $this->repoNiveau = $repoNiveau;
        $this->repoFrais = $repoFrais;
    }

    #[Route('/', name: 'app_parametre_admin')]
    public function index(Request $request): Response
    {

        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();


        /* Inscription debut */
        $dateInscription = $this->repoInscription->findBy([], ['createdAt' => 'DESC'], [1]);
        $listeDateInscription = $this->repoInscription->findAll();
        $inscription =  new Inscription;
        $formInscrption = $this->createForm(InscriptionType::class, $inscription);
        $formInscrption->handleRequest($request);

        if ($formInscrption->isSubmitted() && $formInscrption->isValid()) {
            $dateDebut = $formInscrption->getData()->getDateDebut();
            $dateFin = $formInscrption->getData()->getDateFin();

            if ($dateDebut->format('d-m-y') >= $dateFin->format('d-m-y')) {
                $this->addFlash(
                    'danger',
                    " <b>Oups :X!, Enregistrement échoué.</b><br> La date de fermeture de l'inscription doit être supérieur au date d'ouverture. <br>Veuillez essayer à nouveau. "
                );
                return $this->redirectToRoute('app_parametre_admin');
            } else {
                $this->repoManager->persist($inscription);
                $this->repoManager->flush();
                $this->addFlash(
                    'success',
                    "Votre inscription commence a partir de <b>{$dateDebut->format('d F y')} </b>  jusqu'au <b>{$dateFin->format('d F y')} </b> "
                );
                return $this->redirectToRoute('app_parametre_admin');
            }
        }

        /* Inscription Fin */

        /* Niveau debut */
        $niveau = new Niveau;
        // niveauD = Niveau qui n'a pas de droit
        $niveauD = $this->repoNiveau->findBy(['statut' => false]);
        $formNiveau = $this->createForm(NiveauType::class, $niveau);
        $formNiveau->handleRequest($request);
        if ($formNiveau->isSubmitted() && $formNiveau->isValid()) {
            $niveau->setStatut(false);
            $this->repoManager->persist($niveau);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez crée un niveau dans votre établissement "
            );
            return $this->redirectToRoute('app_parametre_admin');
        }

        /* Niveau Fin */

        /* Frais Debut */
        $fraisParNiveau = $this->repoFrais->findAll();
        $frais = new Frais;
        $formFrais = $this->createForm(FraisType::class, $frais);
        $formFrais->handleRequest($request);
        if ($formFrais->isSubmitted() && $formFrais->isValid()) {
            $this->repoManager->persist($frais);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Enregistrement Ok. </b><br>  Vous avez crée un niveau dans votre établissement "
            );
            return $this->redirectToRoute('app_parametre_admin');
        }
        /* Frais debut */

        return $this->render('Admin/parametre/index.html.twig', [
            'formInscrption' => $formInscrption->createView(),
            'formNiveau' => $formNiveau->createView(),
            'formFrais' => $formFrais->createView(),
            'dateInscription' => $dateInscription,
            'niveauD' => $niveauD,
            'fraisParNiveau' => $fraisParNiveau,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'listeDateInscription' => $listeDateInscription
        ]);
    }
}
