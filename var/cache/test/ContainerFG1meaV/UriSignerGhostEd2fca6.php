<?php

namespace ContainerFG1meaV;
include_once \dirname(__DIR__, 4).'/vendor/symfony/http-foundation/UriSigner.php';

class UriSignerGhostEd2fca6 extends \Symfony\Component\HttpFoundation\UriSigner implements \Symfony\Component\VarExporter\LazyObjectInterface
{
    use \Symfony\Component\VarExporter\LazyGhostTrait;

    private const LAZY_OBJECT_PROPERTY_SCOPES = [
        "\0".parent::class."\0".'clock' => [parent::class, 'clock', null, 16],
        "\0".parent::class."\0".'expirationParameter' => [parent::class, 'expirationParameter', null, 16],
        "\0".parent::class."\0".'hashParameter' => [parent::class, 'hashParameter', null, 16],
        "\0".parent::class."\0".'secret' => [parent::class, 'secret', null, 16],
        'clock' => [parent::class, 'clock', null, 16],
        'expirationParameter' => [parent::class, 'expirationParameter', null, 16],
        'hashParameter' => [parent::class, 'hashParameter', null, 16],
        'secret' => [parent::class, 'secret', null, 16],
    ];
}

// Help opcache.preload discover always-needed symbols
class_exists(\Symfony\Component\VarExporter\Internal\Hydrator::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectRegistry::class);
class_exists(\Symfony\Component\VarExporter\Internal\LazyObjectState::class);

if (!\class_exists('UriSignerGhostEd2fca6', false)) {
    \class_alias(__NAMESPACE__.'\\UriSignerGhostEd2fca6', 'UriSignerGhostEd2fca6', false);
}
