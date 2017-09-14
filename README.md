# doctrine-static-meta

An implementation of Doctrine using the PHP Static Meta Data driver and no annotations

In the src folder you have some traits which are ready to use in your own project

There is also an ExampleEntities Folder which contains a small demo implementation

## Generating Example Schema

please look at [this bash script](example/createExampleDb.bash)

This assumes you are running a bash shell on a host that also has MySQL installed on localhost. You might want to edit some of the configuration values.

To create the example schema, simply run the bash script.

If you do not want to have to repeatedly put in your root password, you have the option of running

```bash

export rootPass="my root pass"
```
Which will then be used by the script without prompting for it

## Using in your own projects

To use this concept in your own projects, simply

```bash
composer require edmonds/doctrine-static-meta
```
And then create your entity classes and use the [UsesPHPMetaData.php](src/Traits/UsesPHPMetaData.php) Trait.

For scalar properties, you can simply declare the private properties and generate your getters and setters and then you must have a method beginning with 



