<?php
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

class wwd_page_foot
{
    private $curPage = 1;
    private $Pages = 1;
    private $urlBase = '';
    private $url = '';

    public function __construct($currentPage, $pageCount, $urlBase)
    {
        $this->curPage = $currentPage;
        $this->Pages = $pageCount;
        $this->urlBase = $urlBase;
    }

    private function first()
    {
        $result = '';
        return $result;
    }

    private function buildURL($page)
    {
        $result = '<a href="' . $this->urlBase . $page . '">';
        return $result;
    }

    public function render()
    {
        $x = '';
        $sep = '</a>&nbsp;';
        $prevPage = $this->curPage - 1;
        if ($prevPage < 1) {
            $prevPage = 1;
        }

        // first page
        $x .= $this->buildURL(1) . '<<' . $sep;
        // previous page
        $x .= $this->buildURL($prevPage) . '<' . $sep;
        // current - 2 page
        // current - 1 page
        // current
        // current + 1 page
        // current + 2 page
        // next page
        // last page
        $x .= $this->buildURL($this->Pages) . '>>' . $sep;

        return $x;
    }

}