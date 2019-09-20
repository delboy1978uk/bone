<?php

use Bone\View\Helper\Exception\PaginatorException;
use Bone\View\Helper\Paginator;
use Codeception\TestCase\Test;

class PaginatorTest extends Test
{
    /**
     * @throws Exception
     */
    public function testCreatePager()
    {
        $pager = new Paginator();
        $pager->setCurrentPage(2);
        $pager->setPageCount(5);
        $pager->setPagerSize(4);
        $pager->setUrl('https://bottlesofrum.com/cannons/:page');
        $pager->setUrlPart(':page');
        $this->assertEquals('<nav><ul class="pagination"><li class="page-item"><a class="page-link"  href ="https://bottlesofrum.com/cannons/1"><i class="fa fa-fast-backward"></i></a></li><li class="page-item"><a class="page-link"  href ="https://bottlesofrum.com/cannons/1"><i class="fa fa-backward"></i></a></li><li class="page-item "><a class="page-link" href="https://bottlesofrum.com/cannons/1">1</a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">2</a></li><li class="page-item "><a class="page-link" href="https://bottlesofrum.com/cannons/3">3</a></li><li class="page-item"><a class="page-link"  href ="https://bottlesofrum.com/cannons/3"><i class="fa fa-forward"></i></i></a></li><li class="page-item"><a class="page-link"  href ="https://bottlesofrum.com/cannons/5"><i class="fa fa-fast-forward"></i></i></a></li></ul></nav>', $pager->render());
    }

    /**
     * @throws Exception
     */
    public function testCreatePagerThrowsExceptionWithoutPageTotal()
    {
        $this->expectException(PaginatorException::class);
        $pager = new Paginator();
        $pager->render();
    }

    /**
     * @throws Exception
     */
    public function testCreatePagerThrowsExceptionWithoutUrl()
    {
        $this->expectException(PaginatorException::class);
        $pager = new Paginator();
        $pager->setPageCountByTotalRecords(100, 20);
        $pager->render();
    }


    /**
     * @throws Exception
     */
    public function testPagerSize()
    {
        $pager = new Paginator();
        $this->assertEquals(5, $pager->getPagerSize());
        $pager->setPagerSize(7);
        $this->assertEquals(7, $pager->getPagerSize());
        $pager->setPagerSize(10);
        $this->assertEquals(9, $pager->getPagerSize());
        $pager->setPagerSize(9);
        $this->assertEquals(9, $pager->getPagerSize());
        $pager->setPagerSize(11);
        $this->assertEquals(11, $pager->getPagerSize());
        $pager->setPagerSize(12);
        $this->assertEquals(11, $pager->getPagerSize());
    }
}
