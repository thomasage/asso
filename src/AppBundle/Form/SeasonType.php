<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'start',
                DatePickerType::class,
                array(
                    'required' => true,
                    'label' => 'field.start',
                    'widget' => 'single_text',
                )
            )
            ->add(
                'stop',
                DatePickerType::class,
                array(
                    'required' => true,
                    'label' => 'field.stop',
                    'widget' => 'single_text',
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
                'data_class' => 'AppBundle\Entity\Season',
                'translation_domain' => 'param',
            )
        );
    }
}
