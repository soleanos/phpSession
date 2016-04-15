<?php


namespace annotationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use annotationBundle\Entity\Personne;
use annotationBundle\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom','text')
            ->add('prenom','text')
            ->add('save','submit',array('label'=>'Ajouter une personne'))
            ->getForm();

    }
}