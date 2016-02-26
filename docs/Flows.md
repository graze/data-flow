# Flows

A `Flow` is a defined series of individual steps each of which modify some data that gets passed to the next step in the flow.

## Creating Flows

### Building the flow steps

Each step of a flow can be build individually.

#### Static calls

```php
$moveFile = Flow::moveFile($target);
$gzip = Flow::gzip();
```

#### Manually

```php
$moveFile = new MoveFile($target);
$gzip = new Gzip();
```

#### Builder

With the build and static accessors, an optional logger will automatically get injected into newly created classes.

```php
$builder = new Builder();
$builder->setLogger($logger);
$moveFile = $builder->buildFlow('moveFile', [$target]);
$gzip = $builder->buildFlow(Gzip::class);
```

## Creating Flows

### Simple Static Binding

This is the shortest way, but requires static access to Flow. Each method represents a Flow class that gets added to the 'Flow'.

``` php
$output = Flow::moveFile($target)->gzip()->flow($file);
```

### Constructor

The `Flow`,`Run` and `ToAll` Constructors each take a variable number of flows.

```php
$flow = new Flow(
    $moveFile,
    $gzip
);
$output = $flow->flow($file);
```

### Adding

This can be accessed fluently or not.

```php
$flow = new Flow();
$output = $flow->add($moveFile)
               ->add($gzip)
               ->flow($file);
```
