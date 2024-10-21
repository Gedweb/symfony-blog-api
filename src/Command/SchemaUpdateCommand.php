<?php
declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:schema',
    description: 'Update a schema without generating foreign keys',
)]
class SchemaUpdateCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'update');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);

        if ($input->getOption('update')) {
            $sql = $schemaTool->getUpdateSchemaSql($metadata);
        } else {
            $sql = $schemaTool->getCreateSchemaSql($metadata);
        }
        $filteredSql = array_filter($sql, fn($query) => stripos($query, 'FOREIGN KEY') === false);

        foreach ($filteredSql as $query) {
            $this->entityManager->getConnection()->executeStatement($query);
        }

        return Command::SUCCESS;
    }
}
