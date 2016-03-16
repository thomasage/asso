<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                array(
                    'label' => 'field.firstname',
                    'required' => true,
                    'attr' => array(
                        'autofocus' => true,
                    ),
                )
            )
            ->add(
                'lastname',
                TextType::class,
                array(
                    'label' => 'field.lastname',
                    'required' => true,
                )
            )
            ->add(
                'gender',
                ChoiceType::class,
                array(
                    'label' => 'field.gender',
                    'required' => true,
                    'choices' => array(
                        'gender.f' => 'f',
                        'gender.m' => 'm',
                    ),
                )
            )
            ->add(
                'birthday',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'label' => 'field.birthday',
                    'required' => true,
                )
            )
            ->add(
                'birthplace',
                TextType::class,
                array(
                    'label' => 'field.birthplace',
                    'required' => true,
                )
            )
            ->add(
                'address',
                TextType::class,
                array(
                    'label' => 'field.address',
                    'required' => true,
                )
            )
            ->add(
                'zip',
                TextType::class,
                array(
                    'label' => 'field.zip',
                    'required' => true,
                )
            )
            ->add(
                'city',
                TextType::class,
                array(
                    'label' => 'field.city',
                    'required' => true,
                )
            )
            ->add(
                'profession',
                TextType::class,
                array(
                    'label' => 'field.profession',
                    'required' => false,
                )
            )
            ->add(
                'nationality',
                TextType::class,
                array(
                    'label' => 'field.nationality',
                    'required' => false,
                )
            )
            ->add(
                'phone0',
                TextType::class,
                array(
                    'label' => 'field.phone0',
                    'required' => false,
                )
            )
            ->add(
                'phone1',
                TextType::class,
                array(
                    'label' => 'field.phone1',
                    'required' => false,
                )
            )
            ->add(
                'phone2',
                TextType::class,
                array(
                    'label' => 'field.phone2',
                    'required' => false,
                )
            )
            ->add(
                'phone3',
                TextType::class,
                array(
                    'label' => 'field.phone3',
                    'required' => false,
                )
            )
            ->add(
                'email0',
                EmailType::class,
                array(
                    'label' => 'field.email0',
                    'required' => false,
                )
            )
            ->add(
                'email1',
                EmailType::class,
                array(
                    'label' => 'field.email1',
                    'required' => false,
                )
            )
            ->add(
                'comment',
                TextareaType::class,
                array(
                    'label' => 'field.comment',
                    'required' => false,
                )
            )
            ->add(
                'photo',
                FileType::class,
                array(
                    'label' => 'field.photo',
                    'required' => false,
                    'mapped' => false,
                    'attr' => array('accept' => 'image/jpeg'),
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
                'data_class' => 'AppBundle\Entity\Member',
                'translation_domain' => 'member',
            )
        );
    }
}
