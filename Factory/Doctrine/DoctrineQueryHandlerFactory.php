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

namespace Sidus\FilterBundle\Factory\Doctrine;

use Doctrine\Persistence\ManagerRegistry;
use Sidus\FilterBundle\Factory\QueryHandlerFactoryInterface;
use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;
use Sidus\FilterBundle\Query\Handler\Doctrine\DoctrineQueryHandler;
use Sidus\FilterBundle\Query\Handler\QueryHandlerInterface;
use Sidus\FilterBundle\Registry\FilterTypeRegistry;

/**
 * Dedicated logic for Doctrine query handler
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class DoctrineQueryHandlerFactory implements QueryHandlerFactoryInterface
{
    /** @var FilterTypeRegistry */
    protected $filterTypeRegistry;

    /** @var ManagerRegistry */
    protected $doctrine;

    public function __construct(FilterTypeRegistry $filterTypeRegistry, ManagerRegistry $doctrine)
    {
        $this->filterTypeRegistry = $filterTypeRegistry;
        $this->doctrine = $doctrine;
    }

    /**
     * @throws \UnexpectedValueException
     */
    public function createQueryHandler(
        QueryHandlerConfigurationInterface $queryHandlerConfiguration
    ): QueryHandlerInterface {
        return new DoctrineQueryHandler($this->filterTypeRegistry, $queryHandlerConfiguration, $this->doctrine);
    }

    public function getProvider(): string
    {
        return 'doctrine';
    }
}
