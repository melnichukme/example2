<?php

namespace App\Command\Fill;

use App\Service\CountryService;
use App\Service\RegionService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'app.fill-country',
    description: 'Fill countries and regions to database',
)]
class FillCountryCommand extends Command
{
    /**
     * @var array
     */
    private $csvParsingOptions = [
        'finder_in' => 'src/Resources/',
        'finder_name' => 'country.csv',
        'ignoreFirstLine' => true
    ];

    public function __construct(
        private CountryService $countryService,
        private RegionService $regionService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csvData = $this->parseCSV();

        foreach ($csvData as $item) {
            if (is_null($region = $this->regionService->getByTitle($item[2]))) {
                $region = $this->regionService->create($item[2]);
            }

            if (!$this->countryService->getByCode($item[1])) {
                $this->countryService->create([
                    'name' => $item[0],
                    'code' => $item[1],
                    'region' => $region
                ]);
            }
        }

        return Command::SUCCESS;
    }

    /**
     * Parse a csv file
     *
     * @return array
     */
    private function parseCSV()
    {
        $ignoreFirstLine = $this->csvParsingOptions['ignoreFirstLine'];

        $finder = new Finder();
        $finder->files()
            ->in($this->csvParsingOptions['finder_in'])
            ->name($this->csvParsingOptions['finder_name']);
        foreach ($finder as $file) {
            $csv = $file;
        }

        $rows = array();
        if (($handle = fopen($csv->getRealPath(), "r")) !== false) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ";")) !== false) {
                $i++;
                if ($ignoreFirstLine && $i == 1) {
                    continue;
                }
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }
}
