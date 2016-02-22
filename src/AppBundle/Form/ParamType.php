<?php
namespace AppBundle\Form;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'password_current',
                PasswordType::class,
                array(
                    'label' => 'field.password_current',
                    'required' => true,
                    'constraints' => array(
                        new UserPassword(),
                    ),
                )
            )
            ->add(
                'password',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => array('label' => 'field.password_new'),
                    'second_options' => array('label' => 'field.password_confirmation'),
                    'constraints' => array(
                        new Length(
                            array(
                                'min' => 6,
                            )
                        ),
                        new StrongPassword(),
                    ),
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
                'translation_domain' => 'param',
            )
        );
    }
}
