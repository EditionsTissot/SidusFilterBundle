<?php
/*
 * This file is part of the Sidus/FilterBundle package.
 *
 * Copyright (c) 2015-2021 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sidus\FilterBundle\Registry;

use Sidus\FilterBundle\Exception\MissingFilterException;
use Sidus\FilterBundle\Exception\MissingQueryHandlerException;
use Sidus\FilterBundle\Exception\MissingQueryHandlerFactoryException;
use Sidus\FilterBundle\Factory\QueryHandlerConfigurationFactoryInterface;
use Sidus\FilterBundle\Factory\QueryHandlerFactoryInterface;
use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;
use Sidus\FilterBundle\Query\Handler\QueryHandlerInterface;

/**
 * Holds all query handler configurations and how to build them
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class QueryHandlerRegistry
{
    /** @var QueryHandlerConfigurationFactoryInterface */
    protected $queryHandlerConfigurationFactory;

    /** @var QueryHandlerFactoryInterface[] */
    protected $queryHandlerFactories = [];

    /** @var array[] */
    protected $rawQueryHandlerConfigurations = [];

    /** @var QueryHandlerConfigurationInterface[] */
    protected $queryHandlerConfigurations = [];

    /** @var QueryHandlerInterface[] */
    protected $queryHandlers = [];

    public function __construct(
        QueryHandlerConfigurationFactoryInterface $queryHandlerConfigurationFactory
    ) {
        $this->queryHandlerConfigurationFactory = $queryHandlerConfigurationFactory;
    }

    /**
     * Used by the dependency injection system to add raw configuration items from YML config
     *
     * @internal Warning this method does not validate the given configuration
     */
    public function addRawQueryHandlerConfiguration(string $code, array $configuration): void
    {
        $this->rawQueryHandlerConfigurations[$code] = $configuration;
    }

    public function addQueryHandlerConfiguration(QueryHandlerConfigurationInterface $queryHandlerConfiguration): void
    {
        $this->queryHandlerConfigurations[$queryHandlerConfiguration->getCode()] = $queryHandlerConfiguration;
    }

    public function addQueryHandler(QueryHandlerInterface $queryHandler): void
    {
        $this->queryHandlers[$queryHandler->getConfiguration()->getCode()] = $queryHandler;
    }

    public function addQueryHandlerFactory(QueryHandlerFactoryInterface $queryHandlerFactory): void
    {
        $this->queryHandlerFactories[$queryHandlerFactory->getProvider()] = $queryHandlerFactory;
    }

    /**
     * @throws MissingFilterException
     * @throws MissingQueryHandlerException
     * @throws MissingQueryHandlerFactoryException
     */
    public function getQueryHandler(string $code): QueryHandlerInterface
    {
        if (!array_key_exists($code, $this->queryHandlers)) {
            return $this->buildQueryHandler($code);
        }

        return $this->queryHandlers[$code];
    }

    public function hasQueryHandler(string $code): bool
    {
        return array_key_exists($code, $this->queryHandlers)
            || array_key_exists($code, $this->queryHandlerConfigurations)
            || array_key_exists($code, $this->rawQueryHandlerConfigurations);
    }

    /**
     * @throws MissingQueryHandlerException
     */
    protected function getQueryHandlerConfiguration(string $code): QueryHandlerConfigurationInterface
    {
        if (!array_key_exists($code, $this->queryHandlerConfigurations)) {
            return $this->buildQueryHandlerConfiguration($code);
        }

        return $this->queryHandlerConfigurations[$code];
    }

    /**
     * @throws MissingFilterException
     * @throws MissingQueryHandlerException
     * @throws MissingQueryHandlerFactoryException
     */
    protected function buildQueryHandler(string $code): QueryHandlerInterface
    {
        $configuration = $this->getQueryHandlerConfiguration($code);
        $queryHandlerFactory = $this->getQueryHandlerFactory($configuration->getProvider());
        $queryHandler = $queryHandlerFactory->createQueryHandler($configuration);
        $this->queryHandlers[$code] = $queryHandler;
        unset($this->queryHandlerConfigurations[$code]);

        return $queryHandler;
    }

    /**
     * @throws MissingQueryHandlerException
     */
    protected function buildQueryHandlerConfiguration(string $code): QueryHandlerConfigurationInterface
    {
        if (!array_key_exists($code, $this->rawQueryHandlerConfigurations)) {
            throw new MissingQueryHandlerException($code);
        }
        $queryHandlerConfiguration = $this->queryHandlerConfigurationFactory->createQueryHandlerConfiguration(
            $code,
            $this->rawQueryHandlerConfigurations[$code]
        );
        $this->queryHandlerConfigurations[$code] = $queryHandlerConfiguration;
        unset($this->rawQueryHandlerConfigurations[$code]);

        return $queryHandlerConfiguration;
    }

    /**
     * @throws MissingQueryHandlerFactoryException
     */
    protected function getQueryHandlerFactory(string $provider): QueryHandlerFactoryInterface
    {
        if (!array_key_exists($provider, $this->queryHandlerFactories)) {
            throw new MissingQueryHandlerFactoryException($provider);
        }

        return $this->queryHandlerFactories[$provider];
    }
}
