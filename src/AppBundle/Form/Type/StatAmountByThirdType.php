<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatAmountByThirdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'start',
                DateType::class,
                [
                    'label' => 'field.start',
                    'required' => false,
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'stop',
                DateType::class,
                [
                    'label' => 'field.stop',
                    'required' => false,
                    'widget' => 'single_text',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'stat',
            ]
        );
    }
}
