<?php

// src/AppBundle/Form/Type/TaskType.php
namespace annotationBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use annotationBundle\Entity\Personne;
use annotationBundle\Entity\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('intitule','text')
            ->add('dateDebut','date')
            ->add('dateFin','date')
            ->add('enseignant', EntityType::class, array(
                'class' => 'annotationBundle\Entity\Personne',
                'choice_label' => 'NomPrenom',
            ))
            ->add('etudiants', EntityType::class, array(
                'class' => 'annotationBundle\Entity\Personne',
                'choice_label' => 'NomPrenom',
                'multiple' => true
            ))
            ->add('save','submit',array('label'=>'Ajouter une personne'))
            ->getForm();

    }
}