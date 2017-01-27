<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\ChoiceLevelType;
use AppBundle\Form\Type\ChoiceSeasonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatRankProgressType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'season',
                ChoiceSeasonType::class,
                [
                    'label' => 'field.season',
                    'required' => true,
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
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'stat',
            ]
        );
    }
}
