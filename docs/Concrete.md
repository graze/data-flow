# Concrete API access to DataFlow

There are multiple different ways to create a DataFlow:

## Static Building

This is the shortest way, but requires static access to Flow. Each method represents a Flow class that gets added to the 'Flow'.

``` php
$flow = Flow::moveFile($target)->gzip();
$flow->flow($file);
```

## Concrete Building

Each of the classes can however be built manually.

### Manually

```php
$moveFile = new MoveFile($target);
$gzip = new Gzip();
```

### Builder

```php
$builder = new Builder();
$builder->setLogger($logger);
$moveFile = $builder->buildFlow('moveFile', [$target]);
$gzip = $builder->buildFlow(Gzip::class);
```

## Creating Flows

### Constructor

The `Flow`,`Run` and `Each` Constructors each take a variable number of flows.

```php
$flow = new Flow(
    new MoveFile($target)
    new Gzip()
);
$flow->flow($file);
```

### Adding

This can be accessed fluently or not.

```php
$flow = new Flow();
$flow->add(new MoveFile($target))
     ->add(new Gzip())
     ->flow($file);
```

