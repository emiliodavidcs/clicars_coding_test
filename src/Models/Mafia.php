<?php

namespace Clicars\Models;

use Clicars\Interfaces\IMafia;
use Clicars\Interfaces\IMember;

class Mafia implements IMafia
{
    private IMember $godfather;

    private array $members;

    public function __construct(IMember $godfather)
    {
        $this->godfather = $godfather;

        $this->members = [];

        $this->addMember($godfather);
    }

    public function getGodfather(): IMember
    {
        return $this->godfather;
    }

    public function setGodFather(IMember $godfather): IMember
    {
        $this->godfather = $godfather;

        return $godfather;
    }

    public function addMember(IMember $member): ?IMember
    {
        $this->members[$member->getId()] = $member;

        return $member;
    }

    public function removeMember(IMember $member): ?IMember
    {
        unset($this->members[$member->getId()]);

        return $member;
    }

    public function getMember(int $id): ?IMember
    {
        return $this->members[$id] ?? null;

    }

    public function sendToPrison(IMember $member): bool
    {
        $this->removeMember($member);
        if (!is_null($member->getBoss())) {
            $member->getBoss()->removeSubordinate($member);
        }

        $this->relocateSubordinates($member);

        return true;
    }

    public function releaseFromPrison(IMember $member): bool
    {
        // TODO: Implement releaseFromPrison() method.
        return true;
    }

    public function findBigBosses(int $minimumSubordinates): array
    {
        $bigBosses = [];
        foreach ($this->members as $member) {
            if (count($member->getSubordinates()) >= $minimumSubordinates) {
                $bigBosses[] = $member;
            }
        }

        return $bigBosses;
    }

    public function compareMembers(IMember $memberA, IMember $memberB): ?IMember
    {
        // TODO: Implement compareMembers() method.
    }

    private function relocateSubordinates(IMember $member): void
    {
        $newBoss = null;

        if (!is_null($member->getBoss())) {
            foreach ($member->getBoss()->getSubordinates() as $possibleBoss) {
                if (is_null($newBoss) || $possibleBoss->getAge() > $newBoss->getAge()) {
                    $newBoss = $possibleBoss;
                }
            }
        }

        if (is_null($newBoss)) {

            foreach ($member->getSubordinates() as $possibleBoss) {
                if (is_null($newBoss) || $possibleBoss->getAge() > $newBoss->getAge()) {
                    $newBoss = $possibleBoss;
                }
            }
            if (!is_null($newBoss)) {
                $member->removeSubordinate($newBoss);
                $newBoss->setBoss($member->getBoss());
                if (is_null($member->getBoss())) {
                    $this->setGodFather($newBoss);
                }
            }
        }

        foreach ($member->getSubordinates() as $subordinate) {
            $subordinate->setBoss($newBoss);
        }
    }
}