<?php

namespace Swot\FormMapperBundle\Form;

use Swot\FormMapperBundle\Entity\AbstractConstraint;
use Swot\FormMapperBundle\Entity\Parameter;
use Swot\FormMapperBundle\Entity\Constraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotNull;

use Symfony\Component\Validator\Constraints as Assert;

class FunctionParameterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $converter = $this;
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use ($converter) {
                /** @var Parameter $param */
                $param = $event->getData();
                $form = $event->getForm();

                $form->add('value', $param->getType(), array(
                    'label' => $param->getName(),
                    'constraints' => $converter->getConstraintsFromParam($param),
                ));
            });
    }

    public  function getConstraintsFromParam(Parameter $parameter) {
        $constraints = array();

        /** @var AbstractConstraint $constraint */
        foreach($parameter->getConstraints() as $constraint) {
            $c = $constraint->createConstraint();
            if($c !== null) $constraints[] = $c;
        }

        return $constraints;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\FormMapperBundle\Entity\Parameter'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'swot_networkbundle_functionparameter';
    }
}
