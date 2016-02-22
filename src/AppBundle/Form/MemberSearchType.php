<?php
namespace AppBundle\Form;

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
                array(
                    'label' => 'field.firstname',
                    'required' => false,
                )
            )
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'field.lastname',
                    'required' => false,
                )
            )
            ->add(
                'season',
                ChoiceSeasonType::class,
                array(
                    'label' => 'field.season',
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
                'translation_domain' => 'member',
            )
        );
    }
}
