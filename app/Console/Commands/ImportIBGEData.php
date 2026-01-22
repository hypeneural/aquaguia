<?php

namespace App\Console\Commands;

use App\Models\City;
use App\Models\State;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportIBGEData extends Command
{
    protected $signature = 'import:ibge 
                            {--uf-file= : Path to the UF CSV file}
                            {--city-file= : Path to the municipalities CSV file}
                            {--only-states : Import only states}
                            {--only-cities : Import only cities}';

    protected $description = 'Import IBGE geographic data (states and cities) from CSV files';

    private int $statesImported = 0;
    private int $citiesImported = 0;
    private int $citiesSkipped = 0;

    public function handle(): int
    {
        $ufFile = $this->option('uf-file');
        $cityFile = $this->option('city-file');

        if (!$ufFile && !$cityFile) {
            $this->error('Please provide at least one file path: --uf-file or --city-file');
            return self::FAILURE;
        }

        DB::beginTransaction();

        try {
            // Import states first
            if ($ufFile && !$this->option('only-cities')) {
                $this->importStates($ufFile);
            }

            // Then import cities
            if ($cityFile && !$this->option('only-states')) {
                $this->importCities($cityFile);
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ Import completed successfully!');
            $this->table(
                ['Entity', 'Imported', 'Skipped'],
                [
                    ['States', $this->statesImported, 0],
                    ['Cities', $this->citiesImported, $this->citiesSkipped],
                ]
            );

            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Import failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }

    private function importStates(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("UF file not found: {$filePath}");
        }

        $this->info('📍 Importing states from: ' . $filePath);

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

        // Map header columns
        $columns = array_flip($header);

        $bar = $this->output->createProgressBar();
        $bar->start();

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || !isset($row[$columns['sigla']])) {
                continue;
            }

            $abbr = trim($row[$columns['sigla']]);
            $name = trim($row[$columns['nome']]);

            if (empty($abbr) || empty($name)) {
                continue;
            }

            State::updateOrCreate(
                ['abbr' => $abbr],
                ['name' => $name]
            );

            $this->statesImported++;
            $bar->advance();
        }

        fclose($handle);
        $bar->finish();
        $this->newLine();
        $this->info("   → Imported {$this->statesImported} states");
    }

    private function importCities(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Cities file not found: {$filePath}");
        }

        $this->info('🏙️ Importing cities from: ' . $filePath);

        // Pre-load all states for faster lookup
        $states = State::pluck('id', 'abbr')->toArray();

        if (empty($states)) {
            $this->warn('   ⚠ No states found in database. Please import states first.');
            return;
        }

        $handle = fopen($filePath, 'r');
        $header = fgetcsv($handle);

        // Map header columns
        $columns = array_flip($header);

        // Get total lines for progress bar
        $totalLines = $this->countLines($filePath) - 1;
        $bar = $this->output->createProgressBar($totalLines);
        $bar->start();

        $batch = [];
        $batchSize = 500;

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || !isset($row[$columns['nome']])) {
                $bar->advance();
                continue;
            }

            $name = trim($row[$columns['nome']]);
            $stateAbbr = trim($row[$columns['sigla_uf']]);

            if (empty($name) || empty($stateAbbr)) {
                $this->citiesSkipped++;
                $bar->advance();
                continue;
            }

            if (!isset($states[$stateAbbr])) {
                $this->citiesSkipped++;
                $bar->advance();
                continue;
            }

            $stateId = $states[$stateAbbr];

            // Check if city already exists
            $exists = City::where('name', $name)
                ->where('state_id', $stateId)
                ->exists();

            if ($exists) {
                $this->citiesSkipped++;
                $bar->advance();
                continue;
            }

            // Create city
            City::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'state_id' => $stateId,
                'featured' => false,
                'image' => null,
            ]);

            $this->citiesImported++;
            $bar->advance();
        }

        fclose($handle);
        $bar->finish();
        $this->newLine();
        $this->info("   → Imported {$this->citiesImported} cities, skipped {$this->citiesSkipped}");
    }

    private function countLines(string $filePath): int
    {
        $count = 0;
        $handle = fopen($filePath, 'r');
        while (!feof($handle)) {
            fgets($handle);
            $count++;
        }
        fclose($handle);
        return $count;
    }
}
