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
     * Configuration object
     *
     * @var Config
     */
    protected Config $config;

    /**
     * Header format reference
     *
     * @var string
     */
    protected string $format = 'platform;environment;version;os-version;device'; // ios;local;1.0.0;10.1;iphone-x

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
        if (!$config) {
            $this->config = Config::createDefault();
        } else {
            $this->config = $config;
        }

        if (empty($header)) {
            throw new BadRequestException($this->config->getHeader() . ' header is missing');
        }

        $this->platforms = $this->config->getPlatforms();
        $this->environments = $this->config->getEnvironments();

        $headerArr = explode(';', $header);

        $this->parsePlatform($headerArr[0] ?? null);
        $this->parseEnvironment($headerArr[1] ?? null);

        // Web does not have further requirements, since they have a normal User-Agent header
        if ($this->platform == 'web') {
            $this->version = sprintf(
                '%d.%d.%d',
                $this->getMajorVersion(),
                $this->getMinorVersion(),
                $this->getPatchVersion()
            );

            return;
        }

        $this->parseBuildVersion($headerArr[2] ?? null);
        $this->parseDeviceOsVersion($headerArr[3] ?? null);
        $this->parseDevice($headerArr[4] ?? null);
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

    /**
     * parsePlatform
     *
     * @param string|null $platform
     * @throws BadRequestException
     */
    private function parsePlatform(?string $platform): void
    {
        if (!isset($platform) || !in_array($platform, $this->platforms)) {
            $message = sprintf(
                '%s header: Platform is not supported, should be: %s - format: %s',
                $this->config->getHeader(),
                implode(',', $this->platforms),
                $this->format
            );

            throw new BadRequestException($message);
        }

        $this->platform = $platform;
    }

    /**
     * parseEnvironment
     *
     * @param string|null $environment
     * @throws BadRequestException
     */
    private function parseEnvironment(?string $environment): void
    {
        if (!isset($environment) || !in_array($environment, $this->environments)) {
            $message = sprintf(
                '%s header: Environment is not supported, should be: %s - format: %s',
                $this->config->getHeader(),
                implode(',', $this->environments),
                $this->format
            );

            throw new BadRequestException($message);
        }

        $this->environment = $environment;
    }

    /**
     * parseBuildVersion
     *
     * @param string|null $version
     * @throws BadRequestException
     */
    private function parseBuildVersion(?string $version): void
    {
        if (!isset($version)) {
            $message = sprintf(
                '%s header: Missing version - format: %s',
                $this->config->getHeader(),
                $this->format
            );

            throw new BadRequestException($message);
        }

        $this->version = $version;
        $versionArr = explode('.', $this->version);

        if (count($versionArr) != 3) {
            $message = sprintf(
                '%s header: Invalid app version, invalid amount of segments. Expected semver [x.y.z]',
                $this->config->getHeader()
            );

            throw new BadRequestException($message);
        }

        if (!is_numeric($versionArr[0])) {
            $message = sprintf(
                '%s header: Invalid Major version, expected integer',
                $this->config->getHeader()
            );

            throw new BadRequestException($message);
        }

        $this->majorVersion = $versionArr[0] ?? 0;

        if (!is_numeric($versionArr[1])) {
            $message = sprintf(
                '%s header: Invalid Minor version, expected integer',
                $this->config->getHeader()
            );

            throw new BadRequestException($message);
        }

        $this->minorVersion = $versionArr[1] ?? 0;

        if (!is_numeric($versionArr[2])) {
            $message = sprintf(
                '%s header: Invalid Patch version, expected integer',
                $this->config->getHeader()
            );

            throw new BadRequestException($message);
        }

        $this->patchVersion = $versionArr[2] ?? 0;
    }

    /**
     * parseDeviceOsVersion
     *
     * @param string|null $version
     * @throws BadRequestException
     */
    private function parseDeviceOsVersion(?string $version): void
    {
        if (!isset($version)) {
            $message = sprintf(
                '%s header: Missing device os version - format: %s',
                $this->config->getHeader(),
                $this->format
            );

            throw new BadRequestException($message);
        }

        $this->deviceOsVersion = $version;
    }

    /**
     * parseDevice
     *
     * @param string|null $device
     * @throws BadRequestException
     */
    private function parseDevice(?string $device): void
    {
        if (!isset($device)) {
            $message = sprintf(
                '%s header: Missing device - format: %s',
                $this->config->getHeader(),
                $this->format
            );

            throw new BadRequestException($message);
        }

        $this->device = $device;
    }
}
