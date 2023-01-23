<?php

namespace App\Controller\Admin;

use DateTime;
use App\Form\PartenairesType;
use App\Entity\RechercheParAnnee;
use App\Form\RechercheParAnneeType;
use App\Repository\FraisRepository;
use App\Repository\NiveauRepository;
use App\Repository\EtudiantRepository;
use App\Repository\PaiementRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PartenairesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AnneeUniversitaireRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/dashboard')]
class DashboardController extends AbstractController
{
    private $repoNiveau;
    private $repoManager;
    private $repoPartenaires;
    private $repoEtudiants;
    private $repoFrais;
    private $repoAnneeUniversitaire;
    private $repoPaiements;

    public function __construct(
        EntityManagerInterface $repoManager,
        PartenairesRepository $repoPartenaires,
        NiveauRepository $repoNiveau,
        EtudiantRepository $repoEtudiants,
        FraisRepository $repoFrais,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        PaiementRepository $repoPaiements

    ) {
        $this->repoManager = $repoManager;
        $this->repoPartenaires = $repoPartenaires;
        $this->repoNiveau = $repoNiveau;
        $this->repoEtudiants = $repoEtudiants;
        $this->repoFrais = $repoFrais;
        $this->repoPaiements = $repoPaiements;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(Request $request): Response
    {
        /* information renvoyé au menu */
        $niveauxMenu = $this->repoNiveau->findAll();
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $paiements = $this->repoPaiements->findAll();

        $ecolageTotal = 0;
        $droitTotal = 0;
        foreach ($paiements as $f) {
            $ecolage =  $f->getFrais()->getEcolage();
            $droit = $f->getFrais()->getDroit();
            $ecolageTotal = $ecolageTotal + $ecolage;
            $droitTotal = $droitTotal + $droit;
        }
        $etudiants = $this->repoEtudiants->findAll();
        $nbrEtudiants = count($etudiants);
        $partenaires = $this->repoPartenaires->findAll();
        $nbrPartenaire = count($partenaires);

        $mois = [
            0 => 1,
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
            6 => 7,
            7 => 8,
            8 => 9,
            9 => 10,
            10 => 11,
            11 => 12,
        ];

        $date = new DateTime();
        $an  = $date->format('Y');
        $dateMois = [];
        foreach ($mois as $moi) {
            $dateMoi  = $this->repoPaiements->recetteParMoi($moi, $an);
            foreach ($dateMoi as $date) {
                if ($date["somme"] == null) {
                    $date["somme"] = "0";
                    $dateMois[] = $date["somme"];
                } else {
                    $dateMois[] = $date["somme"];
                }
            }
        }

        $annee = new RechercheParAnnee();
        $form = $this->createForm(RechercheParAnneeType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            unset($dateMois);
            $ecolageTotal = 0;
            $droitTotal = 0;
            $annee->getAn();
            foreach ($mois as $moi) {
                $dateMoi  = $this->repoPaiements->recetteParAnnee($moi, $annee);
                foreach ($dateMoi as $date) {
                    dump($date);
                    if ($date["somme"] == null) {
                        $date["somme"] = "0";
                        $dateMois[] = $date["somme"];
                    } else {
                        $dateMois[] = $date["somme"];
                        $ecolageTotal = $ecolageTotal + $date["somme"];
                        $droitTotal = $droitTotal + $date["sommeDroit"];
                    }
                }
            }
        }
        return $this->render('Admin/dashboard/index.html.twig', [
            'nbrPartenaire' => $nbrPartenaire,
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'nbrEtudiants' => $nbrEtudiants,
            'ecolageTotal' => $ecolageTotal,
            'dateMois' => $dateMois,
            'droitTotal' => $droitTotal,
            'form' => $form->createView()
        ]);
    }
}
