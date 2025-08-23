<?php

namespace App\Command;

use App\Entity\LoginEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:report:daily-logins')]
class ReportDailyLoginsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $start = new \DateTimeImmutable('today 00:00');
        $end   = new \DateTimeImmutable('tomorrow 00:00');

        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT e.user)')
            ->from(LoginEvent::class, 'e')
            ->where('e.loggedAt >= :s AND e.loggedAt < :e')
            ->setParameter('s', $start)
            ->setParameter('e', $end);

        $count = (int) $qb->getQuery()->getSingleScalarResult();

        $output->writeln(sprintf("Clients connectés aujourd'hui: %d", $count));

        return Command::SUCCESS;
    }
}
