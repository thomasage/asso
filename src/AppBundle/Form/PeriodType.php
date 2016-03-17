<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType
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
                DateType::class,
                array(
                    'label' => 'field.start_date',
                    'required' => true,
                    'widget' => 'single_text',
                )
            )
            ->add(
                'stop',
                DateType::class,
                array(
                    'label' => 'field.stop_date',
                    'required' => true,
                    'widget' => 'single_text'
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
                'translation_domain' => 'lesson',
            )
        );
    }
}
