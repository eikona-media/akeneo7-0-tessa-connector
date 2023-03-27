<?php
/**
 * TessaController.php
 *
 * @author    Timo Müller <t.mueller@eikona-media.de>
 * @copyright 2018 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Controller;

use Eikona\Tessa\ConnectorBundle\Tessa;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TessaController
{
    /** @var Tessa */
    protected $tessa;

    public function __construct(
        Tessa $tessa
    )
    {
        $this->tessa = $tessa;
    }

    /**
     * @return RedirectResponse
     */
    public function gotoTessaAction()
    {
        $url = $this->tessa->getUiUrl();

        if (!$url) {
            throw new NotFoundHttpException('Tessa url not found');
        }

        return new RedirectResponse($url);
    }
}
