<?php

$finder = Symfony\Component\Finder\Finder::create()
                                         ->notPath('bootstrap/*')
                                         ->notPath('storage/*')
                                         ->notPath('storage/*')
                                         ->notPath('resources/view/mail/*')
                                         ->in([
                                             __DIR__ . '/src',
                                             __DIR__ . '/tests',
                                         ])
                                         ->name('*.php')
                                         ->notName('*.blade.php')
                                         ->ignoreDotFiles(true)
                                         ->ignoreVCS(true);

return PhpCsFixer\Config::create()->setRules([
    '@PSR2'                             => true,
    'array_syntax'                      => ['syntax' => 'short'],
    'ordered_imports'                   => ['sortAlgorithm' => 'alpha'],
    'no_unused_imports'                 => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline_array' => true,
    'phpdoc_scalar'                     => true,
    'unary_operator_spaces'             => true,
    'function_declaration'              => [
        'closure_function_spacing' => 'none',
    ],
    'binary_operator_spaces'            => [
        'align_equals'       => true,
        'align_double_arrow' => true,
    ],
    'single_import_per_statement'       => false,
    'blank_line_before_statement'       => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'phpdoc_single_line_var_spacing'    => true,
    'phpdoc_var_without_name'           => true,
    'method_argument_space'             => [
        'on_multiline'                     => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
])->setFinder($finder);
