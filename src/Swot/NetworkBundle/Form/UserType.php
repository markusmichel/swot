<?php

namespace Swot\NetworkBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('username')
            ->add('birthdate')
            ->add('gender', 'choice', array(
                'choices' => array(
                    'm' => 'male',
                    'f' => 'female'
                ),
                'expanded' => true
            ))
            //->add('profileImage')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password')
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\NetworkBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swot_networkbundle_user';
    }
}
