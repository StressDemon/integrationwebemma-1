<?php
namespace App\Service;

use App\Entity\GameMatch;
use App\Entity\StatistiquesEquipe;
use Doctrine\ORM\EntityManagerInterface;

class StatistiquesEquipeUpdater
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function update(GameMatch $gameMatch): void
    {
        $equipe1 = $gameMatch->getEquipe1();
        $equipe2 = $gameMatch->getEquipe2();

        $statRepo = $this->entityManager->getRepository(StatistiquesEquipe::class);

        $statsEquipe1 = $statRepo->findOneBy(['equipe' => $equipe1]) ?? new StatistiquesEquipe();
        $statsEquipe1->setEquipe($equipe1);

        $statsEquipe2 = $statRepo->findOneBy(['equipe' => $equipe2]) ?? new StatistiquesEquipe();
        $statsEquipe2->setEquipe($equipe2);

        $statsEquipe1->incrementMatchsJoues();
        $statsEquipe1->setButsMarques($statsEquipe1->getButsMarques() + $gameMatch->getScoreEquipe1());
        $statsEquipe1->setButsEncaisses($statsEquipe1->getButsEncaisses() + $gameMatch->getScoreEquipe2());

        if ($gameMatch->getScoreEquipe1() > $gameMatch->getScoreEquipe2()) {
            $statsEquipe1->incrementVictoires();
        } elseif ($gameMatch->getScoreEquipe1() < $gameMatch->getScoreEquipe2()) {
            $statsEquipe1->incrementDefaites();
        } else {
            $statsEquipe1->incrementMatchsNuls();
        }

        $statsEquipe2->incrementMatchsJoues();
        $statsEquipe2->setButsMarques($statsEquipe2->getButsMarques() + $gameMatch->getScoreEquipe2());
        $statsEquipe2->setButsEncaisses($statsEquipe2->getButsEncaisses() + $gameMatch->getScoreEquipe1());

        if ($gameMatch->getScoreEquipe2() > $gameMatch->getScoreEquipe1()) {
            $statsEquipe2->incrementVictoires();
        } elseif ($gameMatch->getScoreEquipe2() < $gameMatch->getScoreEquipe1()) {
            $statsEquipe2->incrementDefaites();
        } else {
            $statsEquipe2->incrementMatchsNuls();
        }

        $this->entityManager->persist($statsEquipe1);
        $this->entityManager->persist($statsEquipe2);
        $this->entityManager->flush();
    }

    public function reverse(GameMatch $match): void
    {
        if ($match->getId() === null) {
            dump('Reverse: Match is new, nothing to reverse.');
            return;
        }

        $equipe1Stats = $this->entityManager->getRepository(StatistiquesEquipe::class)->findOneBy(['equipe' => $match->getEquipe1()]);
        $equipe2Stats = $this->entityManager->getRepository(StatistiquesEquipe::class)->findOneBy(['equipe' => $match->getEquipe2()]);

        if (!$equipe1Stats || !$equipe2Stats) {
            dump('Reverse: Missing statistics for one or both teams.');
            return; 
        }

        dump('Reverse: Before reversing', $equipe1Stats, $equipe2Stats);

        $equipe1Stats->setMatchsJoues($equipe1Stats->getMatchsJoues() - 1);
        $equipe2Stats->setMatchsJoues($equipe2Stats->getMatchsJoues() - 1);

        if ($match->getScoreEquipe1() > $match->getScoreEquipe2()) {
            $equipe1Stats->setVictoires($equipe1Stats->getVictoires() - 1);
            $equipe2Stats->setDefaites($equipe2Stats->getDefaites() - 1);
        } elseif ($match->getScoreEquipe1() < $match->getScoreEquipe2()) {
            $equipe2Stats->setVictoires($equipe2Stats->getVictoires() - 1);
            $equipe1Stats->setDefaites($equipe1Stats->getDefaites() - 1);
        } else {
            $equipe1Stats->setNuls($equipe1Stats->getNuls() - 1);
            $equipe2Stats->setNuls($equipe2Stats->getNuls() - 1);
        }

        $equipe1Stats->setButsMarques($equipe1Stats->getButsMarques() - $match->getScoreEquipe1());
        $equipe1Stats->setButsEncaisses($equipe1Stats->getButsEncaisses() - $match->getScoreEquipe2());

        $equipe2Stats->setButsMarques($equipe2Stats->getButsMarques() - $match->getScoreEquipe2());
        $equipe2Stats->setButsEncaisses($equipe2Stats->getButsEncaisses() - $match->getScoreEquipe1());

        dump('Reverse: After reversing', $equipe1Stats, $equipe2Stats);

        $this->entityManager->flush();
    }
}