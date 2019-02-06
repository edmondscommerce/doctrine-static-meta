NOTE: This is a work in progress

# How to load fixtures in a test #



# How to create custom fixtures for your test #



# How to flush fixture data to the database #

The DSM fixtures keep the database clean by running the tests within a database transaction and then rolling this
back after the test is complete. This means that you can never view the fixture data in the database. In order to do
this you can temporarily update the following code:

```php
// \EdmondsCommerce\VaultTestingService\DsmFixturesTrait::removeFixtures

    public function removeFixtures(): void
    {
        $this->connection->getConnection()->rollBack();
    }
```

to

```php
// \EdmondsCommerce\VaultTestingService\DsmFixturesTrait::removeFixtures

    public function removeFixtures(): void
    {
        $this->connection->getConnection()->commit();
    }
```

This change will mean that you can only run one test at a time and you'll need to ensure that the database is dropped
and recreated between each run.