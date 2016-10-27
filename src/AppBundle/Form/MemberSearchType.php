<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\ChoiceLevelType;
use AppBundle\Form\Type\ChoiceSeasonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'field.firstname',
                    'required' => false,
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'field.lastname',
                    'required' => false,
                ]
            )
            ->add(
                'season',
                ChoiceSeasonType::class,
                [
                    'label' => 'field.season',
                    'required' => false,
                ]
            )
            ->add(
                'level',
                ChoiceLevelType::class,
                [
                    'label' => 'field.level',
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
                'translation_domain' => 'member',
            ]
        );
    }
}
