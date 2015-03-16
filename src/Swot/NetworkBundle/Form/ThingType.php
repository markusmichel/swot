<?php

namespace Swot\NetworkBundle\Form;

use Swot\NetworkBundle\Entity\Thing;
use Swot\NetworkBundle\Security\AccessType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ThingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('accessType', 'choice', array(
                'choices' => array(
                    AccessType::ACCESS_TYPE_PRIVATE      => AccessType::ACCESS_TYPE_PRIVATE,
                    AccessType::ACCESS_TYPE_RESTRICTED   => AccessType::ACCESS_TYPE_RESTRICTED,
                    AccessType::ACCESS_TYPE_PUBLIC       => AccessType::ACCESS_TYPE_PUBLIC,
                )
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\NetworkBundle\Entity\Thing'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swot_networkbundle_thing';
    }
}
