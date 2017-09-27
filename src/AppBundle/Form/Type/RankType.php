<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Rank;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'field.name',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'label' => 'field.description',
                    'required' => false,
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'field.image',
                    'required' => false,
                    'mapped' => false,
                    'attr' => ['accept' => 'image/jpeg'],
                ]
            )
            ->add(
                'lessons',
                IntegerType::class,
                [
                    'label' => 'field.lessons',
                    'required' => false,
                ]
            )
            ->add(
                'ageMin',
                NumberType::class,
                [
                    'label' => 'field.age_min',
                    'required' => false,
                    'scale' => 1,
                ]
            )
            ->add(
                'ageMax',
                NumberType::class,
                [
                    'label' => 'field.age_max',
                    'required' => false,
                    'scale' => 1,
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
                'data_class' => Rank::class,
                'translation_domain' => 'param',
            ]
        );
    }
}
