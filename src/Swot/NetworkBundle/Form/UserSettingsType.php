<?php

namespace Swot\NetworkBundle\Form;

use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Security\AccessType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\Translator;

class UserSettingsType extends AbstractType
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
            ->add('birthdate', 'birthday')
            ->add('gender', 'choice', array(
                'choices' => array(
                    'm' => 'male',
                    'f' => 'female'
                ),
                'expanded' => true
            ))
            ->add('accessLevel', 'choice', array(
                'choices' => array(
                    AccessType::ACCESS_TYPE_PRIVATE       => 'settings.access_type.private',
                    AccessType::ACCESS_TYPE_RESTRICTED    => 'settings.access_type.restricted',
                    AccessType::ACCESS_TYPE_PUBLIC        => 'settings.access_type.public'
                ),
                'translation_domain' => 'messages',
            ))
            ->add('profileImageFile')
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
        return 'user_settings';
    }
}
