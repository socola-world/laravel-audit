<?php

foreach (config('app.aliases') as $alias => $class) {
    if (class_exists($alias)) {
        continue;
    }

    class_alias($class, $alias);
}
