# Working with Symfony

## Configuration
For Symfony to work with DSM correctly, the `doctrine.yaml` needs updating.
It is suggested to keep your entities separate from your main Symfony code base.

We are assuming that your entity data will be stored in `vendor` as a Composer package.
Using the configuration below, you can adjust it to match your paths and namespaces.
```yaml
orm:
        auto_generate_proxy_classes: true
        default_entity_manager: default

        proxy_namespace: DoctrineProxies\__CG__\VendorName\PackageName\Entities
        proxy_dir: '%kernel.project_dir%/vendor/VendorName/package-name/proxies'
        entity_managers:
            default:
                connection: default
                auto_mapping: true
                mappings:
                    Main:
                        is_bundle: false
                        type: staticphp
                        dir: '%kernel.project_dir%/vendor/VendorName/package-name/src/Entities'
                        prefix: 'VendorName\PackageName\Entities'
                        alias: VendorName\PackageName\Entities
                    Traits:
                        is_bundle: false
                        type: staticphp
                        dir: '%kernel.project_dir%/vendor/VendorName/package-name/src/Entity'
                        prefix: 'VendorName\PackageName\Entity'
                        alias: VendorName\PackageName\Entities

```
 
## Working with UUIDs
When using UUIDs, notably when trying to load an entity via a route parameter - it is important that you initialise the UUID correctly.
The correct way to convert a string UUID to an actual UUID is to use the [UuidFactory](..//src/Entity/Fields/Factories/UuidFactory.php).
To convert a string to a UUID instance, use the `getOrderedTimeUuidFromString` method and pass your string.
