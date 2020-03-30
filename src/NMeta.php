<?php

namespace NMeta;

/**
 * Class NMeta
 *
 * @package NMeta
 * @author  Casper Rasmussen <cr@nodes.dk>
 */
class NMeta
{
    /**
     * @var string
     */
    protected $platform;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Version number.
     *
     * @var string
     */
    protected $version = 1;

    /**
     * Major version number.
     *
     * @var int
     */
    protected $majorVersion = 0;

    /**
     * Minor version number.
     *
     * @var int
     */
    protected $minorVersion = 0;

    /**
     * Patch version number.
     *
     * @var int
     */
    protected $patchVersion = 0;

    /**
     * @var string|null
     */
    protected $deviceOsVersion;

    /**
     * @var string
     */
    protected $device;

    /**
     * platforms.
     *
     * @var array
     */
    protected $platforms;

    /**
     * environments.
     *
     * @var array
     */
    protected $environments;

    /**
     * NMeta constructor.
     *
     * @param string|null        $header
     * @param \NMeta\Config|null $config
     * @throws \NMeta\BadRequestException
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function __construct(?string $header = null, Config $config = null)
    {
        if (empty($header)) {
            throw new BadRequestException($config->getHeader() . ' header: header is missing');
        }

        if (!$config) {
            $config = Config::createDefault();
        }

        $this->platforms = $config->getPlatforms();
        $this->environments = $config->getEnvironments();

        $format = 'platform;environment;version;os-version;device'; // ios;local;1.0.0;10.1;iphone-x

        $headerArr = explode(';', $header);

        // Parse platform
        if (!isset($headerArr[0]) || !in_array($headerArr[0], $this->platforms)) {
            throw new BadRequestException($config->getHeader() . ' header: Platform is not supported, should be: ' .
                                          implode(',', $this->platforms) .
                                          ' -  format:' . $format);
        }

        $this->platform = $headerArr[0];

        // Parse env
        if (!isset($headerArr[1]) || !in_array($headerArr[1], $this->environments)) {
            throw new BadRequestException($config->getHeader() . ' header: Environment is not supported, should be: ' .
                                          implode(',', $this->environments) .
                                          ' -  format:' . $format);
        }

        $this->environment = $headerArr[1];

        // Web does not have further requirements, since they have a normal User-Agent header
        if ($this->platform == 'web') {
            return;
        }

        // Parse Build number
        if (!isset($headerArr[2])) {
            throw new BadRequestException('Meta header: Missing version' . ' -  format:' . $format);
        }

        $this->version = $headerArr[2];
        $versionArr = explode('.', $this->version);
        $this->majorVersion = isset($versionArr[0]) ? $versionArr[0] : 0;
        $this->minorVersion = isset($versionArr[1]) ? $versionArr[1] : 0;
        $this->patchVersion = isset($versionArr[2]) ? $versionArr[2] : 0;

        // Parse device os version
        if (!isset($headerArr[3])) {
            throw new BadRequestException('Meta header: Missing device os version' .
                                          ' -  format:' . $format);
        }

        $this->deviceOsVersion = $headerArr[3];

        // Parse device
        if (!isset($headerArr[4])) {
            throw new BadRequestException('Meta header: Missing device' . ' -  format:' . $format);
        }

        $this->device = $headerArr[4];
    }

    /**
     * Retrieve platform.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Retrieve environment.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Retrieve version.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Retrieve majorVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getMajorVersion()
    {
        return $this->majorVersion;
    }

    /**
     * Retrieve minorVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getMinorVersion()
    {
        return $this->minorVersion;
    }

    /**
     * Retrieve patchVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getPatchVersion()
    {
        return $this->patchVersion;
    }

    /**
     * Retrieve deviceOsVersion.
     *
     * @return null|string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getDeviceOsVersion()
    {
        return $this->deviceOsVersion;
    }

    /**
     * Retrieve device.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getDevice()
    {
        return $this->device;
    }

    /**
     * toArray.
     *
     * @return array
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function toArray()
    {
        return [
            'platform'        => $this->platform,
            'environment'     => $this->environment,
            'version'         => $this->version,
            'majorVersion'    => $this->majorVersion,
            'minorVersion'    => $this->minorVersion,
            'patchVersion'    => $this->patchVersion,
            'deviceOsVersion' => $this->deviceOsVersion,
            'device'          => $this->device,
        ];
    }

    /**
     * Pass object back to header format platform:environment;version;os-version;device
     * example: ios;local;1.0.0;10.1;iphone-x
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     */
    public function toHeaderString(): string
    {
        switch ($this->platform) {
            case 'web':
                return sprintf('%s;%s;', $this->platform, $this->environment);
            default:
                return sprintf('%s;%s;%s;%s;%s', $this->platform,
                    $this->environment, $this->version,
                    $this->deviceOsVersion, $this->device);
        }
    }
}
