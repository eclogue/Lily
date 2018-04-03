# Lily
Lily is a yaml parser that support import .yaml file from submodule

### usage

`composer require superbogy/Lily`


yaml :

```yaml

parameters:
  $ref: ./paramters/index.yaml
paths:
  $ref: ./routers/index.yaml
definitions:
  $ref: ./definitions/index.yaml
```

example:

```php
use Lily\Parser;

$root = ROOT;
$lily = new Parser($root, $entry);

$data = $lily->run();

```

