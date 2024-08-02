<?php

use Logto\Sdk\LogtoClient;
use Logto\Sdk\LogtoConfig;

$client = new LogtoClient(
  new LogtoConfig(
    endpoint: 'https://login.justicerp.com/',
    appId: 'ampx29jqpxazohq0qb9ho',
    appSecret: 'MWUEPAgE28XFEfAdtFL3UQKVmUSU8Bxs',
  ),
);
