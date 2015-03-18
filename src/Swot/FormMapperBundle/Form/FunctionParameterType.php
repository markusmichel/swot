<?php

namespace Swot\FormMapperBundle\Form;

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

        /** @var Constraint $constraint */
        foreach($parameter->getConstraints() as $constraint) {
            $c = null;
            switch($constraint->getType()) {
                case 'NotNull':
                    $c = new Assert\NotNull();
                    break;
                case 'NotBlank':
                    $c = new Assert\NotBlank();
                    break;
                case 'GreaterThan':
                    $c = new Assert\GreaterThan();
                    break;
                case 'LessThan':
                    $c = new Assert\LessThan();
                    break;
                case 'Email':
                    $c = new Assert\Email();
                    break;
                case 'Date':
                    $c = new Assert\Date();
                    break;
                case 'DateTime':
                    $c = new Assert\DateTime();
                    break;
                case 'Time':
                    $c = new Assert\Time();
                    break;
                case 'Language':
                    $c = new Assert\Language();
                    break;
                case 'Country':
                    $c = new Assert\Country();
                    break;
                case 'Locale':
                    $c = new Assert\Locale();
                    break;
                case 'Choice':
                    $c = new Assert\Choice();
                    break;
            }

            if($c !== null) $constraints[] = $c;
        }

        return $constraints;
    }

    private function transformParameterToFormType() {

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
