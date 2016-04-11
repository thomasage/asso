<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupSqlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('backup:sql')
            ->setDescription('Dump SQL database')
            ->addArgument('dir', InputArgument::OPTIONAL, 'Where do you want to dump database?');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dir = $input->getArgument('dir');
        if (is_null($dir)) {
            $dir = realpath(__DIR__.'/../../..');
        }

        $container = $this->getContainer();
        $database_host = $container->getParameter('database_host');
        $database_name = $container->getParameter('database_name');
        $database_password = $container->getParameter('database_password');
        $database_user = $container->getParameter('database_user');

        $output->writeln('<info>Dump SQL to '.$dir.'...</info>');

        exec(
            '/usr/bin/mysqldump -h '.$database_host.' \
                                -u '.$database_user.' \
                                --password='.$database_password.' \
                                '.$database_name.' > '.$dir.'/dump.sql',
            $o,
            $r
        );

        if ($r == 0) {
            $output->writeln('<info>Done!</info>');
        } else {
            $output->writeln('<error>Error</error>');
            $output->writeln('Output :');
            ob_start();
            print_r($o);
            $output->writeln(ob_get_contents());
            ob_end_clean();
        }

        return 0;
    }
}
