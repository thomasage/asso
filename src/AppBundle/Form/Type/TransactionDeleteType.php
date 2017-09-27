<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionDeleteType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Transaction::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
