<?php

namespace Graze\DataFlow\Utility\ArrayFilter;

class AllOfFilter implements ArrayFilterInterface
{
    /**
     * @var ArrayFilterInterface[]
     */
    private $filters;

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }
    }

    /**
     * @param ArrayFilterInterface $filter
     */
    public function addFilter(ArrayFilterInterface $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * Loop through all the filters and only return if all match
     *
     * @param array $data
     * @return bool
     */
    public function matches($data)
    {
        foreach ($this->filters as $filter)
        {
            if (!$filter->matches($data)) {
                return false;
            }
        }

        return true;
    }
}
