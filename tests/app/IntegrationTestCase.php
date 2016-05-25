<?php

namespace App\Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class IntegrationTestCase extends TestCase
{
    /**
     * Web Driver instance
     *
     * @var RemoteWebDriver
     */
    protected $webDriver;

    protected $session;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        $this->webDriver = $this->createWebDriver();

        $this->beforeApplicationDestroyed(function () {
            $this->webDriver->quit();
        });

        return $app;
    }

    /**
     * Create web driver
     *
     * @return RemoteWebDriver
     */
    protected function createWebDriver()
    {
        $webDriver = RemoteWebDriver::create(
            $this->getDriverHost()
        );

        return $webDriver;
    }

    private function getDriverHost()
    {
        $configs = $this->getIntegrationConfig();

        if (!in_array($configs['host'], ['browserstack', 'saucelabs', 'localhost:4444'])) {
            $configs['host'] = 'localhost:4444';
        }

        if ($configs['host'] !== 'localhost:4444') {
            $key = strtoupper($configs['host']);

            return sprintf(
                'https://%s:%s@hub.%s.com/wd/hub'
                env($key.'_USER'),
                env($key.'_KEY'),
                $configs['host']
            );
        }

        return 'http://localhost:4444/wd/hub';
    }

    protected function getIntegrationConfig()
    {
        return [];
    }

    protected function visit($path)
    {
        $this->currentUri = $this->prepareUrlForRequest($path);

        $this->webDriver->get($this->currentUri);

        return $this;
    }

    protected function closeBrowser()
    {
        if ($this->webDriver) {
            $this->webDriver->close();
        }
    }
}
