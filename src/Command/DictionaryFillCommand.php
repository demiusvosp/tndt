<?php

namespace App\Command;

use App\Dictionary\Fetcher;
use App\Dictionary\TypesEnum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class DictionaryFillCommand extends Command
{
    protected static $defaultName = 'app:dictionary:fill';
    protected static string $defaultDescription = 'Fill dictionary default value to all related entity';

    private EntityManagerInterface $entityManager;
    private Fetcher $dictionaryFetcher;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager,
        Fetcher $dictionaryFetcher,
        PropertyAccessorInterface $propertyAccessor
    ) {
        parent::__construct($name);
        $this->dictionaryFetcher = $dictionaryFetcher;
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('project', InputArgument::REQUIRED, 'project suffix')
            ->addArgument('dictionary', InputArgument::REQUIRED, 'dictionary full type')
            ->addOption(
                'restore',
                'r',
                InputOption::VALUE_NONE,
                'set the default value instead of the wrong one'
            )
            ->addOption(
                'from-value',
                'f',
                InputOption::VALUE_REQUIRED,
                'set the specified value, instead of an empty'
            )
            ->addOption(
                'to-value',
                't',
                InputOption::VALUE_REQUIRED,
                'set to the specified value, instead of default'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dictionaryType = TypesEnum::from($input->getArgument('dictionary'));
        $project = $input->getArgument('project');
        $dictionary = $this->dictionaryFetcher->getDictionary(
            $dictionaryType,
            $project
        );
        if (!$dictionary->isEnabled()) {
            $io->error(
                'Dictionary ' . $dictionaryType->getLabel()
                . ' in project ' . $project . ' is not configured. Configure it on project setting page.'
            );
            return -1;
        }

        $io->text('Fill ' . $dictionaryType->getLabel() . ' to ' . $project . ' project');

        $to = $input->getOption('to-value');
        if ($to === null) {
            $to = $dictionary->getDefault();
        }

        $entityMeta = TypesEnum::relatedEntities()[$dictionaryType->getValue()];
        $repository = $this->entityManager->getRepository($entityMeta['class']);

        $criteria = Criteria::create();
        $criteria->where($criteria::expr()->eq('suffix', $project));
        if ($input->getOption('restore')) {
            $validValues = [];
            foreach ($dictionary->getItems() as $item) {
                $validValues[] = $item->getId();
            }
            $criteria->andWhere($criteria::expr()->notIn($entityMeta['subType'], $validValues));
            $io->text('search entity with invalid ' . $dictionaryType->getValue() . ' values...');

        } else {
            $from = $input->getOption('from-value');
            if ($from === null) {
                $from = 0;
            }
            $criteria->andWhere($criteria::expr()->eq($entityMeta['subType'], $from));
            $io->text('search entity with ' . ($from ?? 'null') . ' ' . $dictionaryType->getValue() . ' value...');
        }

        $result = $repository->matching($criteria);
        $io->note('Found ' . $result->count() . ' entities ');
        foreach ($result as $entity) {
            $this->propertyAccessor->setValue(
                $entity,
                $entityMeta['subType'],
                $to
            );
        }
        $this->entityManager->flush();
        $io->success('In ' . $entityMeta['class'].'::'.$entityMeta['subType']. ' filled successfully');

        return 0;
    }
}
