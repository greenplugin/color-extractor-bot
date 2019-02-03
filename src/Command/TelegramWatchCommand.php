<?php

namespace App\Command;

use App\Service\TelegramUpdateService;
use App\TelegramRouter\RouterUpdateInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\GetUpdatesMethod;
use TgBotApi\BotApiBase\Type\UpdateType;

class TelegramWatchCommand extends AbstractTelegramCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'telegram:watch';

    /**
     * @var TelegramUpdateService
     */
    private $telegramUpdateService;

    /**
     * TelegramWatchCommand constructor.
     * @param BotApiInterface $bot
     * @param ParameterBagInterface $parameterBag
     * @param TelegramUpdateService $telegramUpdateService
     * @param null $name
     */
    public function __construct(
        BotApiInterface $bot,
        ParameterBagInterface $parameterBag,
        TelegramUpdateService $telegramUpdateService,
        $name = null
    ) {
        parent::__construct($bot, $parameterBag, $name);
        $this->telegramUpdateService = $telegramUpdateService;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setDescription('Watching telegram bot updates')
            ->addArgument('offset', InputArgument::OPTIONAL, 'offset of first message');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \TgBotApi\BotApiBase\Exception\BadArgumentException
     * @throws \TgBotApi\BotApiBase\Exception\ResponseException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('Listening updates');

        $offset = (int)$input->getArgument('offset');

        $updateParams = ['timeout' => 120];

        if ($offset) {
            $updateParams['offset'] = $offset;
        }

        while (true) {
            $io->title('Getting update...');
            $updates = $this->bot->getUpdates(GetUpdatesMethod::create($updateParams));
            foreach ($updates as $update) {
                $this->telegramUpdateService->acceptUpdate($update);
                $updateParams['offset'] = $update->updateId + 1;
                $io->success(sprintf('Incoming %s, with id %s', $this->getType($update), $update->updateId));
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }

    /**
     * @param UpdateType $update
     * @return string|null
     */
    public function getType(UpdateType $update): ?string
    {
        foreach (RouterUpdateInterface::UPDATE_TYPES as $type) {
            if ($update->$type) {
                return $type;
            }
        }
        return null;
    }
}
