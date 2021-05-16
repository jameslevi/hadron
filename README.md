# Hadron

![](https://img.shields.io/badge/packagist-v1.0.0-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a) ![](https://img.shields.io/badge/license-MIT-informational?style=flat&logo=<LOGO_NAME>&logoColor=white&color=2bbc8a)  

Is a simple PHP library for MySQL using PDO.

## Installation
1. You can install via composer.
```
composer require jameslevi/hadron
```
2. Paste the following code above your project if not using any PHP framework.
```php
require_once __DIR__.'/vendor/autoload.php';
```
3. Import hadron into your project.
```php
use Graphite\Component\Hadron\Hadron;
```

## The Basics
This is a basic example on how to implement hadron in your project.
```php
<?php

// Import hadron into your project.
use Graphite\Component\Hadron\Hadron;

// Create a new Hadron instance.
$conn = new Hadron('database');

// Set your MySQL username and password.
$conn->setCredentials('username', 'password');

// Create a new query.
$query = $conn->query('SELECT * FROM users WHERE id = :id LIMIT :start, :offset');

// Set the value of the parameters.
$query->addParam('id', 1)
      ->addParam('start', 0)
      ->addParam('offset', 10);

// Get the results from the query.
$results = $query->get();

// Close connection from the database.
$conn->close();
```

## Connecting with your MySQL database
1. Create a new hadron instance.
```php
$conn = new Hadron('database');
```
2. You can also use this magic method.
```php
$conn = Hadron::database();
```
3. Set your MySQL credentials.
```php
$conn->setCredentials('username', 'password');
```
4. You can also set your server name.
```php
$conn->setServerName('localhost');
```
5. You can set your MySQL port number if not using 3306.
```php
$conn->setPort(3303);
```
6. Set the default charset from utf8mb4 to your choice.
```php
$conn->setCharset('utf8mb4');
```
7. The connection will only be established after calling the connect method.
```php
$conn->connect();
```
8. You can determine if connection was established using isConnected method.
```php
$conn->isConnected();
```
9. Always close each connection after use.
```php
$conn->close();
```

## Getting data from the database
1. Use get method when your query expects result from the database.
```php
$query = $conn->query('SELECT first_name, last_name, gender FROM members')->get();
```
2. You can count the number of rows returned.
```php
$count = $query->numRows();
```
3. You can check if the query returns nothing.
```php
$query->empty();
```
4. You can get the first and the last row of the result.
```php
var_dump($query->first());
var_dump($query->last());
```
5. You can also get row by index number.
```php
var_dump($query->get(11)); // Return the 11th result.
```
6. If query returns only a single row, you can directly get each column.
```php
echo $query->first_name . ' ' . $query->last_name;
```
7. And if query returns a multiple row, you can access each column like object properties.
```php
foreach($query->all() as $member)
{
    echo $member->name;
}
```
8. You can return the result as an array.
```php
var_dump($query->toArray());
```
9. You can also return the result as json.
```php
echo $query->toJson();
```

## Executing Queries
1. You can use exec method to execute queries expecting no results such as UPDATE, INSERT and DELETE.
```php
$query = $conn->query('UPDATE members SET first_name = :first_name WHERE id = :id');

$query->addParam('first_name', 'James Levi')
      ->addParam('id', 1);
      
$query->exec();
```
2. You can determine if query is a success.
```php
$query->success();
```
3. You can also know how many rows your query has affected.
```php
echo $query->affectedRows();
```

## Placeholder
Instead of concatenating string to build your SQL query, you can use placeholder to inject values into your SQL query.
```php
$query = $conn->query('INSERT members (`first_name`,`last_name`,`gender`) VALUES(:first_name, :last_name, :gender)');

$query->addParam('first_name', 'James Levi')
      ->addParam('last_name', 'Crisostomo')
      ->addParam('gender', 'male');
      
$query->exec();
```
You can also directly pass your placeholder in the query method.
```php
$query = $conn->query('SELECT * FROM members WHERE id = :id', array('id' => 1))->get();
```

## Contribution
For issues, concerns and suggestions, you can email James Crisostomo via nerdlabenterprise@gmail.com.

## License
This package is an open-sourced software licensed under [MIT](https://opensource.org/licenses/MIT) License.
