<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionSearchType extends AbstractType
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
                    'required' => false,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                )
            )
            ->add(
                'thirdName',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'field.thirdName',
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
                'translation_domain' => 'accounting',
            )
        );
    }
}
