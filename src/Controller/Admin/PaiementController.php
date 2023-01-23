<?php

namespace App\Controller\Admin;

use App\Entity\DateEcolage;
use App\Entity\Inscription;
use App\Entity\Paiement;
use App\Form\DateEcolageType;
use App\Form\MoisType;
use App\Repository\AnneeUniversitaireRepository;
use App\Repository\EtudiantRepository;
use App\Repository\FraisRepository;
use App\Repository\InscriptionRepository;
use App\Repository\NiveauRepository;
use App\Repository\PaiementRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Security("is_granted('ROLE_ADMIN')")
 */
#[Route('/admin/paiment')]
class PaiementController extends AbstractController
{
    private $repoManager;
    private $repoNiveau;
    private $repoFrais;
    private $repoEtudiant;
    private $repoPaiement;
    private $repoInscription;
    private $repoAnneeUniversitaire;

    public function __construct(
        PaiementRepository $repoPaiement,
        EntityManagerInterface $repoManager,
        NiveauRepository $repoNiveau,
        FraisRepository $repoFrais,
        EtudiantRepository $repoEtudiant,
        AnneeUniversitaireRepository $repoAnneeUniversitaire,
        InscriptionRepository $repoInscription
    ) {
        $this->repoManager = $repoManager;
        $this->repoNiveau = $repoNiveau;
        $this->repoFrais = $repoFrais;
        $this->repoEtudiant = $repoEtudiant;
        $this->repoAnneeUniversitaire = $repoAnneeUniversitaire;
        $this->repoPaiement = $repoPaiement;
        $this->repoInscription = $repoInscription;
    }

    #[Route('/{idNiveau}/', name: 'app_paiement_admin')]
    public function index($idNiveau): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();
        $annee = date("Y");
        $anneeProchaine = date('Y', strtotime($annee . ' + 1 years'));
        $anneeProchaine = date('Y', strtotime(' + 1 years'));
        $etudiants = $this->repoPaiement->findDistinctEtudiant(['niveau'  => $idNiveau]);
        $niveau = null;
        foreach ($etudiants as $etudiant => $cle) {
            $niveau = $cle['niveau'];
            break;
        }

        $date = new DateTime();
        $dateNow  = $date->format('Y-m-d');

        /* Effectif ecolage normale */
        $pNormale = $this->repoPaiement->findEcolage($idNiveau);
        $paiementNormale = [];
        foreach ($pNormale as $p) {
            $dernierPaiement = $p["dernierPaiment"];
            $dateP = substr($dernierPaiement, 0, -3);
            $dateMaintenant = $date->format('Y-m');
            if ($dateP == $dateMaintenant) {
                $paiementNormale[] = $dateP;
            }
        }
        $paiementNormale = count($paiementNormale);
        /* FIN Effectif ecolage normale */

        /* Effectif ecolage Avancer */
        $dateAvance = date('Y-m', strtotime($dateNow . ' +1 month'));
        $pAvance = $this->repoPaiement->findEcolageAvance($dateAvance, $idNiveau);
        $paimentAvance = [];
        foreach ($pAvance as $p) {
            $dernierPaiement = $p["dernierPaiment"];
            $dernierPaiement = substr($dernierPaiement, 0, -3);
            if ($dernierPaiement >= $dateAvance) {
                $paimentAvance[] = $dernierPaiement;
            }
        }
        $paimentAvance = count($paimentAvance);
        /* FIN Effectif ecolage Avancer */


        /* Effectif ecolage retard un mois */
        $dateRetardUnMoi = date('Y-m', strtotime($dateNow . ' - 1 month'));
        $paimentRetard = [];
        $pRetard = $this->repoPaiement->findEcolage($idNiveau);
        foreach ($pRetard as $p) {
            $dernierPaiement = $p["dernierPaiment"];
            $dernierPaiement = substr($dernierPaiement, 0, -3);
            if ($dernierPaiement == $dateRetardUnMoi) {
                $paimentRetard[] = $dernierPaiement;
            }
        }
        $paimentRetard = count($paimentRetard);
        /* Effectif ecolage retard un moi */


