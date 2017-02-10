<?php



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Joueur;
use AppBundle\Entity\Personnage;
use AppBundle\Entity\Stats;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


/**
 * Description of PlayersController
 *
 * @author loic
 */
class PlayersController extends Controller {

    /**
     * Methode qui va ajouter les joueurs en base de données
     * A la fin du traitement on est rediriger sur le controlleur de vue
     * afin de retourner la vue de creation de personnages
     * 
     * Si le joueur existe en base de donnée, on le met en session
     * sinon on l'enregistre, et on le met en session.
     * @Route("/players/add",name="addPlayers")
     * @Method({"POST"})
     * @param \Request $r
     */
    public function addPlayers(Request $r) {
        $entityManager = $this->getDoctrine()->getManager();
        //boucle sur valeurs de 1 à 4
        for ($i = 1; $i <= 4; $i++) {
            //stockage de la valeur dans la variabe email
            $email = $r->get('j' . strval($i));
            if ($email != null) {
                $joueurs = $this->getDoctrine()->getRepository(Joueur::class)->findByEmail($email);
                if ($joueurs != null) {
                    $joueur = $joueurs[0];
//                    return new Response($joueurs[0]->getEmail());
                } else {
                    //si nouveau joueur
                    $joueur = new Joueur();
                    $joueur->setEmail($email);
                    $entityManager->persist($joueur);
                }
                //mise en session du joueur
                $r->getSession()->set('j' . strval($i), $joueur);
            }
        }
        $entityManager->flush();
        $r->getSession()->set('actuel', 1);
        return $this->redirectToRoute('createPerso');
    }

    /**
     * Doit etre appelée par la validation de la création du personnage precendent !
     * @param Request $r
     * @return type
     */
    public function switchPlayer(Request $r) {
        $next = $r->getSession()->get('actuel') + 1;
        if ($r->getSession()->has('j' . strval($next))) {
            $r->getSession()->set('actuel', $next);
            return $this->redirectToRoute('createPerso');
        } else {
            return $this->redirectToRoute('game');
        }
    }
    /**
     * 
     * @Route ("/personnage/create",name="createPerso")
     *     
     */
    public function createPerso(Request $r) {
        $personnage = new Personnage;
        $form = $this->createForm("AppBundle\Form\PersonnageType", $personnage);
        $form->handleRequest($r);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $stats = new Stats();
            
            $stats->setPv(
                    $personnage->getRace()->getStats()->getPv() +
                    $personnage->getClasse()->getStats->getPv());
            $stats->setMov(
                    $personnage->getRace()->getStats()->getMov() +
                    $personnage->getClasse()->getStats->getMov());
            $stats->setAtt(
                    $personnage->getRace()->getStats()->getAtt() +
                    $personnage->getClasse()->getStats->getAtt());
            $stats->setDef(
                    $personnage->getRace()->getStats()->getDef() +
                    $personnage->getClasse()->getStats->getDef());
        
        $em->persist($stats);
        
        $personnage->setStats($stats);
        $em->persist($personnage);
        $r->getSession()->set('actuel',1);
                $em->flush();
    }
return $this->redirectToRoute('createPerso');

}        

}

