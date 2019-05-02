<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'required' => true,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'start',
                TimeType::class,
                [
                    'required' => true,
                    'label' => 'field.start',
                    'hours' => range(8, 21),
                    'minutes' => array(0, 15, 30, 45),
                ]
            )
            ->add(
                'duration',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'field.duration',
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'field.comment',
                ]
            )
            ->add(
                'levels',
                EntityType::class,
                [
                    'required' => true,
                    'multiple' => true,
                    'label' => 'field.level',
                    'class' => Level::class,
                    'query_builder' => static function (EntityRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('l')
                            ->addOrderBy('l.name', 'ASC');
                    },
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Lesson::class,
                'translation_domain' => 'lesson',
            ]
        );
    }
}
