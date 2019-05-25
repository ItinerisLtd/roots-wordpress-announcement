<?php

namespace Itineris\RootsWordPressAnnouncement;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    /** @var Composer */
    protected $composer;
    /** @var IOInterface */
    protected $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ScriptEvents::POST_INSTALL_CMD => array(
                'announce',
            ),
            ScriptEvents::POST_UPDATE_CMD => array(
                'announce',
            ),
        );
    }

    public function announce()
    {
        if (PHP_VERSION_ID >= 50620) {
            return;
        }

        $installedRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        $brokenPackage = $installedRepo->findPackage(
            'roots/wordpress',
            '5.2 || dev-5.2-branch || 5.2.1 || dev-5.2.1-branch'
        );
        if (null === $brokenPackage) {
            return;
        }

        $message = 'Your PHP version is not compatible with WordPress v5.2 or later.';
        $message .= ' Composer version constraints failed to catch this because of a mistake in roots/wordpress.';
        $message .= ' See: https://github.com/roots/wordpress/pull/9';

        $this->io->writeError("<error>${message}</error>");
    }
}
