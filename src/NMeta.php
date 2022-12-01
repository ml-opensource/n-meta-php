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
    protected string $platform;

    /**
     * @var string
     */
    protected string $environment;

    /**
     * Version number.
     *
     * @var string
     */
    protected string $version;

    /**
     * Major version number.
     *
     * @var int
     */
    protected int $majorVersion = 0;

    /**
     * Minor version number.
     *
     * @var int
     */
    protected int $minorVersion = 0;

    /**
     * Patch version number.
     *
     * @var int
     */
    protected int $patchVersion = 0;

    /**
     * @var string|null
     */
    protected ?string $deviceOsVersion = null;

    /**
     * @var string|null
     */
    protected ?string $device = null;

    /**
     * platforms.
     *
     * @var array
     */
    protected array $platforms;

    /**
     * environments.
     *
     * @var array
     */
    protected array $environments;

    /**
     * NMeta constructor.
     *
     * @param string|null        $header
     * @param Config|null $config
     * @throws BadRequestException
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function __construct(?string $header = null, Config $config = null)
    {
        if (empty($header)) {
            throw new BadRequestException($config->getHeader() . ' header is missing');
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
            $message = sprintf(
                '%s header: Platform is not supported, should be: %s - format: %s',
                $config->getHeader(),
                implode(',', $this->platforms),
                $format
            );

            throw new BadRequestException($message);
        }

        $this->platform = $headerArr[0];

        // Parse env
        if (!isset($headerArr[1]) || !in_array($headerArr[1], $this->environments)) {
            $message = sprintf(
                '%s header: Environment is not supported, should be: %s - format: %s',
                $config->getHeader(),
                implode(',', $this->environments),
                $format
            );

            throw new BadRequestException($message);
        }

        $this->environment = $headerArr[1];

        // Web does not have further requirements, since they have a normal User-Agent header
        if ($this->platform == 'web') {
            $this->version = sprintf(
                '%d.%d.%d',
                $this->majorVersion,
                $this->minorVersion,
                $this->patchVersion
            );
            return;
        }

        // Parse Build number
        if (!isset($headerArr[2])) {
            $message = sprintf(
                'Meta header: Missing version - format: %s',
                $format
            );

            throw new BadRequestException($message);
        }

        $this->version = $headerArr[2];
        $versionArr = explode('.', $this->version);
        $this->majorVersion = $versionArr[0] ?? 0;
        $this->minorVersion = $versionArr[1] ?? 0;
        $this->patchVersion = $versionArr[2] ?? 0;

        // Parse device os version
        if (!isset($headerArr[3])) {
            $message = sprintf(
                'Meta header: Missing device os version - format: %s',
                $format
            );

            throw new BadRequestException($message);
        }

        $this->deviceOsVersion = $headerArr[3];

        // Parse device
        if (!isset($headerArr[4])) {
            $message = sprintf(
                'Meta header: Missing device - format: %s',
                $format
            );

            throw new BadRequestException($message);
        }

        $this->device = $headerArr[4];
    }

    /**
     * Retrieve platform.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * Retrieve environment.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Retrieve version.
     *
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Retrieve majorVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getMajorVersion(): int
    {
        return $this->majorVersion;
    }

    /**
     * Retrieve minorVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getMinorVersion(): int
    {
        return $this->minorVersion;
    }

    /**
     * Retrieve patchVersion.
     *
     * @return int
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getPatchVersion(): int
    {
        return $this->patchVersion;
    }

    /**
     * Retrieve deviceOsVersion.
     *
     * @return string|null
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getDeviceOsVersion(): ?string
    {
        return $this->deviceOsVersion;
    }

    /**
     * Retrieve device.
     *
     * @return string|null
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * toArray.
     *
     * @return array
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function toArray(): array
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
                return sprintf(
                    '%s;%s;',
                    $this->platform,
                    $this->environment
                );
            default:
                return sprintf(
                    '%s;%s;%s;%s;%s',
                    $this->platform,
                    $this->environment,
                    $this->version,
                    $this->deviceOsVersion,
                    $this->device
                );
        }
    }
}
