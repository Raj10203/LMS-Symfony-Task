<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Country;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressForm extends AbstractType
{
    private EntityManagerInterface $entityManager;

    // Inject the EntityManager to fetch filtered city list
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('streetName')
            ->add('postcode')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a country',
                'mapped' => true,
                'required' => true,
                'attr' => ['class' => 'select2-dropdown-single']
            ]);

        $formModifier = function ($form, ?Country $country) {
            $cities = $country ? $this->entityManager
                ->getRepository(City::class)
                ->findBy(['country' => $country]) : [];

            $form->add('city', EntityType::class, [
                'class' => City::class,
                'placeholder' => $country ? 'Select a city' : 'Select country first',
                'choices' => $cities,
                'choice_label' => 'name',
                'required' => true,
                'attr' => ['class' => 'select2-dropdown-single']
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $form = $event->getForm();
            /** @var Address|null $data */
            $formModifier($form, $data?->getCountry());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $form = $event->getForm();

            if (!empty($data['country'])) {
                $country = $this->entityManager->getRepository(Country::class)->find($data['country']);
                $formModifier($form, $country);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
