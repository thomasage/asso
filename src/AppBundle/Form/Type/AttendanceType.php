<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Attendance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttendanceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'state',
                ChoiceType::class,
                [
                    'required' => true,
                    'expanded' => true,
                    'multiple' => false,
                    'choices' => [0, 1, 2],
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
                'data_class' => Attendance::class,
                'translation_domain' => 'lesson',
            ]
        );
    }
}
