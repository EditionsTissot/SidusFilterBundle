<?php

namespace Sidus\FilterBundle\Query\Handler;

use Pagerfanta\Exception\InvalidArgumentException;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sidus\FilterBundle\DTO\SortConfig;
use Sidus\FilterBundle\Form\Type\OrderButtonType;
use Sidus\FilterBundle\Form\Type\SortConfigType;
use Sidus\FilterBundle\Query\Handler\Configuration\QueryHandlerConfigurationInterface;
use Sidus\FilterBundle\Registry\FilterTypeRegistry;
use Symfony\Component\Form\Exception\AlreadySubmittedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;

/**
 * Build the necessary logic around filters based on a configuration
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
abstract class AbstractQueryHandler implements QueryHandlerInterface
{
    public const FILTERS_FORM_NAME = 'filters';
    public const SORTABLE_FORM_NAME = 'sortable';
    public const SORT_CONFIG_FORM_NAME = 'config';

    /** @var FilterTypeRegistry */
    protected $filterTypeRegistry;

    /** @var QueryHandlerConfigurationInterface */
    protected $configuration;

    /** @var Form */
    protected $form;

    /** @var SortConfig */
    protected $sortConfig;

    /** @var Pagerfanta */
    protected $pager;

    /**
     * @param FilterTypeRegistry                 $filterTypeRegistry
     * @param QueryHandlerConfigurationInterface $configuration
     */
    public function __construct(
        FilterTypeRegistry $filterTypeRegistry,
        QueryHandlerConfigurationInterface $configuration
    ) {
        $this->filterTypeRegistry = $filterTypeRegistry;
        $this->configuration = $configuration;
        $this->sortConfig = new SortConfig();
        /** @noinspection LoopWhichDoesNotLoopInspection */
        foreach ($configuration->getDefaultSort() as $column => $direction) {
            $this->sortConfig = new SortConfig($column, strtolower($direction) === 'desc');
            break;
        }
    }

    /**
     * @return QueryHandlerConfigurationInterface
     */
    public function getConfiguration(): QueryHandlerConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * @param Request $request
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws NotValidCurrentPageException
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     * @throws \UnexpectedValueException
     */
    public function handleRequest(Request $request)
    {
        $this->getForm()->handleRequest($request);
        $this->handleForm($request->get('page'));
    }

    /**
     * @param array $data
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws AlreadySubmittedException
     * @throws NotValidCurrentPageException
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     * @throws \UnexpectedValueException
     */
    public function handleArray(array $data = [])
    {
        $this->getForm()->submit($data);
        $this->handleForm($data['page'] ?? null);
    }

    /**
     * @return FormInterface
     * @throws \LogicException
     */
    public function getForm(): FormInterface
    {
        if (!$this->form) {
            throw new \LogicException(
                'You must first build the form by calling buildForm($builder) with your form builder'
            );
        }

        return $this->form;
    }

    /**
     * @return SortConfig
     */
    public function getSortConfig(): SortConfig
    {
        return $this->sortConfig;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @throws \UnexpectedValueException
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     *
     * @return FormInterface
     */
    public function buildForm(FormBuilderInterface $builder): FormInterface
    {
        $this->buildFilterForm($builder);
        $this->buildSortableForm($builder);

        $this->form = $builder->getForm();

        return $this->form;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return Pagerfanta
     */
    public function getPager(): Pagerfanta
    {
        if (null === $this->pager) {
            $this->applyPager();
        }

        return $this->pager;
    }

    /**
     * @param FormBuilderInterface $builder
     */
    protected function buildSortableForm(FormBuilderInterface $builder)
    {
        $sortableBuilder = $builder->create(
            self::SORTABLE_FORM_NAME,
            FormType::class,
            [
                'label' => false,
            ]
        );
        $sortableBuilder->add(
            self::SORT_CONFIG_FORM_NAME,
            SortConfigType::class,
            [
                'data' => $this->getSortConfig(),
            ]
        );
        foreach ($this->getConfiguration()->getSortable() as $sortable) {
            $sortableBuilder->add(
                $sortable,
                OrderButtonType::class,
                [
                    'sort_config' => $this->getSortConfig(),
                ]
            );
        }
        $builder->add($sortableBuilder);
    }

    /**
     * @todo : Put in form event ?
     * @throws \LogicException
     * @throws \OutOfBoundsException
     */
    protected function applySortForm()
    {
        $form = $this->getForm();
        $sortableForm = $form->get(self::SORTABLE_FORM_NAME);
        /** @var FormInterface $sortConfigForm */
        $sortConfigForm = $sortableForm->get(self::SORT_CONFIG_FORM_NAME);
        /** @var SortConfig $sortConfig */
        $sortConfig = $sortConfigForm->getData();

        foreach ($this->getConfiguration()->getSortable() as $sortable) {
            /** @var SubmitButton $button */
            $button = $sortableForm->get($sortable);
            if ($button->isClicked()) {
                if ($sortConfig->getColumn() === $sortable) {
                    $sortConfig->switchDirection();
                } else {
                    $sortConfig->setColumn($sortable);
                    $sortConfig->setDirection(false);
                }
            }
        }

        return $sortConfig;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     * @throws \UnexpectedValueException
     */
    protected function buildFilterForm(FormBuilderInterface $builder)
    {
        $filtersBuilder = $builder->create(
            self::FILTERS_FORM_NAME,
            FormType::class,
            [
                'label' => false,
            ]
        );
        foreach ($this->getConfiguration()->getFilters() as $filter) {
            $filterType = $this->filterTypeRegistry->getFilterType(
                $this->getConfiguration()->getProvider(),
                $filter->getFilterType()
            );
            $formOptions = array_merge(
                ['required' => false],
                $filterType->getFormOptions($this, $filter)
            );
            $filtersBuilder->add(
                $filter->getCode(),
                $filter->getFormType() ?? $filterType->getFormType(),
                $formOptions
            );
        }
        $builder->add($filtersBuilder);
    }

    /**
     * @param int $selectedPage
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     * @throws \UnexpectedValueException
     */
    protected function handleForm($selectedPage = null)
    {
        $this->applyFilters(); // maybe do it in a form event ?
        $this->applySort($this->applySortForm());
        $this->applyPager($selectedPage); // merge with filters ?
    }

    /**
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \UnexpectedValueException
     * @throws \Sidus\FilterBundle\Exception\BadQueryHandlerException
     */
    protected function applyFilters()
    {
        $form = $this->getForm();
        $filterForm = $form->get(self::FILTERS_FORM_NAME);
        foreach ($this->getConfiguration()->getFilters() as $filter) {
            $filterType = $this->filterTypeRegistry->getFilterType(
                $this->getConfiguration()->getProvider(),
                $filter->getFilterType()
            );
            $filterType->handleForm($this, $filter, $filterForm->get($filter->getCode()));
        }
    }

    /**
     * @param SortConfig $sortConfig
     */
    abstract protected function applySort(SortConfig $sortConfig);

    /**
     * @param int $selectedPage
     */
    abstract protected function applyPager($selectedPage = null);
}