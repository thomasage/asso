<?php
namespace AppBundle\Form\Type;

use AppBundle\Entity\ForecastBudgetPeriod;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceForecastBudgetPeriodType extends AbstractType
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
        foreach ($em->getRepository(ForecastBudgetPeriod::class)->findBy([], ['start' => 'DESC']) as $period) {
            $this->choices[$period->__toString()] = $period->getId();
        }
    }

    /**
     * @param OptionsResolver $resolver
     * @throws AccessException
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
