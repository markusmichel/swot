<?php

namespace Swot\NetworkBundle\Form;

use Swot\NetworkBundle\Entity\User;
use Swot\NetworkBundle\Entity\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RentalType extends AbstractType
{
    /** @var UserRepository  */
    private $userRepo;

    /** @var User */
    private $user;

    public function __construct(UserRepository $userRepo, TokenStorage $token) {
        $this->userRepo = $userRepo;
        $this->user = $token->getToken()->getUser();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $friends = $this->userRepo->findFriendsOf($this->user);

        $currDate = new \DateTime();
        $builder
            ->add('userTo', 'entity', array(
                'class' => 'SwotNetworkBundle:User',
                'choices' => $friends,
                'property' => 'username',
            ))
            ->add('accessGrantedUntil', 'datetime', array(
                'html5' => true,
                'years' => range(intval($currDate->format("Y")), intval($currDate->format("Y")) + 10),
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Swot\NetworkBundle\Entity\Rental'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rental';
    }
}
