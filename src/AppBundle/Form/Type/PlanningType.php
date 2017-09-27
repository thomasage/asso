<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Level;
use AppBundle\Entity\Planning;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'weekday',
                ChoiceType::class,
                [
                    'label' => 'field.weekday',
                    'required' => true,
                    'choices' => [
                        'monday' => 1,
                        'tuesday' => 2,
                        'wednesday' => 3,
                        'thursday' => 4,
                        'friday' => 5,
                        'saturday' => 6,
                        'sunday' => 7,
                    ],
                ]
            )
            ->add(
                'start',
                TimeType::class,
                [
                    'label' => 'field.start',
                    'required' => true,
                    'hours' => [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21],
                    'minutes' => [0, 15, 30, 45],
                ]
            )
            ->add(
                'duration',
                IntegerType::class,
                [
                    'label' => 'field.duration',
                    'required' => true,
                ]
            )
            ->add(
                'level',
                EntityType::class,
                [
                    'label' => 'field.level',
                    'required' => true,
                    'class' => Level::class,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Planning::class,
                'translation_domain' => 'lesson',
            ]
        );
    }
}
