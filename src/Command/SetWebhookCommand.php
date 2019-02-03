<?php

namespace App\Command;

use Http\Client\Exception\HttpException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use TgBotApi\BotApiBase\BotApiInterface;
use TgBotApi\BotApiBase\Method\SetWebhookMethod;

class SetWebhookCommand extends AbstractTelegramCommand
{
    /**
     * @var string
     */
    protected static $defaultName = 'telegram:set:webhook';

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * SetWebhookCommand constructor.
     * @param BotApiInterface $bot
     * @param ParameterBagInterface $parameterBag
     * @param RouterInterface $router
     * @param null $name
     */
    public function __construct(
        BotApiInterface $bot,
        ParameterBagInterface $parameterBag,
        RouterInterface $router,
        $name = null
    ) {
        parent::__construct($bot, $parameterBag, $name);
        $this->router = $router;
    }

    /**
     * @inheritdoc
     */
    protected function configure():void
    {
        $this->setDescription('Ths command is setting webhook');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \TgBotApi\BotApiBase\Exception\BadArgumentException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $url = $this->router->generate(
            'webhook_path',
            ['token' => $this->parameterBag->get('telegram.webhook_secret')],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $io->success(sprintf('Webhook was set to %s ', $url));

        try {
            $this->bot->set(SetWebhookMethod::create($url));
        } catch (HttpException $exception) {
            $io->error(json_decode($exception->getResponse()->getBody())->description);
        }

        $io->success(sprintf('Webhook was set to %s ', $url));
    }
}
