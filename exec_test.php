<?php
exec(PHP_BINARY . ' -r "http_response_code(400); echo http_response_code();" 2>&1', $out, $exit);
var_dump($out, $exit);
