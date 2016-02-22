<?php
namespace AppBundle\Form;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'weekday',
                ChoiceType::class,
                array(
                    'label' => 'field.weekday',
                    'required' => true,
                    'choices' => array(
                        'monday' => 1,
                        'tuesday' => 2,
                    ),
                )
            )
            ->add(
                'start',
                TimeType::class,
                array(
                    'label' => 'field.start',
                    'required' => true,
                    'hours' => array(8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21),
                    'minutes' => array(0, 15, 30, 45),
                )
            )
            ->add(
                'duration',
                IntegerType::class,
                array(
                    'label' => 'field.duration',
                    'required' => true,
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Planning',
                'translation_domain' => 'lesson',
            )
        );
    }
}