<?php
    namespace App\Command;

    use App\Entity\RequestAPI;
    use App\Repository\ActionRepository;
    use App\Repository\AutomationRepository;
    use App\Repository\AutomationActionRepository;
    use App\Repository\ServiceRepository;
    use App\Repository\UserServiceRepository;
    use Symfony\Component\Process\Process;
    use Symfony\Component\Console\Command\Command;
    use Symfony\Component\Console\Input\InputArgument;
    use Symfony\Component\Console\Input\InputInterface;
    use Symfony\Component\Console\Input\InputOption;
    use Symfony\Component\Console\Output\OutputInterface;
    use Symfony\Component\Console\Style\SymfonyStyle;

    class ServerCommand extends Command
    {
        protected static $defaultName = "app:server";
        protected static $defaultDescription = "Server loop";
        private $a;

        public function __construct(AutomationRepository $automation_repository)
        {
            $this->a = $automation_repository;
            parent::__construct();
        }

        protected function configure(): void {}

        protected function execute(InputInterface $input, OutputInterface $output): int
        {
            $io = new SymfonyStyle($input, $output);
            while (true) {
                $io->success("test");
                sleep(10);
            }
            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
            return Command::SUCCESS;
        }
    }
?>