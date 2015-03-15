<?php

namespace Swot\NetworkBundle\Form;

use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Entity\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * Use this form type if the user wants to write a new message to a user who is currently unknown.
 *
 * Class NewMessageType
 * @package Swot\NetworkBundle\Form
 */
class NewMessageToUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', 'textarea')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\NetworkBundle\Entity\Message'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'new_message';
    }
}
