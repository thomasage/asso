<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\Level;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceLevelType extends AbstractType
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
        $this->choices = [];
        foreach ($em->getRepository(Level::class)->findBy([], ['name' => 'ASC']) as $level) {
            $this->choices[$level->__toString()] = $level->getId();
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'choices' => $this->choices,
            ]
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
