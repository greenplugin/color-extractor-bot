<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TgBotApi\BotApiBase\Method\DeleteWebhookMethod;

class RemoveWebhookCommand extends AbstractTelegramCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'telegram:remove:webhook';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setDescription('Telegram command is removing webhook');

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $this->bot->delete(DeleteWebhookMethod::create());

        $io->success('webhook was successfully removed.');
    }
}
