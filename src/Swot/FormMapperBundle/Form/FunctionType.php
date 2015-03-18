<?php

namespace Swot\FormMapperBundle\Form;

use Swot\FormMapperBundle\Entity\AbstractParameter;
use Swot\NetworkBundle\Entity\ThingFunction;
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
                /** @var ThingFunction $function */
                $function = $event->getData();
                $form = $event->getForm();

                if($function === null) return;

                /** @var AbstractParameter $param */
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
            'data_class' => 'Swot\NetworkBundle\Entity\ThingFunction'
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
