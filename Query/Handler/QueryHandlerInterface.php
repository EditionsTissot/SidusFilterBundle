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

namespace Sidus\FilterBundle\Query\Handler;

use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\Pagerfanta;
use Sidus\FilterBundle\DTO\SortConfig;
use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Describes the bare minimum api to work with filters
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
interface QueryHandlerInterface
{
    public function getConfiguration(): QueryHandlerConfigurationInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function getPager(): Pagerfanta;

    public function handleRequest(Request $request);

    public function handleArray(array $data = []);

    public function getForm(): FormInterface;

    public function getSortConfig(): SortConfig;

    public function buildForm(FormBuilderInterface $builder): FormInterface;
}
