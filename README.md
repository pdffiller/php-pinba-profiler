-How to use

1) Install Pinba php extension, Pinba Engine with Intaro Pinboard
2) Modify your composer json

```json
 {
     "require": {
         "pdffiller/php-pinba-profiler": "dev-master"
     },
   "repositories": [
         {
             "type": "vcs",
             "url":  "https://github.com/pdffiller/php-pinba-profiler.git"
         }
     ]
 }
```

3) add timers to your code

```php
<?php
require('vendor/autoload.php');

use Pdffiller\PinbaProfiler\PinbaProfiler;

$tags = array('category'=>'Database', 'group'=>'ClassName::method', 'mytag'=>'tagValue');

PinbaProfiler::timerStart($tags);

sleep(1);

PinbaProfiler::timerStop($tags);
```

category: Database, Memcache, API etc
group: TDb - subgroup or class, connect - operation

group must be separated with ::
