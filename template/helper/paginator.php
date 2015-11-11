<?php
/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2011 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/nooku/nooku-files for the canonical source repository
 */

/**
 * Paginator Template Helper
 *
 * @author  Ercan Ozkaya <https://github.com/ercanozkaya>
 * @package Koowa\Component\Files
 */
class ComFilesTemplateHelperPaginator extends KTemplateHelperPaginator
{
    /**
     * Render item pagination
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     * @see     http://developer.yahoo.com/ypatterns/navigation/pagination/
     */
    public function pagination($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'limit'   => 0,
        ));

        $translator = $this->getObject('translator');

        $html  = '<div id="files-paginator-container">';

        $html .= '<div class="k-pagination" id="files-paginator">';

        $html .= '<div class="limit">'.$this->limit($config->toArray()).'</div>';

        $html .= '<span class="start hidden"><a></a></span>';
        $html .= '<ul class="pagination">';
        $html .=  $this->_pages(array());
        $html .= '</ul>';
        $html .= '<span class="end hidden"><a></a></span>';

        $html .= '<div class="limit k-pagination-pages"> ';
        $html .= sprintf($translator->translate('JLIB_HTML_PAGE_CURRENT_OF_TOTAL'), '<strong class="page-current">1</strong>', '<strong class="page-total">1</strong></div>');
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render a list of pages links
     *
     * This function is overrides the default behavior to render the links in the khepri template
     * backend style.
     *
     * @param   array   $pages An array of page data
     * @return  string  Html
     */
    protected function _pages($pages)
    {
        $tpl = '<li class="%s"><a href="#">%s</a></li>';

        $html  = sprintf($tpl, 'prev', '&laquo;');
        $html .= '<li class="page"></li>';
        $html .= sprintf($tpl, 'next', '&raquo;');

        return $html;
    }
}
