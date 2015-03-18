<?php

namespace Swot\FormMapperBundle\Form;

use Swot\FormMapperBundle\Entity\AbstractParameter;
use Swot\FormMapperBundle\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FunctionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                /** @var Action $function */
                $function = $event->getData();
                $form = $event->getForm();

                if($function === null) return;

                /** @var Parameter $param */
                $form->add('parameters', 'collection', array(
                    'type' => new FunctionParameterType(),
                    'label' => $function->getName(),
                ));
            });
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\FormMapperBundle\Entity\Action'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swot_networkbundle_thingfunction';
    }
}
