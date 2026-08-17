<?php

declare(strict_types=1);

namespace Supplycart\Money\Console;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'money:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install package';

    public function handle(): int
    {
        $this->components->info('The Supplycart Money package is installed.');

        return self::SUCCESS;
    }
}
