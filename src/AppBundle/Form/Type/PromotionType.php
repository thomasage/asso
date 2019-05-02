<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Promotion;
use AppBundle\Entity\Rank;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'label' => 'field.date',
                    'required' => true,
                    'widget' => 'single_text',
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
                'rank',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'field.rank',
                    'class' => Rank::class,
                    'choice_label' => 'fullName',
                    'query_builder' => static function (EntityRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('r')
                            ->addOrderBy('r.position', 'ASC');
                    },
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Promotion::class,
                'translation_domain' => 'member',
            ]
        );
    }
}
