<?php

use Bone\View\Helper\Exception\PaginatorException;
use Bone\View\Helper\Paginator;
use Codeception\Test\Unit;

class PaginatorTest extends Unit
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


    /**
     * @throws Exception
     */
    public function testRender()
    {
        $pager = new Paginator();
        $pager->setUrl('https://awesome.scot/rendertest/:page');
        $pager->setPagerSize(3);
        $pager->setCurrentPage(3);
        $pager->setPageCountByTotalRecords(30, 5);
        $html = $pager->render();

        $this->assertEquals('<nav><ul class="pagination"><li class="page-item"><a class="page-link"  href ="https://awesome.scot/rendertest/1"><i class="fa fa-fast-backward"></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/rendertest/2"><i class="fa fa-backward"></i></a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/rendertest/2">2</a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">3</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/rendertest/4">4</a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/rendertest/4"><i class="fa fa-forward"></i></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/rendertest/6"><i class="fa fa-fast-forward"></i></i></a></li></ul></nav>', $html);
    }


    /**
     * @throws Exception
     */
    public function testRenderLastPage()
    {
        $pager = new Paginator();
        $pager->setUrl('https://awesome.scot/renderagain/:page');
        $pager->setPagerSize(3);
        $pager->setCurrentPage(6);
        $pager->setPageCountByTotalRecords(30, 5);
        $html = $pager->render();

        $this->assertEquals('<nav><ul class="pagination"><li class="page-item"><a class="page-link"  href ="https://awesome.scot/renderagain/1"><i class="fa fa-fast-backward"></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/renderagain/5"><i class="fa fa-backward"></i></a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/renderagain/4">4</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/renderagain/5">5</a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">6</a></li><li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-forward disabled"></i></a></li><li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-fast-forward disabled"></i></a></li></ul></nav>', $html);
    }

    /**
     * @throws Exception
     */
    public function testRenderFirstPage()
    {
        $pager = new Paginator();
        $pager->setUrl('https://awesome.scot/first/:page');
        $pager->setPagerSize(3);
        $pager->setCurrentPage(1);
        $pager->setPageCountByTotalRecords(30, 5);
        $html = $pager->render();

        $this->assertEquals('<nav><ul class="pagination"><li class="page-item disabled"><a class="page-link"  href ="#"><i class="fa fa-fast-backward disabled"></i></a></li><li class="page-item disabled"><a class="page-link"  href ="#"><i class="fa fa-backward disabled"></i></a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">1</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/first/2">2</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/first/3">3</a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/first/2"><i class="fa fa-forward"></i></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/first/6"><i class="fa fa-fast-forward"></i></i></a></li></ul></nav>', $html);
    }

    /**
     * @throws Exception
     */
    public function testRenderLowNumberOfPages()
    {
        $pager = new Paginator();
        $pager->setUrl('https://awesome.scot/low/:page');
        $pager->setPagerSize(3);
        $pager->setCurrentPage(1);
        $pager->setPageCountByTotalRecords(4, 5);
        $html = $pager->render();

        $this->assertEquals('<nav><ul class="pagination"><li class="page-item disabled"><a class="page-link"  href ="#"><i class="fa fa-fast-backward disabled"></i></a></li><li class="page-item disabled"><a class="page-link"  href ="#"><i class="fa fa-backward disabled"></i></a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">1</a></li><li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-forward disabled"></i></a></li><li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-fast-forward disabled"></i></a></li></ul></nav>', $html);
    }

    /**
     * @throws Exception
     */
    public function testCalculateStartsAtPageOne()
    {
        $pager = new Paginator();
        $pager->setUrl('https://awesome.scot/calculate/:page');
        $pager->setPagerSize(10);
        $pager->setCurrentPage(4);
        $pager->setPageCount(15);
        $html = $pager->render();

        $this->assertEquals('<nav><ul class="pagination"><li class="page-item"><a class="page-link"  href ="https://awesome.scot/calculate/1"><i class="fa fa-fast-backward"></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/calculate/3"><i class="fa fa-backward"></i></a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/1">1</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/2">2</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/3">3</a></li><li class="page-item  active" aria-current="page"><a class="page-link" href="#">4</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/5">5</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/6">6</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/7">7</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/8">8</a></li><li class="page-item "><a class="page-link" href="https://awesome.scot/calculate/9">9</a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/calculate/5"><i class="fa fa-forward"></i></i></a></li><li class="page-item"><a class="page-link"  href ="https://awesome.scot/calculate/15"><i class="fa fa-fast-forward"></i></i></a></li></ul></nav>', $html);
    }
}
