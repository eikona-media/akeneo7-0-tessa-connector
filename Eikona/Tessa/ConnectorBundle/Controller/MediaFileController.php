<?php
/**
 * MediaFileController.php
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2016 Eikona AG (http://www.eikona.de)
 */

namespace Eikona\Tessa\ConnectorBundle\Controller;

use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductModelRepositoryInterface;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ProductRepositoryInterface;
use Akeneo\UserManagement\Component\Model\User;
use Eikona\Tessa\ConnectorBundle\Security\AuthGuard;
use Eikona\Tessa\ConnectorBundle\Tessa;
use Eikona\Tessa\ConnectorBundle\Utilities\IdPrefixer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MediaFileController extends AbstractController
{
    const SSO_ACTION_ASSET_DETAIL = 'detail';
    const SSO_ACTION_ASSET_SELECT = 'select';

    /** @var Tessa */
    protected $tessa;
    /** @var AuthGuard */
    protected $authGuard;
    /** @var ProductRepositoryInterface */
    protected $productRepository;
    /** @var ProductModelRepositoryInterface */
    protected $productModelRepository;
    /** @var IdPrefixer */
    protected $idPrefixer;
    /** @var TokenStorageInterface */
    protected $tokenStorage;

    public function __construct(
        Tessa $tessa,
        AuthGuard $authGuard,
        ProductRepositoryInterface $productRepository,
        ProductModelRepositoryInterface $productModelRepository,
        IdPrefixer $idPrefixer,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->tessa = $tessa;
        $this->authGuard = $authGuard;
        $this->productRepository = $productRepository;
        $this->productModelRepository = $productModelRepository;
        $this->idPrefixer = $idPrefixer;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $assetId
     * @return Response
     */
    public function previewAction($assetId)
    {
        $downloadToken = $this->authGuard->getDownloadAuthToken($assetId, 'bild');
        $url = $this->tessa->getBaseUrl()
            . '/ui/bild.php'
            . '?asset_system_id=' . $assetId
            . '&type=preview'
            . '&key=' . $downloadToken;

        return new RedirectResponse($url, 301);
    }

    /**
     * @param $assetId
     * @return Response
     */
    public function detailAction($assetId)
    {
        return $this->gotoTessaWithAuthentication(
            self::SSO_ACTION_ASSET_DETAIL,
            ['id' => $assetId]
        );
    }

    /**
     * @param $data
     * @return Response
     */
    public function selectAction($data)
    {
        // $data wird vom Frontend als Query-Parameter formatiert -> in Array umwandeln
        parse_str(urldecode($data), $dataDecoded);

        return $this->gotoTessaWithAuthentication(
            self::SSO_ACTION_ASSET_SELECT,
            $dataDecoded
        );
    }

    /**
     * @param string $action
     * @param array $payload
     * @return Response
     */
    protected function gotoTessaWithAuthentication(string $action = self::SSO_ACTION_ASSET_SELECT, $payload = [])
    {
        if (!$this->tessa->isAvailable()) {
            return $this->render('@EikonaTessaConnector/Tessa/404.html.twig');
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $url = $this->tessa->getBaseUrl() . '/ui/login.php';
        $auth = $this->authGuard->getHmac('GET', $url, time());

        $queryParams = [
            'auth' => $auth,
            'system' => 'akeneo',
            'action' => $action,
            'data' => $payload,
            'user' => [
                'username' => $user->getUsername(),
                'firstname' => $user->getFirstName(),
                'lastname' => $user->getLastName(),
                'email' => $user->getEmail(),
                'locale' => $user->getUiLocale()->getCode(),
            ]
        ];

        $urlWithParams = $url . '?' . http_build_query($queryParams);
        return new RedirectResponse($urlWithParams, 301);
    }
}
