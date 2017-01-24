<?php

namespace CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommandeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateVisite', DateType::class)
            ->add('typeBillet',ChoiceType::class, array(
                'choices' => array('Journée' => "Journée", "Demi-journée" => "Demi-journée"),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('nbBillets', RangeType::class, array(
                'attr' => array(
                    'min' => 1,
                    'max' => 30
                )
            ))
            ->add('mail', RepeatedType::class, array(
                'type' => EmailType::class,
                'invalid_message' => 'Les deux adresses mail doivent correspondre.',
                'first_options' => array('label' => 'Adresse mail'),
                'second_options' => array('label' => 'Confirmez l\'adresse mail')
            ))
            ->add('valider', SubmitType::class)
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CoreBundle\Entity\Commande'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'corebundle_commande';
    }


}
