<?php

namespace limepie;

/*

$totalCount     = 1232;
$recordsPerPage = 50;
$pagesPerBlock  = 9;
$currentPage    = sanitize\segment::int(2,1);;
$urlPattern     = '/service/list/(:page)';


// html
$pagingHtml  = paginator::getHtml($totalCount, $currentPage, $recordsPerPage, $pagesPerBlock, $urlPattern);

view::assign([
    'pagingHtml'  => $pagingHtml,
]);
*/

/*
// array, define
$paging = paginator::get($totalCount, $currentPage, $recordsPerPage, $pagesPerBlock, $urlPattern, true);

view::assign([
    'paging'  => $paging,
]);

view::define([
    'pagination' => 'theme/service/normal/pagination.phtml',
]);
*/


// theme/service/normal/pagination.phtml
/*

<nav style='text-align:center'>

    <ul class="pagination">
        {{?paginator.prevUrl}}
            <li><a href="{{=paginator.prevUrl}}">&laquo;</a></li>
        {{:}}
            <li class='disabled'><a>&laquo;</a></li>
        {{/}}

        {{@page =  paginator.pages}}
            {{?page.url}}
                <li {{?page.isCurrent}}class="active"{{/}}>
                    <a href="{{=page.url}}">{{=page.num}}</a>
                </li>
            {{:}}
                <li class="disabled"><span>{{=page.num}}</span></li>
            {{/}}
        {{/}}

        {{?paginator.nextUrl}}
            <li><a href="{{=paginator.nextUrl}}">&raquo;</a></li>
        {{:}}
            <li class='disabled'><a>&raquo;</a></li>
        {{/}}
    </ul>

</nav>

*/

class paginator
{

    private $totalCount       = 0;
    private $totalPages       = 0;
    private $currentPage      = 0;
    private $recordsPerPage   = 10;
    private $pagesPerBlock    = 9;
    private $viewStartEnd     = FALSE;
    private $urlPattern;

    public function __construct($totalCount, $currentPage, $recordsPerPage=10, $pagesPerBlock=9, $urlPattern = '', $viewStartEnd = FALSE)
    {

        $this->totalCount     = $totalCount;
        $this->recordsPerPage = $recordsPerPage;
        $this->currentPage    = $currentPage;
        $this->urlPattern     = $urlPattern;
        $this->pagesPerBlock  = $pagesPerBlock;
        $this->viewStartEnd   = $viewStartEnd;

        $this->totalPages     = ($this->recordsPerPage == 0 ? 0 : (int) ceil($this->totalCount/$this->recordsPerPage));
        $this->nextPage       = $this->currentPage < $this->totalPages ? $this->currentPage + 1 : NULL;
        $this->prevPage       = $this->currentPage > 1 ? $this->currentPage - 1 : NULL;

    }

    public static function getHtml($totalCount, $currentPage, $recordsPerPage=10, $pagesPerBlock=9, $urlPattern = '', $viewStartEnd = FALSE)
    {

        $paginator = new paginator($totalCount, $currentPage, $recordsPerPage, $pagesPerBlock=9, $urlPattern, $viewStartEnd);
        return $paginator->toHtml();

    }

    public static function get($totalCount, $currentPage, $recordsPerPage=10, $pagesPerBlock=9, $urlPattern = '', $viewStartEnd = FALSE)
    {

        $paginator = new paginator($totalCount, $currentPage, $recordsPerPage, $pagesPerBlock=9, $urlPattern, $viewStartEnd);
        return $paginator->toArray();

    }

    private function getPageUrl($pageNum=NULL)
    {

        if(!$pageNum)
        {
            return NULL;
        }
        return str_replace('(:page)', $pageNum, $this->urlPattern);

    }

    private function createPage($pageNum, $isCurrent = FALSE)
    {

        return [
            'num'       => $pageNum,
            'url'       => $this->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        ];

    }

    private function createEllipsisPage()
    {

        return [
            'num'       => '...',
            'url'       => NULL,
            'isCurrent' => FALSE,
        ];

    }

    public function getPages()
    {

        $pages = [];

        if ($this->totalPages <= $this->pagesPerBlock)
        {
            for ($i = 1; $i <= $this->totalPages; $i++)
            {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
        }
        else
        {
            if(TRUE === $this->viewStartEnd)
            {
                $pagesPerBlock = $this->pagesPerBlock - 2;
            }
            else
            {
                $pagesPerBlock = $this->pagesPerBlock;
            }
            $numAdjacents = (int)floor(($pagesPerBlock - 1) / 2);

            if ($this->currentPage + $numAdjacents > $this->totalPages)
            {
                $startPage = $this->totalPages - $pagesPerBlock+1;// + 2;
            }
            else
            {
                $startPage = $this->currentPage - $numAdjacents;
            }
            if ($startPage < 1)
            {
                $startPage = 1;
            }
            $endPage = $startPage + $pagesPerBlock - 1;
            if ($endPage >= $this->totalPages)
            {
                $endPage = $this->totalPages;
            }

            if(TRUE === $this->viewStartEnd && $startPage >2)
            {
                $pages[] = $this->createPage(1, $this->currentPage == 1);
                $pages[] = $this->createEllipsisPage();
            }
            for ($i = $startPage; $i <= $endPage; $i++)
            {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
            if(TRUE === $this->viewStartEnd && $endPage < $this->totalPages -1)
            {
                $pages[] = $this->createEllipsisPage();
                $pages[] = $this->createPage($this->totalPages, $this->currentPage == $this->totalPages);
            }
        }

        return $pages;

    }

    public function toArray()
    {

        return [
            'totalPages'  => $this->totalPages,
            'currentPage' => $this->currentPage,
            'prevUrl'     => $this->getPageUrl($this->prevPage),
            'pages'       => $this->getPages(),
            'nextUrl'     => $this->getPageUrl($this->nextPage),
        ];

    }

    public function toHtml()
    {

        $paginator = $this->toArray();

        $html = '<ul class="pagination">';
        if ($paginator['prevUrl'])
        {
            $html .= '<li><a href="' . $paginator['prevUrl'] . '">&laquo;</a></li>';//Previous
        }
        else
        {
            $html .= '<li class="disabled"><a>&laquo;</a></li>';
        }
        foreach ($paginator['pages'] as $page)
        {
            if ($page['url'])
            {
                $html .= '<li' . ($page['isCurrent'] ? ' class="active"' : '') . '><a href="' . $page['url'] . '">' . $page['num'] . '</a></li>';
            }
            else
            {
                $html .= '<li class="disabled"><span>' . $page['num'] . '</span></li>';
            }
        }
        if ($paginator['nextUrl'])
        {
            $html .= '<li><a href="' . $paginator['nextUrl'] . '">&raquo;</a></li>';//Next
        }
        else
        {
            $html .= '<li class="disabled"><a>&raquo;</a></li>';
        }
        $html .= '</ul>';

        return $html;

    }

}
