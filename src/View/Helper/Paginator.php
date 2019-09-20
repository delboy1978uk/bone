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
        $this->pageCount = (int)ceil($rowCount / $numPerPage) ?: 1;
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

        $html .= $this->renderRewind(Icon::FAST_BACKWARD, true);
        $html .= $this->renderRewind();

        for ($x = $start; $x <= ($start + ($pages - 1)); $x++) {
            $html .= $this->renderBox($x);
        }

        $html .= $this->renderForward();
        $html .= $this->renderForward(Icon::FAST_FORWARD, true);
        $html .= '</ul></nav>';

        return $html;
    }

    /**
     * @param string $icon
     * @param bool $fastBackward
     * @return string
     */
    private function renderRewind($icon = Icon::BACKWARD, bool $fastBackward = false): string
    {
        $urlPageNo = $fastBackward ? 1 : $this->currentPage - 1;
        $html = ($this->currentPage === 1) ? '<li class="page-item disabled">' : '<li class="page-item">';
        if ($this->currentPage === 1) {
            $html .= '<a class="page-link"  href ="#">' . Icon::custom($icon, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($urlPageNo) . '">' . $icon . '</a>';
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * @param string $icon
     * @param bool $fastForward
     * @return string
     */
    private function renderForward($icon = Icon::FORWARD, bool $fastForward = false): string
    {
        $urlPageNo = $fastForward ? $this->pageCount : $this->currentPage + 1;
        $html = ($this->currentPage >= $this->pageCount) ? '<li class="page-item disabled">' : '<li class="page-item">';
        if ($this->currentPage >= $this->pageCount) {
            $html .= '<a class="page-link" href="#">' . Icon::custom($icon, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($urlPageNo) . '">' . $icon . '</i></a>';
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * @param int $pageNo
     * @return string
     */
    private function renderBox(int $pageNo): string
    {
        $html = '<li class="page-item ';
        if ($this->currentPage === $pageNo) {
            $html .= ' active" aria-current="page';
        }
        $html .= '">';
        if ($this->currentPage === $pageNo) {
            $html .= '<a class="page-link" href="#">' . $pageNo . '</a>';
        } else {
            $html .= '<a class="page-link" href="' . $this->url($pageNo) . '">' . $pageNo . '</a>';
        }
        $html .= '</li>';

        return $html;
    }
}
