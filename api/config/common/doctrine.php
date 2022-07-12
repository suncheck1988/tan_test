<?php

declare(strict_types=1);

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

use function App\Application\env;

return [
    EntityManagerInterface::class => static function (ContainerInterface $container): EntityManagerInterface {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *     metadata_dirs:string[],
         *     dev_mode:bool,
         *     proxy_dir:string,
         *     cache_dir:?string,
         *     types:array<string,class-string<Doctrine\DBAL\Types\Type>>,
         *     subscribers:string[],
         *     connection:array<string, mixed>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = ORMSetup::createConfiguration(
            $settings['dev_mode'],
            $settings['proxy_dir'],
            $settings['cache_dir'] ?
                new FilesystemAdapter('', 0, $settings['cache_dir']) :
                new ArrayAdapter()
        );

        $config->setMetadataDriverImpl(new AttributeDriver($settings['metadata_dirs']));

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        /**
         * @psalm-suppress MixedArgument
         */
        foreach ($settings['types'] as $class) {
            if (!DBAL\Types\Type::hasType($class::NAME)) {
                DBAL\Types\Type::addType($class::NAME, $class);
            }
        }

        $eventManager = new EventManager();

        foreach ($settings['subscribers'] as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }

        return EntityManager::create(
            $settings['connection'],
            $config,
            $eventManager
        );
    },
    Connection::class => static function (ContainerInterface $container): Connection {
        $em = $container->get(EntityManagerInterface::class);
        return $em->getConnection();
    },

    'config' => [
        'doctrine' => [
            'dev_mode' => false,
            'cache_dir' => __DIR__ . '/../../var/cache/doctrine/cache',
            'proxy_dir' => __DIR__ . '/../../var/cache/doctrine/proxy',
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => env('DB_HOST'),
                'user' => env('DB_USER'),
                'password' => env('DB_PASSWORD'),
                'dbname' => env('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'subscribers' => [],
            'metadata_dirs' => [
                __DIR__ . '/../../src/Auth/Model',
                __DIR__ . '/../../src/Order/Model',
                __DIR__ . '/../../src/Shop/Model',
            ],
            'types' => [
                App\Data\Doctrine\Type\AmountType::class,
                App\Data\Doctrine\Type\QuantityType::class,
                App\Data\Doctrine\Type\UuidType::class,

                App\Data\Doctrine\Type\Auth\UserEmailType::class,
                App\Data\Doctrine\Type\Auth\UserRoleType::class,
                App\Data\Doctrine\Type\Auth\UserStatusType::class,

                App\Data\Doctrine\Type\Order\CartStatusType::class,

                App\Data\Doctrine\Type\Shop\ProductStatusType::class,
            ],
        ],
    ],
];
