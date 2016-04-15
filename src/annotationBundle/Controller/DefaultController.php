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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

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


}
