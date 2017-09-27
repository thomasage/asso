<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\ForecastBudgetPeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForecastBudgetPeriodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'start',
                DatePickerType::class,
                [
                    'label' => 'field.start',
                    'required' => true,
                ]
            )
            ->add(
                'stop',
                DatePickerType::class,
                [
                    'label' => 'field.stop',
                    'required' => true,
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
                'data_class' => ForecastBudgetPeriod::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
