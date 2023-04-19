<?php

return json_decode(file_get_contents(dirname(__FILE__). DIRECTORY_SEPARATOR. "json" . DIRECTORY_SEPARATOR . basename(__FILE__, '.php') .".json"), true);
