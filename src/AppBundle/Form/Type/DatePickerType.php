<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DatePickerType extends AbstractType
{
    /**
     * @var string
     */
    private $locale;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->locale = $requestStack->getCurrentRequest()->getLocale();
    }

    /**
     * @param OptionsResolver $resolver
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'format' => 'MM/dd/yyyy',
                'html5' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'date',
                ],
            ]
        );
        if ($this->locale === 'fr') {
            $resolver->setDefaults(
                [
                    'format' => 'dd/MM/yyyy',
                ]
            );
        }
    }

    /**
     * @return mixed
     */
    public function getParent()
    {
        return DateType::class;
    }
}
