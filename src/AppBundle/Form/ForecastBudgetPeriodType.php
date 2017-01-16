<?php
namespace AppBundle\Form;

use AppBundle\Entity\ForecastBudgetPeriod;
use AppBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForecastBudgetPeriodType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ForecastBudgetPeriod::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
