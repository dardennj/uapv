<?php

namespace Dosi\TheseFondationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Dosi\TheseFondationBundle\Form\DocumentType;



class CandidatType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom','text', array('label'  => 'Nom :'));
        $builder->add('prenom','text', array('label'  => 'Prénom :'));
        $builder->add('email', 'email', array('label'  => 'Courriel :'));
        $builder->add('adresse','textarea',array('label'  => 'Adresse : ', 'attr' => array('class'=>'form-control editor','rows' => '3','cols' => '10'),'required'  => true));
        $builder->add('tel', 'text', array('label'  => 'Téléphone :'));
        $builder->add('these_titre','textarea',array('attr' => array('class'=>'form-control editor','rows' => '2','cols' => '10'),'required'  => true));
        $builder->add('these_resume','textarea',array('attr' => array('class'=>'form-control editor','rows' => '5','cols' => '10'),'required'  => true,'max_length'=>"1400"));
        $builder->add('these_adeq','textarea',array('attr' => array('class'=>'form-control editor','rows' => '5','cols' => '10'),'required'  => true,'max_length'=>"1400"));
        $builder->add('these_prog','textarea',array('attr' => array('class'=>'form-control editor','rows' => '20','cols' => '10'),'required'  => true,'max_length'=>"9800"));
        $builder->add('these_retour','textarea',array('attr' => array('class'=>'form-control editor','rows' => '15','cols' => '10'),'required'  => true,'max_length'=>"1400"));
        $builder->add('these_struct','textarea',array('attr' => array('class'=>'form-control editor','rows' => '2','cols' => '10'),'required'  => true));
        $builder->add('these_unite','textarea',array('attr' => array('class'=>'form-control editor','rows' => '3','cols' => '10'),'required'  => true));
        $builder->add('these_directeur','textarea',array('attr' => array('class'=>'form-control editor','rows' => '3','cols' => '10'),'required'  => true));
        $builder->add('these_PB','checkbox',array('required'  => false));
        $builder->add('these_JHF','checkbox',array('required'  => false));
        $builder->add('diplomes', new DocumentType());
        $builder->add('lettre_dir_these', new DocumentType());
        $builder->add('lettre_dir_unite', new DocumentType());


    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Dosi\TheseFondationBundle\Entity\Candidat'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'candidat';
    }
}
