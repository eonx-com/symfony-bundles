<?php
declare(strict_types=1);

namespace LoyaltyCorp\CoreBundle\Pagination;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;

final class CustomPaginator implements CustomPaginationInterface
{
    /** @var \ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator */
    private $decorated;

    /**
     * CustomPaginator constructor.
     *
     * @param \ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator $decorated
     */
    public function __construct(Paginator $decorated)
    {
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): array
    {
        return \iterator_to_array($this->decorated->getIterator());
    }

    /**
     * {@inheritdoc}
     */
    public function getPagination(): array
    {
        $hasNextPage = $this->decorated->getCurrentPage() < $this->decorated->getLastPage();
        $hasPreviousPage = $this->decorated->getCurrentPage() - 1 > 0;

        return [
            'current_page' => $this->decorated->getCurrentPage(),
            'has_next_page' => $hasNextPage,
            'has_previous_page' => $hasPreviousPage,
            'items_per_page' => $this->decorated->getItemsPerPage(),
            'total_items' => $this->decorated->getTotalItems(),
            'total_pages' => $this->decorated->getLastPage()
        ];
    }
}
