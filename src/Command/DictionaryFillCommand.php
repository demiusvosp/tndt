<?php /** @noinspection NullPointerExceptionInspection */

namespace App\Command;

use App\Dictionary\Fetcher;
use App\Dictionary\TypesEnum;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

#[AsCommand(
    name: 'app:dictionary:fill',
    description: 'Fill dictionary default value to all related entity'
)]
class DictionaryFillCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private Fetcher $dictionaryFetcher;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(
        EntityManagerInterface $entityManager,
        Fetcher $dictionaryFetcher,
        PropertyAccessorInterface $propertyAccessor
    ) {
        parent::__construct();
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
            ->addOption(
                'dry-run',
                'd',
                InputOption::VALUE_NONE,
                'only print planning changes without real change'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $dictionaryType = TypesEnum::from($input->getArgument('dictionary'));
        } catch (\UnexpectedValueException $e) {
            $io->error('Wrong dictionary type');
            $io->note('Valid types: ' . implode(', ', TypesEnum::values()));
            return -1;
        }
        $project = $input->getArgument('project');
        try {
            $dictionary = $this->dictionaryFetcher->getDictionary(
                $dictionaryType,
                $project
            );
        } catch (\InvalidArgumentException $e) {
            $io->error('Project ' . $project . ' not found');
            return -1;
        }
        if (!$dictionary->isEnabled()) {
            $io->warning(
                'Dictionary ' . $dictionaryType->getLabel()
                . ' in project ' . $project . ' is not configured. Configure it on project setting page.'
            );
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
            if ($dictionary->isEnabled()) {
                $io->writeln(
                    'dictionary configured, search all except valid dictionary values',
                    OutputInterface::VERBOSITY_VERBOSE
                );

                foreach ($dictionary->getItems() as $item) {
                    $validValues[] = $item->getId();
                }
                $criteria->andWhere($criteria::expr()->notIn($entityMeta['subType'], $validValues));

            } else {
                $io->writeln(
                    'dictionary empty, search all, except default dictionary value',
                    OutputInterface::VERBOSITY_VERBOSE
                );

                $criteria->andWhere($criteria::expr()->neq($entityMeta['subType'], $to));
            }
            $io->text('search entity with invalid ' . $dictionaryType->getValue() . ' values...');

        } else {
            $from = $input->getOption('from-value');
            if ($from === null) {
                $from = 0;
            }
            $io->writeln(
                'search equal from value',
                OutputInterface::VERBOSITY_VERBOSE
            );

            $criteria->andWhere($criteria::expr()->eq($entityMeta['subType'], $from));
            $io->text('search entity with ' . $from . ' ' . $dictionaryType->getValue() . ' value...');
        }

        $result = $repository->matching($criteria);
        $io->note('Found ' . $result->count() . ' entities ' . $entityMeta['class']);
        if ($result->count() === 0) {
            $io->warning('Entities not found, nothing to change');
            return 0;
        }
        foreach ($result as $entity) {
            if ($input->getOption('dry-run') || $output->getVerbosity() > OutputInterface::VERBOSITY_NORMAL) {
                $entityId = $this->propertyAccessor->getValue($entity, 'id');
                $oldValue = $this->propertyAccessor->getValue($entity, $entityMeta['subType']);
                $newValue = $to;
                $io->text($entityId . ': ' . $oldValue . ' -> ' . $newValue);
            }

            if (!$input->getOption('dry-run')) {
                $this->propertyAccessor->setValue(
                    $entity,
                    $entityMeta['subType'],
                    $to
                );
            }
        }
        if (!$input->getOption('dry-run')) {
            $this->entityManager->flush();
            $io->success('In ' . $entityMeta['class'].'::'.$entityMeta['subType']. ' filled successfully');
        }

        return 0;
    }
}
