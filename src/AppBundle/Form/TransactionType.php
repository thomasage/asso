<?php
namespace AppBundle\Form;

use AppBundle\Form\Type\DatePickerType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
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
                    'required' => true,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'dateValue',
                DatePickerType::class,
                [
                    'required' => false,
                    'label' => 'field.date_value',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'amount',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'field.amount',
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'thirdName',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'field.thirdName',
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
                'operationNumber',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.operationNumber',
                ]
            )
            ->add(
                'information',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.information',
                ]
            )
            ->add(
                'paymentMethod',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'field.paymentMethod',
                    'class' => 'AppBundle\Entity\PaymentMethod',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('pm')->addOrderBy('pm.name', 'ASC');
                    },
                ]
            )
            ->add(
                'details',
                CollectionType::class,
                [
                    'required' => false,
                    'label' => 'field.details',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'entry_type' => TransactionDetailType::class,
                ]
            )
            ->add(
                'copy',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'field.copy',
                    'mapped' => false,
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
                'data_class' => 'AppBundle\Entity\Transaction',
                'translation_domain' => 'accounting',
            ]
        );
    }
}
