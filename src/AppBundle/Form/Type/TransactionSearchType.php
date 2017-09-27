<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

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
    public function buildForm(FormBuilderInterface $builder, array $options): void
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
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'translation_domain' => 'accounting',
            ]
        );
    }
}
