<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Member;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                [
                    'label' => 'field.firstname',
                    'required' => true,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ]
            )
            ->add(
                'lastname',
                TextType::class,
                [
                    'label' => 'field.lastname',
                    'required' => true,
                ]
            )
            ->add(
                'gender',
                ChoiceType::class,
                [
                    'label' => 'field.gender',
                    'required' => true,
                    'choices' => [
                        'gender.f' => 'f',
                        'gender.m' => 'm',
                    ],
                ]
            )
            ->add(
                'birthday',
                DatePickerType::class,
                [
                    'label' => 'field.birthday',
                    'required' => false,
                ]
            )
            ->add(
                'birthplace',
                TextType::class,
                [
                    'label' => 'field.birthplace',
                    'required' => false,
                ]
            )
            ->add(
                'address',
                TextType::class,
                [
                    'label' => 'field.address',
                    'required' => false,
                ]
            )
            ->add(
                'zip',
                TextType::class,
                [
                    'label' => 'field.zip',
                    'required' => false,
                ]
            )
            ->add(
                'city',
                TextType::class,
                [
                    'label' => 'field.city',
                    'required' => false,
                ]
            )
            ->add(
                'profession',
                TextType::class,
                [
                    'label' => 'field.profession',
                    'required' => false,
                ]
            )
            ->add(
                'nationality',
                TextType::class,
                [
                    'label' => 'field.nationality',
                    'required' => false,
                ]
            )
            ->add(
                'phone0',
                TextType::class,
                [
                    'label' => 'field.phone0',
                    'required' => false,
                ]
            )
            ->add(
                'phone1',
                TextType::class,
                [
                    'label' => 'field.phone1',
                    'required' => false,
                ]
            )
            ->add(
                'phone2',
                TextType::class,
                [
                    'label' => 'field.phone2',
                    'required' => false,
                ]
            )
            ->add(
                'phone3',
                TextType::class,
                [
                    'label' => 'field.phone3',
                    'required' => false,
                ]
            )
            ->add(
                'email0',
                EmailType::class,
                [
                    'label' => 'field.email0',
                    'required' => false,
                ]
            )
            ->add(
                'email1',
                EmailType::class,
                [
                    'label' => 'field.email1',
                    'required' => false,
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'label' => 'field.comment',
                    'required' => false,
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
                'data_class' => Member::class,
                'translation_domain' => 'member',
            ]
        );
    }
}
