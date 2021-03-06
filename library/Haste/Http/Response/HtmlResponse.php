<?php

/**
 * Haste utilities for Contao Open Source CMS
 *
 * Copyright (C) 2012-2013 Codefog & terminal42 gmbh
 *
 * @package    Haste
 * @link       http://github.com/codefog/contao-haste/
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

namespace Haste\Http\Response;


class HtmlResponse extends Response
{
    /**
     * Creates a new HTML HTTP response
     * @param   string The response content
     * @param   integer The response HTTP status code
     * @throws  \InvalidArgumentException When the HTTP status code is not valid
     */
    public function __construct($strContent = '', $intStatus = 200)
    {
        parent::__construct($strContent, $intStatus);

        $this->setHeader('Content-Type', 'text/html');
    }
}
