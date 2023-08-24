# A simple package to list hotels

## Installation
Using composer:

```composer require Vitorschweder/Xlr8```

## Example to use:

```
<?php

use Vitorschweder\Xlr8\Search;

require '../vendor/autoload.php';

Search::getNearbyHotels('-27.2073518677943', '-49.65282331831691', 'proximity');
```
