<?php
$env = file_get_contents(__DIR__ . '/../.env');
preg_match_all('%export (?<key>[^=]+)="(?<value>[^"]+?)"%', $env, $matches);
if (empty($matches['key'])) {
    throw new Exception('Failed to parse .env file');
}
foreach ($matches['key'] as $k => $key) {
    $_SERVER[$key] = $matches['value'][$k];
}