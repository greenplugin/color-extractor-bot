<?php
declare(strict_types=1);

namespace App\Service;

use App\TelegramRouter\Exceptions\RouteExtractionException;
use App\TelegramRouter\Exceptions\RouterParameterException;
use App\TelegramRouter\Extractor\ExtractorInterface;
use App\TelegramRouter\RouteCollector;
use App\TelegramRouter\RouterUpdateInterface;
use App\TelegramRouter\TelegramResponse;
use App\TelegramRouter\TelegramResponseInterface;
use App\TelegramRouter\TelegramRoute;
use App\TelegramRouter\TelegramRouteCollection;
use Psr\Container\ContainerInterface;
use ReflectionFunctionAbstract;
use TgBotApi\BotApiBase\Type\UpdateType;

class TelegramRouter
{
    private $collection;
    private $container;

    public function __construct(TelegramRouteCollection $collection, ContainerInterface $container)
    {
        $this->collection = $collection;
        $this->container = $container;

        $collector = new RouteCollector($this->collection);
        $collector->collect();
    }

    /**
     * @param RouterUpdateInterface $update
     * @return TelegramResponseInterface|null
     * @throws RouteExtractionException
     * @throws RouterParameterException
     * @throws \ReflectionException
     */
    public function dispatch(RouterUpdateInterface $update): ?TelegramResponseInterface
    {
        if ($this->collection->get($update->getType())) {
            foreach ($this->collection->get($update->getType()) as $route) {
                if ($route->match($update)) {
                    $this->extractRouteData($update);
                    return $this->invokeUpdate($update);
                }
            }
        }
        return null;
    }

    /**
     * @param RouterUpdateInterface $update
     * @throws RouteExtractionException
     */
    private function extractRouteData(RouterUpdateInterface $update): void
    {
        foreach ($update->getRoute()->getExtractors() as [$extractor, $fields]) {
            if (is_string($extractor)) {
                $extractor = $this->container->get($extractor);
            }
            if (!($extractor instanceof ExtractorInterface)) {
                throw new RouteExtractionException('Extractor must be implement ' . ExtractorInterface::class);
            }
            $extractor->extract($update, $fields);
        }

    }

    /**
     * @param RouterUpdateInterface $update
     * @return TelegramResponse|null
     * @throws \ReflectionException
     * @throws RouterParameterException
     */
    private function invokeUpdate(RouterUpdateInterface $update): ?TelegramResponse
    {
        if ($update->getRoute()->getEndpoint() instanceof \Closure) {
            $reflectionFunction = new \ReflectionFunction($update->getRoute()->getEndpoint());
            return $reflectionFunction->invokeArgs($this->getInvokeParams($reflectionFunction, $update));
        }

        [$class, $method] = $this->getRouteClassAndMethod($update->getRoute());
        $reflectionMethod = new \ReflectionMethod(implode('::', [$class, $method]));
        return $reflectionMethod->invokeArgs(
            $this->container->get($class),
            $this->getInvokeParams($reflectionMethod, $update)
        );
    }

    /**
     * @param ReflectionFunctionAbstract $reflectionMethod
     * @param RouterUpdateInterface      $update
     * @return array
     * @throws RouterParameterException
     */
    private function getInvokeParams(
        ReflectionFunctionAbstract $reflectionMethod,
        RouterUpdateInterface $update
    ): array {
        $reflectionParams = $reflectionMethod->getParameters();
        $params = [];
        foreach ($reflectionParams as $param) {

            if ($update->getContext()->isSet($param->getName())) {
                $contextValue = $update->getContext()->get($param->getName());
                if (!$param->getType()) {
                    $params[] = $contextValue;
                    continue;
                }
                $typeName = $param->getType()->getName();
                if ($typeName === gettype($contextValue)) {
                    $params[] = $contextValue;
                    continue;
                }
                if ($contextValue instanceof $typeName) {
                    $params[] = $contextValue;
                    continue;
                }
            }

            if (!$param->getType()) {
                throw new RouterParameterException(sprintf('Param %s does not have the specified type.', $param->getName()));
            }

            $typeName = $param->getType()->getName();

            if ($typeName === UpdateType::class) {
                $params[] = $update->getUpdate();
                continue;
            }

            if ($update instanceof $typeName) {
                $params[] = $update;
                continue;
            }

            if (!$this->container->has($typeName)) {
                throw new RouterParameterException(
                    sprintf('%s $%s is not public in container, set your service public before using it in telegram controller.',
                        $typeName,
                        $param->getName()
                    )
                );
            }
            $params[] = $this->container->get($typeName);
        }

        return $params;
    }

    /**
     * @param TelegramRoute $route
     * @return array
     * @throws RouterParameterException
     */
    private function getRouteClassAndMethod(TelegramRoute $route): array
    {
        $routeParts = explode('::', $route->getEndpoint());
        if (count($routeParts) > 2) {
            throw new RouterParameterException(sprintf(
                '%s is not valid class and method name. please use class::method format or provide classname for invokable',
                $route->getEndpoint()
            ));
        }

        if (count($routeParts) === 2) {
            return $routeParts;
        }

        return [$routeParts[0], '__invoke'];
    }
}
