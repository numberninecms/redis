<?php

/*
 * This file is part of the NumberNine package.
 *
 * (c) William Arin <williamarin.dev@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NumberNine\RedisBundle\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function NumberNine\Common\Util\ConfigUtil\file_env_variable_exists;
use function NumberNine\Common\Util\ConfigUtil\file_put_env_variable;

final class InstallCommand extends Command
{
    protected static $defaultName = 'numbernine:install:redis';

    private string $projectPath;

    public function __construct(string $projectPath)
    {
        parent::__construct();
        $this->projectPath = $projectPath;
    }

    protected function configure(): void
    {
        $this
            ->addOption('force', null, InputOption::VALUE_NONE, "Force recreation of Redis environment variables")
            ->setDescription('Configuration wizard for Redis');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');

        $envFile = $this->projectPath . '/.env.local';

        if ($force || !file_env_variable_exists($envFile, 'REDIS_URL')) {
            if (($result = $this->createRedisString($io, $envFile)) !== Command::SUCCESS) {
                return $result;
            }
        }

        $io->success('Redis install complete.');

        return Command::SUCCESS;
    }

    private function createRedisString(SymfonyStyle $io, string $envFile): int
    {
        $io->title('Redis settings');

        $host = $io->ask('Host', 'localhost', function ($host) {
            if (empty($host)) {
                throw new RuntimeException('Host cannot be empty.');
            }

            return $host;
        });

        $port = $io->ask('Port', '6379', function ($port) {
            if (!is_numeric($port)) {
                throw new RuntimeException('Port must be a number.');
            }

            return (int) $port;
        });

        if (!file_put_env_variable($envFile, 'REDIS_URL', sprintf('redis://%s:%d', $host, $port))) {
            $io->error("Unable to create file '.env.local'");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
