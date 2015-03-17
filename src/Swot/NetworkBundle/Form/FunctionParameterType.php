<?php

namespace Swot\NetworkBundle\Form;

use Swot\NetworkBundle\Entity\FunctionParameter;
use Swot\NetworkBundle\Entity\ParameterConstraint;
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
                /** @var FunctionParameter $param */
                $param = $event->getData();
                $form = $event->getForm();

                $form->add('value', $param->getType(), array(
                    'label' => $param->getName(),
                    'constraints' => $converter->getConstraintsFromParam($param),
                ));
            });
    }

    public  function getConstraintsFromParam(FunctionParameter $parameter) {
        $constraints = array();

        /** @var ParameterConstraint $constraint */
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
            'data_class' => 'Swot\NetworkBundle\Entity\FunctionParameter'
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