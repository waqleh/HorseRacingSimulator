<?php
/**
 * @author: Walid Aqleh <waleedakleh23@hotmail.com>
 */

defined('BASEURL') OR exit('No direct script access allowed');

/**
 * Class base_controller
 */
class base_controller
{
    /**
     * load view file
     * @param string $contentView the page view
     * @param mixed $data the data the view will show
     */
    public function loadView($contentView, $data = null)
    {
        include(VIEWPATH . 'theme.php');
    }

}