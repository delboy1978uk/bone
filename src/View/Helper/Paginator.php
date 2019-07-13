<?php

namespace Bone\View\Helper;

use Bone\View\Helper\Exception\PaginatorException;
use Del\Icon;

class Paginator
{
    private $currentPage = 1;
    private $customNext;
    private $customPrev;
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
        $this->pageCount = (int) ceil($rowCount / $numPerPage);;
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
     * @param $url
     */
    public function setCustomPrev($url): void
    {
        $this->customPrev = '<a href="' . $url . '/"><i class="icon-backward"></i></a>';
    }


    /**
     * @param $url
     */
    public function setCustomNext(string $url)
    {
        $this->customNext = '<a href="' . $url . '/"><i class="icon-forward"></i></a>';
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
        if (!$this->pagerSize) {
            $this->pagerSize = 5;
        }
        return $this->pagerSize;
    }

    /**
     * @return string
     * @throws PaginatorException
     */
    public function render(): string
    {
        if (!$this->pageCount) {
            throw new PaginatorException(PaginatorException::NO_PAGE_COUNT);
        }

        if (!$this->url) {
            throw new PaginatorException(PaginatorException::NO_URL);
        }

        if (!$this->urlPart) {
            throw new PaginatorException(PaginatorException::NO_URL_PART);
        }

        if (!$this->currentPage) {
            throw new PaginatorException(PaginatorException::NO_CURRENT_PAGE);
        }

        $html = '<nav><ul class="pagination">';

        if ($this->pageCount > ($this->getPagerSize() - 1)) {
            $pages = $this->getPagerSize();
            $half = ($pages - 1) / 2;
            if ($this->currentPage === 1) {
                $start = 1;
            } elseif ($this->currentPage === 2) {
                $start = 1;
            } elseif ($this->currentPage >= ($this->pageCount - $half)) {
                $start = $this->pageCount - ($this->getPagerSize() - 1);
            } else {
                $start = $this->currentPage - $half;
                if ($start < 1) {
                    $start = 1;
                }
            }
        } else {
            $pages = $this->pageCount;
            $start = 1;
        }

        $html .= ($start === 1) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if (isset($this->customPrev)) {
            $html .= $this->customPrev;
        } elseif ($start === 1) {
            $html .= '<a class="page-link"  href ="#">' . Icon::custom(Icon::FAST_BACKWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url(1) . '">' . Icon::FAST_BACKWARD . '</a>';
        }
        $html .= '</li>';

        $html .= ($this->currentPage === 1) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if (isset($this->customPrev)) {
            $html .= $this->customPrev;
        } elseif ($this->currentPage === 1) {
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

        $html .= (($start + ($pages - 1)) >= $this->currentPage) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if (isset($this->customNext)) {
            $html .= $this->customNext;
        } elseif (($start + ($pages - 1)) >= $this->currentPage) {
            $html .= '<a class="page-link" href="#">' . Icon::custom(Icon::FORWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($this->currentPage + 1) . '">' . Icon::FORWARD . '</i></a>';
        }
        $html .= '</li>';

        $html .= (($start + ($pages - 1)) >= $this->pageCount) ? '<li class="page-item disabled">' :'<li class="page-item">';
        if (isset($this->customNext)) {
            $html .= $this->customNext;
        } elseif (($start + ($pages - 1)) >= $this->pageCount) {
            $html .= '<a class="page-link" href="#">' . Icon::custom(Icon::FAST_FORWARD, 'disabled') . '</a>';
        } else {
            $html .= '<a class="page-link"  href ="' . $this->url($this->pageCount) . '">' . Icon::FAST_FORWARD . '</i></a>';
        }
        $html .= '</li>';
        $html .= '</ul></nav>';

        return $html;
    }
}
