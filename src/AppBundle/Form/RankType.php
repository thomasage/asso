<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RankType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'label' => 'field.name',
                    'required' => true,
                    'attr' => array(
                        'autofocus' => true,
                    ),
                )
            )
            ->add(
                'description',
                TextType::class,
                array(
                    'label' => 'field.description',
                    'required' => false,
                )
            )
            ->add(
                'image',
                FileType::class,
                array(
                    'label' => 'field.image',
                    'required' => false,
                    'mapped' => false,
                    'attr' => array('accept' => 'image/jpeg'),
                )
            )
            ->add(
                'lessons',
                IntegerType::class,
                array(
                    'label' => 'field.lessons',
                    'required' => false,
                )
            )
            ->add(
                'ageMin',
                IntegerType::class,
                array(
                    'label' => 'field.age_min',
                    'required' => false,
                )
            )
            ->add(
                'ageMax',
                IntegerType::class,
                array(
                    'label' => 'field.age_max',
                    'required' => false,
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
                'data_class' => 'AppBundle\Entity\Rank',
                'translation_domain' => 'param',
            )
        );
    }
}
