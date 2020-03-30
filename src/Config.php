<?php

namespace NMeta;

/**
 * Class Config
 *
 * @package NMeta
 * @author  Casper Rasmussen <cr@nodes.dk>
 */
class Config
{
    /** @var string */
    protected $header;

    /** @var array */
    protected $platforms;

    /** @var array */
    protected $environments;

    public function __construct(array $data)
    {
        $this->header = (string)$data['header'];
        $this->platforms = (array)$data['platforms'];
        $this->environments = (array)$data['environments'];
    }

    /**
     * @return string
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getHeader(): string
    {
        return $this->header;
    }

    /**
     * @return array
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * @return array
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function getEnvironments(): array
    {
        return $this->environments;
    }

    /**
     * toArray
     *
     * @return array
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function toArray(): array
    {
        return [
            'header'       => $this->header,
            'platforms'    => $this->platforms,
            'environments' => $this->environments,
        ];
    }

    /**
     * createDefault
     *
     * @return \NMeta\Config
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public static function createDefault(): self
    {
        return new self([
            'header'       => 'Client-Meta-Information',
            'platforms'    => [
                'android',
                'ios',
                'web',
            ],
            'environments' => [
                'local',
                'development',
                'staging',
                'production',
            ],
        ]);
    }
}