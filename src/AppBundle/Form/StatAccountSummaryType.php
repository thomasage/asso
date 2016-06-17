<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatAccountSummaryType extends AbstractType
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
                    'required' => false,
                ]
            )
            ->add(
                'stop',
                DatePickerType::class,
                [
                    'label' => 'field.stop',
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'stat',
            ]
        );
    }
}
