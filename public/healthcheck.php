<?php

print("true\n");
print("Debug: " . $_REQUEST['debug']);

if (!empty($_REQUEST['debug'])) {
    print(json_encode($_SERVER, JSON_PRETTY_PRINT));
}
