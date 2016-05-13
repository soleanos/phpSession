<?php

namespace annotationBundle\Controller;
use annotationBundle\Form\Type\PersonneType;
use annotationBundle\Form\Type\SessionType;
use annotationBundle\Entity\Personne;
use annotationBundle\Entity\Session;
use annotationBundle\Form\Type\TaskType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
    * @Route("/")
    */
    public function indexAction()
    {
        return $this->render('annotationBundle:Default:index.html.twig');
    }

    /**
     * @Route("/personne/ajout",name = "ajout personne")
     */
    public function ajoutAction(Request $request)
    {
        $personne = new Personne();
        $form = $this->createForm(PersonneType::class, $personne);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em= $this->getDoctrine()->getManager();
            $em->persist($personne);
            $em->flush();
            return $this->redirectToRoute('ajout personne');
        }

        $em= $this->getDoctrine()->getManager();
        $liste = $em->getRepository('annotationBundle:Personne')->findAll();


        return $this->render('annotationBundle:Default:ajout.html.twing',
        array('form'=> $form->createView(),
                'liste'=>$liste,
        ));
    }


    /**
     * @Route("/session/ajout",name = "ajout session")
     */
    public function ajoutSessionAction(Request $request)
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $em= $this->getDoctrine()->getManager();
            $em->persist($session);
            $em->flush();
            return $this->redirectToRoute('ajout session');
        }

        $em= $this->getDoctrine()->getManager();
        $liste = $em->getRepository('annotationBundle:Session')->findAll();


        return $this->render('annotationBundle:Default:ajoutSession.html.twing',
            array('form'=> $form->createView(),
                'liste'=>$liste,
            ));
    }


    /**
     * @Route("/session/{id}/detail",name = "detail session")
     * @ParamConverter("session",class="annotationBundle:Session")
     */

    public function detailSessionAction(Request $request,Session $session)
    {
            $form = $this->createFormBuilder($session)
            ->add('enseignant', EntityType::class, array(
                'class' => 'annotationBundle\Entity\Personne',
                'choice_label' => 'NomPrenom',
            ))
            ->add('save','submit',array('label'=>'Ajouter un enseignant à cette session'))
            ->getForm();

            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $em= $this->getDoctrine()->getManager();
                $em->persist($session);
                $em->flush();
                return $this->redirectToRoute('detail session');
            }

            return $this->render('annotationBundle:Default:ajoutEnseignantSession.html.twing',
            array('form'=> $form->createView(),));

    }

    // MON API ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    //PERSONNES

    //GET ALL

    /**
     * @Route("/api/personne/",name = "get personnes")
     * @Method("GET")
     */

    public function getPersonnes(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        $liste = $em->getRepository('annotationBundle:Personne')->findAll();
        $personneToSerialize = (object) array();
        $collectionPersonneToSerialize = array();
        $listSession = array();

        foreach ($liste as $personne) {
            $personneToSerialize->_self = "http://127.0.0.1:8000/personne/".$personne->getId()."/detail";
            $personneToSerialize->id = $personne->getId();
            $personneToSerialize->nom = $personne->getNom();
            $personneToSerialize->prenom = $personne->getPrenom();


            foreach ($personne->getsession() as $session) {
                $listSession[] = "http://127.0.0.1:8000/session/".$session->getId()."/detail";
            }

            $personneToSerialize->sessions = $listSession;
            $collectionPersonneToSerialize[] =  $personneToSerialize;

        }
        return new Response(json_encode($collectionPersonneToSerialize));
    }


    // GET ONE

    /**
     * @Route("/api/personne/{id}",name = " get personne")
     * @Method("GET")
     */

    public function getPersonne(Request $request,$id)
    {
            $em= $this->getDoctrine()->getManager();

            $personne = $em->getRepository('annotationBundle:Personne')->findOneById($id);

            $personneToSerialize = (object) array();

            $personneToSerialize->_self = "http://127.0.0.1:8000/personne/".$personne->getId()."/detail";
            $personneToSerialize->id = $personne->getId();
            $personneToSerialize->nom = $personne->getNom();
            $personneToSerialize->prenom = $personne->getPrenom();

            foreach ($personne->getsession() as $session) {
                $listSession[] = 'http://127.0.0.1:8000/session/'.$session->getId().'/detail';
            }

            return new Response(json_encode($personneToSerialize,JSON_UNESCAPED_SLASHES));

    }

    //ADD ONE

    /**
     * @Route("/api/personne/",name = "add personne")
     * @Method("POST")
     */

    public function addPersonne(Request $request)
    {
        $content = $request->getContent();
        $em= $this->getDoctrine()->getManager();

        if (!empty($content))
        {
            $param = (object) array();
            $param = json_decode($content);

            $personne = new Personne();
            $personne->setNom($param->nom);
            $personne->setPrenom($param->prenom);

            $em->persist($personne);
            $em->flush();

            return new Response("L'utilisateur ".$personne->getNomPrenom()." a été correctement crée");

        }else{
            return new Response("no json Person sended");

        }

    }

    //DELETE ONE

    /**
     * @Route("/api/personne/{id}",name = "delete_personne")
     * @Method("DELETE")
     */

    public function deletePersonne(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();

        $personne = $em->getRepository('annotationBundle:Personne')->findOneById($id);

        if (!$personne) {
            return new Response("bad id, person not deleted");
        }else{

            $em->remove($personne);
            $em->flush();

            return new Response("perso succesfully deleted ");

        }

    }

    //PUT ONE

    /**
     * @Route("/api/personne/{id}",name = "put personne")
     * @Method("PUT")
     */

    public function updatePersonne(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $content = $request->getContent();

        if (!empty($content)){

            $fieldsToUpdate = (object) array();
            $fieldsToUpdate = json_decode($content);
            $personne = $em->getRepository('annotationBundle:Personne')->findOneById($id);

            $form = $this->createFormBuilder($personne);

            foreach($fieldsToUpdate as $key => $value) {
                    if ($personne) {
                        $form->get($key)->submit($value);
                    }
            }

            if ($form->isSubmitted() && $form->isValid()){
                $em= $this->getDoctrine()->getManager();
                $em->persist($personne);
                $em->flush();
                return new Response("http://127.0.0.1:8000/personne/".$personne->getId()."/detail");
            }else{
                return new Response("nope.")
            ;}

        }else{

            return new Response("Pas de donnée envoyée, l'update a échoué");
        }

    }


    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ SESSION ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    //GET ALL

    /**
     * @Route("/api/session/",name = "get sessions")
     * @Method("GET")
     */

    public function getSessions(Request $request)
    {
        $em= $this->getDoctrine()->getManager();
        $liste = $em->getRepository('annotationBundle:Session')->findAll();
        $sessionToSerialize = (object) array();
        $collectionPersonneToSerialize = array();
        $listPersonne = array();

        foreach ($liste as $session) {
            $sessionToSerialize->_self = "http://127.0.0.1:8000/session/".$session->getId()."/";
            $sessionToSerialize->id = $session->getId();
            $sessionToSerialize->intitule = $session->getIntitule();
            $sessionToSerialize->dateDebut = $session->getDateDebut();
            $sessionToSerialize->dateFin = $session->getDateFin();
            $sessionToSerialize->enseignant = $session->getEnseignant();

            foreach ($session->getEtudiants() as $etudiant) {
                $listPersonne[] = "http://127.0.0.1:8000/personne/".$etudiant->getId();
            }

            $sessionToSerialize->sessions = $listPersonne;
            $collectionSessionToSerialize[] =  $sessionToSerialize;

        }
        return new Response(json_encode($collectionSessionToSerialize));
    }

    // GET ONE

    /**
     * @Route("/api/session/{id}",name = "api_session")
     * @Method("GET")
     */

    public function getSession(Request $request,$id)
    {
        $em= $this->getDoctrine()->getManager();
        $sessionToSerialize = (object) array();
        $listEtudiants[] = array();
        $session = $em->getRepository('annotationBundle:Session')->findOneById($id);

        $sessionToSerialize->_self = "http://127.0.0.1:8000/session/".$session->getId()."/";
        $sessionToSerialize->id = $session->getId();
        $sessionToSerialize->intitule = $session->getIntitule();
        $sessionToSerialize->dateDebut = $session->getDateDebut();
        $sessionToSerialize->dateFin = $session->getDateFin();
        $sessionToSerialize->enseignant = $session->getEnseignant();

        foreach ($session->getEtudiants() as $etudiant) {
            $listEtudiants[] = "http://127.0.0.1:8000/personne/".$etudiant->getId();
        }

        $sessionToSerialize->etudiants = $listEtudiants;


        return new Response(json_encode($sessionToSerialize,JSON_UNESCAPED_SLASHES));

    }



}
