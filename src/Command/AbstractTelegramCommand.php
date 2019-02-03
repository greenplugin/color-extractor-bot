<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TgBotApi\BotApiBase\BotApiInterface;

abstract class AbstractTelegramCommand extends Command
{
    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;
    /**
     * @var BotApiInterface
     */
    protected $bot;

    /**
     * AbstractTelegramCommand constructor.
     * @param BotApiInterface $bot
     * @param ParameterBagInterface $parameterBag
     * @param null $name
     */
    public function __construct(BotApiInterface $bot, ParameterBagInterface $parameterBag, $name = null)
    {
        parent::__construct($name);

        $this->bot = $bot;
        $this->parameterBag = $parameterBag;
    }
}
