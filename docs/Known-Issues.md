# Known Issues

## Doctrine Pagination Issues with UUIDs
The [paginator](https://github.com/doctrine/orm/blob/master/lib/Doctrine/ORM/Tools/Pagination/Paginator.php) provided by Doctrine has a known issue when it is used in conjunction
with UUIDs.

The issue occurs when calling the `getIterator` method, a fatal exception will be thrown when the UUID objects are treated as entities and
Doctrine tries to load metadata for the objects which does not exist.

To work around this, you need to pass `false` as the second parameter when using the constructor of the paginator object.
This will prevent Doctrine from trying to join collections which is where the issue is isolated to.
