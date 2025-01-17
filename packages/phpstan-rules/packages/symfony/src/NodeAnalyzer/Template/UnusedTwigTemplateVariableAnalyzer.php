<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\NodeAnalyzer\Template;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\MethodCallArrayResolver;

final class UnusedTwigTemplateVariableAnalyzer
{
    public function __construct(
        private TwigVariableNamesResolver $twigVariableNamesResolver,
        private MethodCallArrayResolver $methodCallArrayResolver
    ) {
    }

    /**
     * @return string[]
     */
    public function resolveMethodCallAndTemplate(
        MethodCall $methodCall,
        array $templateFilePaths,
        Scope $scope
    ): array {
        $templatesUsedVariableNames = [];
        foreach ($templateFilePaths as $templateFilePath) {
            $currentUsedVariableNames = $this->twigVariableNamesResolver->resolveFromFilePath($templateFilePath);
            $templatesUsedVariableNames = array_merge($templatesUsedVariableNames, $currentUsedVariableNames);
        }

        $passedVariableNames = $this->methodCallArrayResolver->resolveArrayKeysOnPosition($methodCall, $scope, 1);

        return array_diff($passedVariableNames, $templatesUsedVariableNames);
    }
}