        $paimentRetardTrop = [];
        foreach ($pRetard as $p) {
            $dernierPaiement = $p["dernierPaiment"];
            $dernierPaiement = substr($dernierPaiement, 0, -3);
            if ($dernierPaiement < $dateRetardUnMoi) {
                $paimentRetardTrop[] = $dernierPaiement;
            }
        }

        $paimentRetardTrop = count($paimentRetardTrop);

        return $this->render('Admin/paiement/index.html.twig', [
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'niveau' => $niveau,
            'etudiants' => $etudiants,
            'anneeProchaine' => $anneeProchaine,
            'annee' => $annee,
            'paiementNormale' => $paiementNormale,
            'paimentRetard' => $paimentRetard,
            'paimentRetardTrop' => $paimentRetardTrop,
            'paimentAvance' => $paimentAvance
        ]);
    }

    #[Route('/payer', name: 'app_paiement_frais', methods: "POST")]
    public function payer(Request $request)
    {
        if ($request->isMethod('POST')) {
            $donnee = $request->request->all();
            $etudiant_id = $donnee['etudiant'];
            $frais_id = $donnee['frais'];
            $niveau_id = $donnee['niveau'];
            $dateMoi = $donnee['dateMois'];
            $datePaiment = new DateTime();
            $annee = $donnee['annee'];

            $dateNow = new DateTime();
            $jour  = $dateNow->format('d');

            $payer = $annee . "-" . $dateMoi . "-" . $jour;
            $stringDate = strtotime($payer);
            $ecolagePayer = date('y-m-d', $stringDate);
            $dte = new DateTime($ecolagePayer);

            $etudiant = $this->repoEtudiant->findOneBy(['id' => $etudiant_id]);
            $frais = $this->repoFrais->findOneBy(['id' => $frais_id]);
            $niveau = $this->repoNiveau->findOneBy(['id' => $niveau_id]);
            $dateMoi = $dateMoi;

            $paiement = new Paiement();
            $paiement->setEtudiant($etudiant);
            $paiement->setPayer($dte);
            $paiement->setNiveau($niveau);
            $paiement->setFrais($frais);
            $paiement->setDatePaiment($datePaiment);

            $dernierPaiment = $this->repoPaiement->findDernierPaiment($niveau_id, $etudiant_id);
            $eleve = $this->repoPaiement->findBy(['etudiant' => $etudiant_id, 'payer' => $dte]);
            if (empty($eleve)) {

                foreach ($dernierPaiment as $p) {
                    $dernierP = $p['dernierPaiment'];
                    $dernierP = substr($dernierP, 0, -3);
                    $datePaimentSuivant = date('Y-m', strtotime($dernierP . ' + 1 month'));

                    $paiementSoumettre = $dte->format('Y-m');
                    if ($datePaimentSuivant == $paiementSoumettre) {
                        $this->repoManager->persist($paiement);
                        $this->repoManager->flush();

                        $this->addFlash(
                            'success',
                            "<b> :) Enregistrement OK!</b><br> <b>{$etudiant->getNom()}</b> a payé son frais pour le moi de <b>{$paiement->getPayer()->format('F Y')}<b/> "
                        );
                    } elseif ($datePaimentSuivant < $paiementSoumettre) {

                        $datePaimentSuivant = strtotime($datePaimentSuivant);
                        $paimentS = date('y-m-d', $datePaimentSuivant);
                        $paimentSuivant = new DateTime($paimentS);
                        $this->addFlash(
                            'danger',
                            "<b> :x Oups! Désolé</b><br> <b>{$etudiant->getNom()}</b> doit payer le frais d'ecolage moi de <b>'{$paimentSuivant->format('F Y')}'<b/> "
                        );
                    } else {
                        $this->addFlash(
                            'danger',
                            "<b> :x Oups! Désolé</b><br> <b>{$etudiant->getNom()}</b> a déjà payé son frais pour <b>{$dte->format('F Y')}<b/> "
                        );
                    }
                }
            } else {
                //date de paiment de frais d'ecolage qui est déja fait;
                $datePayer = null;
                foreach ($eleve as $el) {
                    $datePayer = $el->getDatePaiment()->format('F Y');
                }
                $this->addFlash(
                    'danger',
                    "<b> :x Oups! Désolé</b><br> <b>{$etudiant->getNom()}</b> a déjà payé son frais pour <b>{$datePayer}<b/> "
                );
            }
        }
        return $this->redirectToRoute('app_paiement_admin', ['idNiveau' => $niveau->getId()]);
    }

    #[Route('/{idEtudiant}/voir', name: 'app_paiement_voir_admin')]
    public function voir($idEtudiant): Response
    {
        /* information renvoyé au menu */
        $anneeUniversitaires = $this->repoAnneeUniversitaire->findAll();
        $niveauxMenu = $this->repoNiveau->findAll();

        $ecolagePayerParEtudiant = $this->repoPaiement->paiementParEtudiant($idEtudiant);
        $paiement = $this->repoPaiement->findBy(['etudiant' => $idEtudiant]);
        $dernierPaiment = $this->repoPaiement->dernierPaiment($idEtudiant);
        $dateDernierPaiement = null;
        foreach ($dernierPaiment as $dernierP) {
            $dateDernierPaiement = $dernierP['dernierPaiment'];
            $dateDernierPaiement = substr($dateDernierPaiement, 3, 7);
        }
        $nom = null;
        $prenom = null;
        $matricule = null;
        $nomPhoto = null;
        foreach ($paiement as $etudiant) {
            $nom = $etudiant->getEtudiant()->getNom();
            $prenom = $etudiant->getEtudiant()->getPrenom();
            $matricule = $etudiant->getEtudiant()->getMatricule();
            $nomPhoto = $etudiant->getEtudiant()->getFilename();
            break;
        }
        $dateNow = new DateTime();
        $dateNow1  = $dateNow->format('m-Y');
        $impayer = [];
        $moi = $dateNow->format('m');
        $moiNow = intval($moi);
        foreach ($ecolagePayerParEtudiant as $ecolagePayer) {
            $dateDernierPaiment = $ecolagePayer['ecolagePayer'];
            $dateDernierPaiment = substr($dateDernierPaiment, 3, 7);
            if ($dateDernierPaiement < $dateNow1) {
                $moiDernierPaiment = substr($dateDernierPaiement, 0, 2);
                $moiDernierPaiment = intval($moiDernierPaiment);
                for ($i = 1; $i < $moiNow; $i++) {
                    $date = new DateTime();
                    $dateNowReduire = $date->modify(' -' . $i . 'month');
                    $moiReduire = $dateNowReduire->format('m');
                    $moiReduire = intval($moiReduire);
                    if ($moiDernierPaiment == $moiReduire) {
                        break;
                    } else {
                        $impayer[] = $dateNowReduire;
                    }
                }
                break;
            }
        }
        return $this->render('Admin/paiement/voir.html.twig', [
            'anneeUniversitaires' => $anneeUniversitaires,
            'niveauxMenu' => $niveauxMenu,
            'paiement' => $paiement,
            'nom' => $nom,
            'prenom' => $prenom,
            'matricule' => $matricule,
            'nomPhoto' => $nomPhoto,
            'ecolagePayerParEtudiant' => $ecolagePayerParEtudiant,
            'impayer' => $impayer
        ]);
    }

    #[Route('/{idEtudiant}/delete', name: 'app_paiement_annuler_admin', methods: ['POST'])]
    public function annuler(Request $request, $idEtudiant): Response
    {

        if ($request->isMethod('POST')) {
            $donnee = $request->request->all();
            $dernierP = $donnee['dernierPaimenet'];

            $dte = new DateTime($dernierP);
            $paiement = $this->repoPaiement->findOneBy(['etudiant' => $idEtudiant, 'payer' => $dte]);
            $this->repoManager->remove($paiement);
            $this->repoManager->flush();
            $this->addFlash(
                'success',
                "<b> Annulation Ok!</b><br> Vous avez annulerrr le paiment d\'ecolage moi <b><b>{$datePayer}<b/> "
            );
            return $this->redirectToRoute('app_paiement_admin', ['idNiveau' => $paiement->getNiveau()->getId()]);
        }
    }
}
