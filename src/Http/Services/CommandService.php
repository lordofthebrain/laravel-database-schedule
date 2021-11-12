<?php

namespace RobersonFaria\DatabaseSchedule\Http\Services;

use App\Console\Kernel;
use Illuminate\Support\Collection;

class CommandService
{
    public function get(): Collection
    {
        $commands = collect(app(Kernel::class)->all())->sortKeys();
        $commandsKeys = $commands->keys()->toArray();
        foreach (config('database-schedule.commands.exclude') as $exclude) {
            $commandsKeys = preg_grep("/^$exclude/", $commandsKeys, PREG_GREP_INVERT);
        }
        return $commands->only($commandsKeys)
            ->map(function ($command) {
                return (object)[
                    'name' => $command->getName(),
                    'description' => $command->getDescription(),
                    'signature' => $command->getSynopsis(),
                    'arguments' => $this->getArguments($command),
                    'options' => $this->getOptions($command),
                ];
            });
    }

    private function getArguments($command): array
    {
        $arguments = [];
        foreach ($command->getDefinition()->getArguments() as $argument) {
            $arguments[] = (object)[
                'name' => $argument->getName(),
                'default' => $argument->getDefault(),
                'required' => $argument->isRequired()
            ];
        }

        return $arguments;
    }

    private function getOptions($command): object
    {
        $options = (object)[
            'withValue' => [],
            'withoutValue' => []
        ];
        foreach ($command->getDefinition()->getOptions() as $option)
        {
            if($option->acceptValue()) {
                $options->withValue[] = (object)[
                    'name' => $option->getName(),
                    'default' => $option->getDefault(),
                    'required' => $option->isValueRequired()
                ];
            } else {
                $options->withoutValue[] = $option->getName();
            }
        }

        return $options;
    }
}
