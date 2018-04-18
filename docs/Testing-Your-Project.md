# Testing Your Project

The generated code for your project comes with a reasonable test coverage out of the box.

This is built around the [AbstractEntityTest](./../src/Entity/AbstractEntityTest.php)

The Abstract Entity test handles the creation and of a test entity and also associated entities.

## Faker

Faker is used to generate the data and there is a mechanism for Fields to provide their own Faker data provider if the standard Faker guessing mechanism is not good enough.

### Custom Faker Data

Faker does a great job of guessing values to provide based on the Entity meta data. However for some fields you might want to roll somethign more custom.

This can be done by creating a new class that implements the [FakerDataProviderInterface](./../src/Entity/Fields/FakerData/FakerDataProviderInterface.php)

Once you have created the class, you also need to override the AbstractEntityTest::FAKER_DATA_PROVIDERS constant in your child test class

