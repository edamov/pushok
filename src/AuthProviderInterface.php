<?php

namespace Pushok;

interface AuthProviderInterface
{
    public function authenticateClient($request);
}