<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Prompts\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;

class StorageLinkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Event::listen(CommandFinished::class, function (CommandFinished $event) {
            if ($event->command === 'storage:link') {
                $this->runImageSymlinkCommand();
            }
        });
    }

    protected function runImageSymlinkCommand(): void
    {
        $bufferedOutput = new BufferedOutput();
        $consoleOutput = new ConsoleOutput();

        $consoleOutput->writeln('<question>Hooked storage:link command...</question>');

        $exitCode = Artisan::call('images:link', [], $bufferedOutput);

        $commandOutput = $bufferedOutput->fetch();

        $consoleOutput->writeln('<info>Running images:link command...</info>');
        $consoleOutput->writeln("<info>Output:</info>\n".$commandOutput);

        if ($exitCode !== 0) {
            $consoleOutput->writeln('<error>Error running images:link command.</error>');
            $consoleOutput->writeln("<error>Output:</error>\n".$commandOutput);
        } else {
            $consoleOutput->writeln('<info>images:link command finished successfully.</info>');
        }
    }
}
