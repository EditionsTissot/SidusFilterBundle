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

namespace Sidus\FilterBundle\Factory;

use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfiguration;
use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;
use Symfony\Component\PropertyAccess\Exception\ExceptionInterface;

/**
 * Converts an array configuration into QueryHandlerConfiguration objects
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class QueryHandlerConfigurationFactory implements QueryHandlerConfigurationFactoryInterface
{
    /** @var FilterFactoryInterface */
    protected $filterFactory;

    public function __construct(FilterFactoryInterface $filterFactory)
    {
        $this->filterFactory = $filterFactory;
    }

    /**
     * @throws \UnexpectedValueException
     * @throws ExceptionInterface
     */
    public function createQueryHandlerConfiguration(
        string $code,
        array $configuration
    ): QueryHandlerConfigurationInterface {
        /** @var array[] $filters */
        $filters = $configuration['filters'];
        unset($configuration['filters']);

        $queryHandlerConfiguration = new QueryHandlerConfiguration(
            $code,
            $configuration
        );

        foreach ($filters as $filterCode => $filterConfiguration) {
            $queryHandlerConfiguration->addFilter(
                $this->filterFactory->createFilter($queryHandlerConfiguration, $filterCode, $filterConfiguration)
            );
        }

        return $queryHandlerConfiguration;
    }
}
