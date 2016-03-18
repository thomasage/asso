<?php

namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PromotionType extends AbstractType
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
                'rank',
                EntityType::class,
                array(
                    'required' => true,
                    'label' => 'field.rank',
                    'class' => 'AppBundle\Entity\Rank',
                    'choice_label' => 'fullName',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('r')->addOrderBy('r.position', 'ASC');
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
                'data_class' => 'AppBundle\Entity\Promotion',
                'translation_domain' => 'member',
            )
        );
    }
}
