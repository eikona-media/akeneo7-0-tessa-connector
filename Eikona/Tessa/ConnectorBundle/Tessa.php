<?php

namespace Eikona\Tessa\ConnectorBundle;

use Exception;
use Monolog\Logger;
use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class Tessa
{
    /** @var string */
    protected $baseUrl;

    /** @var string */
    protected $uiUrl;

    /** @var bool */
    protected $useHttpInternally = false;

    /** @var string */
    protected $username;

    /** @var string */
    protected $accessToken;

    /** @var Kernel */
    protected $kernel;

    /** @var Logger */
    protected $logger;

    /** @var string */
    protected $systemIdentifier;

    /** @var int */
    protected $userId;

    /** @var bool */
    protected $isAssetEditingInAkeneoUiDisabled;

    /** @var bool */
    protected $isReferenceEntityTessaMainImageEnabled;

    /**
     * Tessa constructor.
     *
     * @param ConfigManager $oroGlobal
     * @param Kernel|KernelInterface $kernel
     * @param Logger $logger
     */
    public function __construct(
        ConfigManager $oroGlobal,
        KernelInterface $kernel,
        Logger $logger,
    )
    {
        try {
            $this->baseUrl = trim($oroGlobal->get('pim_eikona_tessa_connector.base_url'), ' /');
            $this->uiUrl = trim($oroGlobal->get('pim_eikona_tessa_connector.ui_url'), ' /');
            $this->useHttpInternally = (bool)$oroGlobal->get('pim_eikona_tessa_connector.use_http_internally');
            $this->username = trim($oroGlobal->get('pim_eikona_tessa_connector.username'));
            $this->accessToken = trim($oroGlobal->get('pim_eikona_tessa_connector.api_key'));
            $this->userId = (int)substr($this->accessToken, 0, strpos($this->accessToken, ':'));
            $this->systemIdentifier = trim($oroGlobal->get('pim_eikona_tessa_connector.system_identifier'));
            $this->isAssetEditingInAkeneoUiDisabled = (bool)$oroGlobal->get('pim_eikona_tessa_connector.disable_asset_editing_in_akeneo_ui');
            $this->isReferenceEntityTessaMainImageEnabled = (bool)$oroGlobal->get('pim_eikona_tessa_connector.enable_reference_entity_tessa_main_image');
        } catch(Exception $e) {
            // This exception happens when the database is missing (first installation, so nothing to concern about)
            $this->baseUrl = '';
            $this->uiUrl = '';
            $this->useHttpInternally = false;
            $this->username = '';
            $this->accessToken = '';
            $this->userId = 0;
            $this->systemIdentifier = '';
            $this->isAssetEditingInAkeneoUiDisabled = false;
            $this->isReferenceEntityTessaMainImageEnabled = false;
        }
        $this->kernel = $kernel;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getUiUrl()
    {
        return $this->uiUrl;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getSystemIdentifier()
    {
        return $this->systemIdentifier;
    }

    /**
     * @return bool
     */
    public function isAssetEditingInAkeneoUiDisabled()
    {
        return $this->isAssetEditingInAkeneoUiDisabled;
    }

    /**
     * @return bool
     */
    public function isReferenceEntityTessaMainImageEnabled(): bool
    {
        return $this->isReferenceEntityTessaMainImageEnabled;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool
    {
        $baseUrl = $this->getUrlForInternalCommunication();
        if (!$baseUrl) {
            return false;
        }

        $ch = curl_init($baseUrl);

        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpcode < 400;
    }

    public function getAsset(string $assetId)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->baseUrl . '/api/media/asset/' . $assetId,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'X-Auth-Token: ' . $this->accessToken,
                'Content-Type: application/x-www-form-urlencoded',
            ],
        ]);
        $response = curl_exec($curl);
        $responseFormatted = json_decode($response);
        curl_close($curl);

        return $responseFormatted;
    }

    /**
     * Returns the tessa url for internal communication
     * Depends on useHttpInternally if http or https is used
     *
     * @return string
     */
    private function getUrlForInternalCommunication(): string
    {
        $url = $this->baseUrl;
        if (!is_string($url) || !$this->useHttpInternally) {
            return $url;
        }

        return preg_replace('/^https/', 'http', $url);
    }
}
