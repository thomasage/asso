<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Validator\Constraints\StrongPassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

class ParamType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'password_current',
                PasswordType::class,
                [
                    'label' => 'field.password_current',
                    'required' => true,
                    'constraints' => [
                        new UserPassword(),
                    ],
                ]
            )
            ->add(
                'password',
                RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'field.password_new'],
                    'second_options' => ['label' => 'field.password_confirmation'],
                    'constraints' => [
                        new Length(
                            [
                                'min' => 6,
                            ]
                        ),
                        new StrongPassword(),
                    ],
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
                'translation_domain' => 'param',
            ]
        );
    }
}
