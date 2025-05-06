<?php

namespace App\Form;

use App\Entity\Equipe;
use App\Entity\Joueur;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class JoueurType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Fetch all user IDs associated with a Joueur
        $usedUserIds = $this->entityManager->getRepository(Joueur::class)->createQueryBuilder('j')
            ->select('j.id_user') // Fetch only the id_user column
            ->getQuery()
            ->getResult();

        // Transform the result into a flat array of IDs
        $usedUserIds = array_map(fn($joueur) => $joueur['id_user'], $usedUserIds);

        // Fetch all user IDs that are not associated with a Joueur
        $availableUsers = $this->entityManager->getRepository(Users::class)->createQueryBuilder('u')
            ->select('u.id') // Fetch only the id column
            ->where('u.id NOT IN (:usedUserIds)')
            ->setParameter('usedUserIds', $usedUserIds)
            ->getQuery()
            ->getResult();

        // Transform the result into a flat array of IDs
        $userChoices = array_map(fn($user) => $user['id'], $availableUsers);

        $builder
            ->add('cin')
            ->add('url_photo')
            ->add('id_user', ChoiceType::class, [
                'choices' => array_combine($userChoices, $userChoices), // Use IDs as both keys and values
                'placeholder' => 'Select a user',
                'required' => true,
            ])
            ->add('equipe', EntityType::class, [
                'class' => Equipe::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Joueur::class,
        ]);
    }
}
