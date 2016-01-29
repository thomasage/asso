<?php
namespace AppBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceSeasonType extends AbstractType
{
    /**
     * @var array
     */
    private $choices;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->choices = array();
        foreach ($em->getRepository('AppBundle:Season')->findBy(array(), array('start' => 'ASC')) as $season) {
            $this->choices[$season->__toString()] = $season->getId();
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'choices' => $this->choices,
            )
        );
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}