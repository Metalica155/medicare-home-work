<?php

namespace App\Domain\Appointment\Services\Transition;

use Illuminate\Container\Container;

class RuleResolver
{
    public function __construct(
        private readonly Container $container,
    ) {}

    /**
     * @param class-string<Rule> $ruleClass
     */
    public function resolve(string $ruleClass): Rule
    {
        return $this->container->make($ruleClass);
    }
}
