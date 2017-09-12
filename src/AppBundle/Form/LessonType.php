<?php

namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date',
                DatePickerType::class,
                array(
                    'required' => true,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                )
            )
            ->add(
                'start',
                TimeType::class,
                array(
                    'required' => true,
                    'label' => 'field.start',
                    'hours' => range(8, 21),
                    'minutes' => array(0, 15, 30, 45),
                )
            )
            ->add(
                'duration',
                IntegerType::class,
                array(
                    'required' => true,
                    'label' => 'field.duration',
                )
            )
            ->add(
                'comment',
                TextareaType::class,
                array(
                    'required' => false,
                    'label' => 'field.comment',
                )
            )
            ->add(
                'levels',
                EntityType::class,
                array(
                    'required' => true,
                    'multiple' => true,
                    'label' => 'field.level',
                    'class' => 'AppBundle\Entity\Level',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')->addOrderBy('l.name', 'ASC');
                    },
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
                'data_class' => 'AppBundle\Entity\Lesson',
                'translation_domain' => 'lesson',
            )
        );
    }
}
