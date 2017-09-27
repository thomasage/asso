<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Season;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
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
                    'required' => true,
                    'label' => 'field.start',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'stop',
                DatePickerType::class,
                [
                    'required' => true,
                    'label' => 'field.stop',
                    'widget' => 'single_text',
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
                'data_class' => Season::class,
                'translation_domain' => 'param',
            ]
        );
    }
}
