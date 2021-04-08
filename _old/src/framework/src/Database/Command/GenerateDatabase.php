<?php

declare(strict_types=1);

namespace Tulia\Framework\Database\Command;

use Doctrine\DBAL\DriverManager;
use PDO;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Tulia\Framework\Console\Command\Command;
use Tulia\Framework\Database\Connection;

/**
 * @author Adam Banaszkiewicz
 */
class GenerateDatabase extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:database')
            ->setDescription('Generate database in given MySQL server.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Please provide following credentials to create new database.');
        $output->writeln('If You create a database in docker installation, please leave empty to use default docker values from brackets.');
        $output->writeln('---');

        $helper = $this->getHelper('question');
        $name = $helper->ask($input, $output, new Question('Database name: '));

        if (empty($name)) {
            $output->writeln('<error>Please provide a database name.</error>');
            return Command::FAILURE;
        }

        $host = $helper->ask($input, $output, new Question('Database host [tulia_mysql]: ', 'tulia_mysql'));
        $port = $helper->ask($input, $output, new Question('Database port [3306]: ', '3306'));
        $user = $helper->ask($input, $output, new Question('Username [root]: ', 'root'));
        $pass = $helper->ask($input, $output, new Question('Password [root]: ', 'root'));

        $connection = DriverManager::getConnection([
            'user'     => $user,
            'password' => $pass,
            'host'     => $host,
            'port'     => $port,
            'driver'   => 'pdo_mysql',
            'wrapperClass' => Connection::class,
            'driverOptions' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
            ],
        ]);

        $connection->connect();
        $sm = $connection->getSchemaManager();

        foreach ($sm->listDatabases() as $row) {
            if ($row['Database'] === $name) {
                $output->writeln(sprintf('<comment>Database "%s" already exists, cannot be created twice.</comment>', $name));
                return Command::FAILURE;
            }
        }

        $sm->createDatabase($name);

        $output->writeln(sprintf('<info>Database "%s" created successfully.</info>', $name));

        return Command::SUCCESS;
    }
}
