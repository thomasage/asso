<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\ChoiceCategoryType;
use AppBundle\Form\Type\DatePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
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
                [
                    'required' => false,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'thirdName',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.thirdName',
                ]
            )
            ->add(
                'operationNumber',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.operationNumber',
                ]
            )
            ->add(
                'bankName',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.bankName',
                ]
            )
            ->add(
                'category',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.category',
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
                'translation_domain' => 'accounting',
            ]
        );
    }
}
