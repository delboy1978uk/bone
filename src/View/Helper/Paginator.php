<?php

namespace Bone\View\Helper;

use Bone\View\Helper\Exception\PaginatorException;
use Del\Icon;

class Paginator
{
    private $currentPage = 1;
    private $pageCount;
    private $pagerSize = 5;
    private $url;
    private $urlPart = ':page';

    /**
     * @param int $pageNum
     * @return string
     */
    private function url(int $pageNum): string
    {
        return str_replace($this->urlPart, $pageNum, $this->url);
    }

    /**
     * @param $pageCount
     */
    public function setPageCount(int $pageCount): void
    {
        $this->pageCount = $pageCount;
    }

    /**
     * @param $pageCount
     */
    public function setPageCountByTotalRecords(int $rowCount, int $numPerPage): void
    {
        $this->pageCount = (int) ceil($rowCount / $numPerPage) ?: 1;
    }

    /**
     * @param $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @param $replace
     */
    public function setUrlPart(string $replace): void
    {
        $this->urlPart = $replace;
    }

    /**
     * @param int $page_no
     */
    public function setCurrentPage(int $page_no): void
    {
        $this->currentPage = $page_no;
    }

    /**
     * @param int $numBoxes an ODD number!
     */
    public function setPagerSize(int $numBoxes): void
    {
        if ($numBoxes % 2 === 0) {
            $numBoxes--;
        }
        $this->pagerSize = $numBoxes;
    }

    /**
     * @return int
     */
    public function getPagerSize(): int
    {
        return $this->pagerSize;
    }

    /**
     * @throws PaginatorException
     */
    private function ensurePageCount()
    {
        if (null === $this->pageCount) {
            throw new PaginatorException(PaginatorException::NO_PAGE_COUNT);
        }
    }

    /**
     * @throws PaginatorException
     */
    private function ensureUrl()
    {
        codecept_debug($this->url);
        if (null === $this->url) {
            throw new PaginatorException(PaginatorException::NO_URL);
        }
    }

    /**
     * @return int
     */
    private function calculateStart(int $pages): int
    {
        $half = ($pages - 1) / 2;
        if ($this->currentPage < 3) {
            $start = 1;
        } elseif ($this->currentPage >= ($this->pageCount - $half)) {
            $start = $this->pageCount - ($this->getPagerSize() - 1);
        } else {
            $start = $this->currentPage - $half;
            if ($start < 1) {
                $start = 1;
            }
        }

        return $start;
    }

    /**
     * @return string
     * @throws PaginatorException
     */
    public function render(): string
    {
        $this->ensurePageCount();
        $this->ensureUrl();

        $html = '<nav><ul class="pagination">';

        if ($this->pageCount > ($this->getPagerSize() - 1)) {
            $pages = $this->getPagerSize();
            $start = $this->calculateStart($pages);
        } else {
            $pages = $this->pageCount;
            $start = 1;
        }

        $html .= ($this->currentPage === 1) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if ($this->currentPage === 1) {
            $html .= '<a class="page-link"  href ="#">' . Icon::custom(Icon::FAST_BACKWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url(1) . '">' . Icon::FAST_BACKWARD . '</a>';
        }
        $html .= '</li>';

        $html .= ($this->currentPage === 1) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if ($this->currentPage === 1) {
            $html .= '<a class="page-link"  href ="#">' . Icon::custom(Icon::BACKWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($this->currentPage - 1) . '">' . Icon::BACKWARD . '</a>';
        }
        $html .= '</li>';

        for ($x = $start; $x <= ($start + ($pages - 1)); $x++) {
            $html .= '<li class="page-item ';
            if ($this->currentPage === $x) {
                $html .= ' active" aria-current="page';
            }
            $html .= '">';
            if ($this->currentPage === $x) {
                $html .= '<a class="page-link" href="#">' . $x . '</a>';
            } else {
                $html .= '<a class="page-link" href="' . $this->url($x) .'">' . $x . '</a>';
            }
            $html .= '</li>';
        }

        $html .= ($this->currentPage >= $this->pageCount) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if ($this->currentPage >= $this->pageCount) {
            $html .= '<a class="page-link" href="#">' . Icon::custom(Icon::FORWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($this->currentPage + 1) . '">' . Icon::FORWARD . '</i></a>';
        }
        $html .= '</li>';

        $html .= ($this->currentPage >= $this->pageCount) ? '<li class="page-item disabled">' : '<li class="page-item">';
        if ($this->currentPage >= $this->pageCount) {
            $html .= '<a class="page-link" href="#">' . Icon::custom(Icon::FAST_FORWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($this->pageCount) . '">' . Icon::FAST_FORWARD . '</i></a>';
        }
        $html .= '</li>';
        $html .= '</ul></nav>';

        return $html;
    }
}
