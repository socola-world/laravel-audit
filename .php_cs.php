<?php
/*
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.8
 *
 * */
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12' => true,
    '@PSR2' => true,
    'align_multiline_comment' => [
        'comment_type' => 'phpdocs_only',
    ],
    'array_indentation' => true,
    'array_push' => false,
    'array_syntax' => [
        'syntax' => 'short',
    ],
    'assign_null_coalescing_to_coalesce_equal' => false, // version_compare(phpversion(), '7.4', '>='),
    'backtick_to_shell_exec' => true,
    'binary_operator_spaces' => [
        'default' => 'single_space',
        'operators' => [
//            [
//                'align',
//                'align_single_space',
//                'align_single_space_minimal',
//                'single_space',
//                'no_space',
//                null,
//            ]
//            '=' => '',
//            '*' => '',
//            '/' => '',
//            '%' => '',
//            '<' => '',
//            '>' => '',
//            '|' => '',
//            '^' => '',
//            '+' => '',
//            '-' => '',
//            '&' => '',
//            '&=' => '',
//            '&&' => '',
//            '||' => '',
//            '.=' => '',
//            '/=' => '',
//            '=>' => '',
//            '==' => '',
//            '>=' => '',
//            '===' => '',
//            '!=' => '',
//            '<>' => '',
//            '!==' => '',
//            '<=' => '',
//            'and' => '',
//            'or' => '',
//            'xor' => '',
//            '-=' => '',
//            '%=' => '',
//            '*=' => '',
//            '|=' => '',
//            '+=' => '',
//            '<<' => '',
//            '<<=' => '',
//            '>>' => '',
//            '>>=' => '',
//            '^=' => '',
//            '**' => '',
//            '**=' => '',
//            '<=>' => '',
//            '??' => '',
//            '??=' => '',
        ],
    ],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => [
        'statements' => [
            'break',
//            'case',
            'continue',
            'declare',
//            'default',
//            'phpdoc',
            'do',
            'exit',
            'for',
            'foreach',
            'goto',
            'if',
//            'include',
//            'include_once',
//            'require',
//            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
            'yield',
            'yield_from',
        ],
    ],
    'braces' => [
        'allow_single_line_anonymous_class_with_empty_body' => false,
        'allow_single_line_closure' => false,
        'position_after_anonymous_constructs' => 'same',
        'position_after_control_structures' => 'same',
        'position_after_functions_and_oop_constructs' => 'next',
    ],
    'cast_spaces' => [
        'space' => 'single',
    ],
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
            'trait_import' => 'one',
            'case' => 'none',
        ],
    ],
    'class_definition' => [
//        'inline_constructor_arguments' => true,
        'multi_line_extends_each_single_line' => true,
        'single_item_single_line' => true,
        'single_line' => false,
//        'space_before_parenthesis' => false,
    ],
    'class_keyword_remove' => false,
    'class_reference_name_casing' => true,
    'clean_namespace' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
//    'combine_nested_dirname' => false,
//    'comment_to_phpdoc' => false,
    'compact_nullable_typehint' => true,
    'concat_space' => [
        'spacing' => 'none',
    ],
    'constant_case' => [
        'case' => 'lower'
    ],
    'control_structure_continuation_position' => [
        'position' => 'same_line',
    ],
//    'date_time_create_from_format_call' => false,
//    'date_time_immutable' => false,
    'declare_equal_normalize' => [
        'space' => 'single',
    ],
    'declare_parentheses' => true,
//    'declare_strict_types' => false,
//    'dir_constant' => true,
//    'doctrine_annotation_array_assignment' => [
//        'ignored_tags' => [
//            'abstract',
//            'access',
//            'code',
//            'deprec',
//            'encode',
//            'exception',
//            'final',
//            'ingroup',
//            'inheritdoc',
//            'inheritDoc',
//            'magic',
//            'name',
//            'toc',
//            'tutorial',
//            'private',
//            'static',
//            'staticvar',
//            'staticVar',
//            'throw',
//            'api',
//            'author',
//            'category',
//            'copyright',
//            'deprecated',
//            'example',
//            'filesource',
//            'global',
//            'ignore',
//            'internal',
//            'license',
//            'link',
//            'method',
//            'package',
//            'param',
//            'property',
//            'property-read',
//            'property-write',
//            'return',
//            'see',
//            'since',
//            'source',
//            'subpackage',
//            'throws',
//            'todo',
//            'TODO',
//            'usedBy',
//            'uses',
//            'var',
//            'version',
//            'after',
//            'afterClass',
//            'backupGlobals',
//            'backupStaticAttributes',
//            'before',
//            'beforeClass',
//            'codeCoverageIgnore',
//            'codeCoverageIgnoreStart',
//            'codeCoverageIgnoreEnd',
//            'covers',
//            'coversDefaultClass',
//            'coversNothing',
//            'dataProvider',
//            'depends',
//            'expectedException',
//            'expectedExceptionCode',
//            'expectedExceptionMessage',
//            'expectedExceptionMessageRegExp',
//            'group',
//            'large',
//            'medium',
//            'preserveGlobalState',
//            'requires',
//            'runTestsInSeparateProcesses',
//            'runInSeparateProcess',
//            'small',
//            'test',
//            'testdox',
//            'ticket',
//            'uses',
//            'SuppressWarnings',
//            'noinspection',
//            'package_version',
//            'enduml',
//            'startuml',
//            'psalm',
//            'phpstan',
//            'template',
//            'fix',
//            'FIXME',
//            'fixme',
//            'override',
//        ],
//        'operator' => '=', /* =, :*/
//    ],
//    'doctrine_annotation_braces' => [
//        'ignored_tags' => [
//            'abstract',
//            'access',
//            'code',
//            'deprec',
//            'encode',
//            'exception',
//            'final',
//            'ingroup',
//            'inheritdoc',
//            'inheritDoc',
//            'magic',
//            'name',
//            'toc',
//            'tutorial',
//            'private',
//            'static',
//            'staticvar',
//            'staticVar',
//            'throw',
//            'api',
//            'author',
//            'category',
//            'copyright',
//            'deprecated',
//            'example',
//            'filesource',
//            'global',
//            'ignore',
//            'internal',
//            'license',
//            'link',
//            'method',
//            'package',
//            'param',
//            'property',
//            'property-read',
//            'property-write',
//            'return',
//            'see',
//            'since',
//            'source',
//            'subpackage',
//            'throws',
//            'todo',
//            'TODO',
//            'usedBy',
//            'uses',
//            'var',
//            'version',
//            'after',
//            'afterClass',
//            'backupGlobals',
//            'backupStaticAttributes',
//            'before',
//            'beforeClass',
//            'codeCoverageIgnore',
//            'codeCoverageIgnoreStart',
//            'codeCoverageIgnoreEnd',
//            'covers',
//            'coversDefaultClass',
//            'coversNothing',
//            'dataProvider',
//            'depends',
//            'expectedException',
//            'expectedExceptionCode',
//            'expectedExceptionMessage',
//            'expectedExceptionMessageRegExp',
//            'group',
//            'large',
//            'medium',
//            'preserveGlobalState',
//            'requires',
//            'runTestsInSeparateProcesses',
//            'runInSeparateProcess',
//            'small',
//            'test',
//            'testdox',
//            'ticket',
//            'uses',
//            'SuppressWarnings',
//            'noinspection',
//            'package_version',
//            'enduml',
//            'startuml',
//            'psalm',
//            'phpstan',
//            'template',
//            'fix',
//            'FIXME',
//            'fixme',
//            'override',
//        ],
//        'syntax' => 'without_braces' /* with_braces, without_braces */
//    ],
//    'doctrine_annotation_indentation' => [
//        'ignored_tags' => [
//            'abstract',
//            'access',
//            'code',
//            'deprec',
//            'encode',
//            'exception',
//            'final',
//            'ingroup',
//            'inheritdoc',
//            'inheritDoc',
//            'magic',
//            'name',
//            'toc',
//            'tutorial',
//            'private',
//            'static',
//            'staticvar',
//            'staticVar',
//            'throw',
//            'api',
//            'author',
//            'category',
//            'copyright',
//            'deprecated',
//            'example',
//            'filesource',
//            'global',
//            'ignore',
//            'internal',
//            'license',
//            'link',
//            'method',
//            'package',
//            'param',
//            'property',
//            'property-read',
//            'property-write',
//            'return',
//            'see',
//            'since',
//            'source',
//            'subpackage',
//            'throws',
//            'todo',
//            'TODO',
//            'usedBy',
//            'uses',
//            'var',
//            'version',
//            'after',
//            'afterClass',
//            'backupGlobals',
//            'backupStaticAttributes',
//            'before',
//            'beforeClass',
//            'codeCoverageIgnore',
//            'codeCoverageIgnoreStart',
//            'codeCoverageIgnoreEnd',
//            'covers',
//            'coversDefaultClass',
//            'coversNothing',
//            'dataProvider',
//            'depends',
//            'expectedException',
//            'expectedExceptionCode',
//            'expectedExceptionMessage',
//            'expectedExceptionMessageRegExp',
//            'group',
//            'large',
//            'medium',
//            'preserveGlobalState',
//            'requires',
//            'runTestsInSeparateProcesses',
//            'runInSeparateProcess',
//            'small',
//            'test',
//            'testdox',
//            'ticket',
//            'uses',
//            'SuppressWarnings',
//            'noinspection',
//            'package_version',
//            'enduml',
//            'startuml',
//            'psalm',
//            'phpstan',
//            'template',
//            'fix',
//            'FIXME',
//            'fixme',
//            'override',
//        ],
//        'indent_mixed_lines' => false,
//    ],
    'doctrine_annotation_spaces' => [
        'before_argument_assignments' => true,
        'after_argument_assignments' => true,
        /**/
        'before_array_assignments_colon' => true,
        'after_array_assignments_colon' => true,
        /**/
        'before_array_assignments_equals' => true,
        'after_array_assignments_equals' => true,
        /**/
        'around_commas' => true,

//        'ignored_tags' => [
//            'abstract',
//            'access',
//            'code',
//            'deprec',
//            'encode',
//            'exception',
//            'final',
//            'ingroup',
//            'inheritdoc',
//            'inheritDoc',
//            'magic',
//            'name',
//            'toc',
//            'tutorial',
//            'private',
//            'static',
//            'staticvar',
//            'staticVar',
//            'throw',
//            'api',
//            'author',
//            'category',
//            'copyright',
//            'deprecated',
//            'example',
//            'filesource',
//            'global',
//            'ignore',
//            'internal',
//            'license',
//            'link',
//            'method',
//            'package',
//            'param',
//            'property',
//            'property-read',
//            'property-write',
//            'return',
//            'see',
//            'since',
//            'source',
//            'subpackage',
//            'throws',
//            'todo',
//            'TODO',
//            'usedBy',
//            'uses',
//            'var',
//            'version',
//            'after',
//            'afterClass',
//            'backupGlobals',
//            'backupStaticAttributes',
//            'before',
//            'beforeClass',
//            'codeCoverageIgnore',
//            'codeCoverageIgnoreStart',
//            'codeCoverageIgnoreEnd',
//            'covers',
//            'coversDefaultClass',
//            'coversNothing',
//            'dataProvider',
//            'depends',
//            'expectedException',
//            'expectedExceptionCode',
//            'expectedExceptionMessage',
//            'expectedExceptionMessageRegExp',
//            'group',
//            'large',
//            'medium',
//            'preserveGlobalState',
//            'requires',
//            'runTestsInSeparateProcesses',
//            'runInSeparateProcess',
//            'small',
//            'test',
//            'testdox',
//            'ticket',
//            'uses',
//            'SuppressWarnings',
//            'noinspection',
//            'package_version',
//            'enduml',
//            'startuml',
//            'psalm',
//            'phpstan',
//            'template',
//            'fix',
//            'FIXME',
//            'fixme',
//            'override',
//        ],
        'around_parentheses' => true,
    ],
    'echo_tag_syntax' => [
        'format' => \PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer::FORMAT_LONG,
        'long_function' => \PhpCsFixer\Fixer\PhpTag\EchoTagSyntaxFixer::LONG_FUNCTION_ECHO,
        'shorten_simple_statements_only' => false,
    ],
    'elseif' => true,
    'empty_loop_body' => [
        'style' => 'braces',
    ],
//    'empty_loop_condition' => [
//        'style' => 'while',
//    ],
    'encoding' => true,
//    'ereg_to_preg' => true,
//    'error_suppression' => [
//        'mute_deprecation_error' => false,
//        'noise_remaining_usages' => false,
//        'noise_remaining_usages_exclude' => 'array',
//    ],
    'escape_implicit_backslashes' => [
        'double_quoted' => true,
        'heredoc_syntax' => true,
        'single_quoted' => false,
    ],
    'explicit_indirect_variable' => true,
    'explicit_string_variable' => true,
    'final_class' => false,
//    final_internal_class
    'final_public_method_for_abstract_class' => false,
    'fopen_flag_order' => false,
    'fopen_flags' => false,
//    'fopen_flags' => [
//        'b_mode' => true,
//    ],
    'full_opening_tag' => true,
    'fully_qualified_strict_types' => true,
    'function_declaration' => [
        'closure_function_spacing' => \PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer::SPACING_ONE,
        'trailing_comma_single_line' => true,
    ],
//    'function_to_constant' => false,
    'function_typehint_space' => true,
//    'general_phpdoc_annotation_remove' => []
//    'general_phpdoc_tag_rename' => true,
    'get_class_to_class_keyword' => false,
    'global_namespace_import' => [
        'import_classes' => true,
        'import_constants' => null,
        'import_functions' => null,
    ],
    'group_import' => false,
//    'header_comment' => [
//        'comment_type' => \PhpCsFixer\Fixer\Comment\HeaderCommentFixer::HEADER_PHPDOC,
//        'header' => 'Made by Socola Dai Ca',
//        'location' => 'after_declare_strict',
//        'separate' => 'both',
//    ],
//    'heredoc_indentation'
//    'heredoc_to_nowdoc' => false,
    'implode_call' => false,
    'include' => true,
    'increment_style' => [
        'style' => \PhpCsFixer\Fixer\Operator\IncrementStyleFixer::STYLE_POST,
    ],
    'indentation_type' => true,
    'integer_literal_case' => true,
    'is_null' => false,
    'lambda_not_used_import' => true,
    'line_ending' => true,
    'linebreak_after_opening_tag' => true,
    'list_syntax' => [
        'syntax' => 'short',
    ],
    'logical_operators' => true,
    'lowercase_cast' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_constant_casing' => true,
    'magic_method_casing' => true,
//    'mb_str_functions' => true,
    'method_argument_space' => [
//        'after_heredoc' => true,
        'keep_multiple_spaces_after_comma' => false,
        'on_multiline' => 'ensure_fully_multiline',
    ],
    'method_chaining_indentation' => true,
//    'modernize_strpos' => true,
//    'modernize_types_casting' => false,
    'multiline_comment_opening_closing' => true,
    'multiline_whitespace_before_semicolons' => [
//        'strategy' => \PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NEW_LINE_FOR_CHAINED_CALLS, // should
        'strategy' => \PhpCsFixer\Fixer\Semicolon\MultilineWhitespaceBeforeSemicolonsFixer::STRATEGY_NO_MULTI_LINE,
    ],
    'native_function_casing' => true,
//    native_constant_invocation
    'native_function_type_declaration_casing' => true,
    'new_with_braces' => [
        'anonymous_class' => true,
        'named_class' => true,
    ],
//    'no_alias_functions' => true,
    'no_alias_language_construct_call' => true,
    'no_alternative_syntax' => [
        'fix_non_monolithic_code' => true,
    ],
//    'no_binary_string'
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
//    'no_blank_lines_before_namespace' => true, conflict with "single_blank_line_before_namespace"
    'no_break_comment' => false,
    'no_closing_tag' => true,
    'no_empty_comment' => false,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_extra_blank_lines' => [
        'tokens' => [
            'break',
            'continue',
            'curly_brace_block',
            'extra',
            'parenthesis_brace_block',
            'return',
            'square_brace_block',
            'throw',
            'use',
            'use_trait',
            'switch',
            'case',
            'default',
        ],
    ],
//    'no_homoglyph_names' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_mixed_echo_print' => [
        'use' => 'echo',
    ],
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_null_property_initialization' => true,
    'no_php4_constructor' => true,
    'no_short_bool_cast' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_space_around_double_colon' => true,
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => [
        'positions' => [
            'inside',
            'outside'
        ],
    ],
    'no_spaces_inside_parenthesis' => true,
    'no_superfluous_elseif' => true,
//    'no_superfluous_phpdoc_tags' => false,
    'no_superfluous_phpdoc_tags' => [
        'allow_mixed' => false,
        'allow_unused_params' => false,
        'remove_inheritdoc' => false,
    ],
    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_trailing_comma_in_singleline_function_call' => true,
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,
    'no_trailing_whitespace_in_string' => false,
    'no_unneeded_control_parentheses' => [
        'statements' => [
            'break',
            'clone',
            'continue',
            'echo_print',

            'return',
            'switch_case',
            'yield',
            'yield_from',
        ]
    ],
//    'no_unneeded_curly_braces'
//    'no_unneeded_final_method'
    'no_unneeded_import_alias' => true,
//    'no_unreachable_default_argument_value' => true,
    'no_unset_cast' => true,
    'no_unset_on_property' => false,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
//    'no_useless_sprintf' => false,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
//    'non_printable_character' =>
    'normalize_index_brace' => true,
//    'not_operator_with_space' => true,
//    'not_operator_with_successor_space' => true, // optional
    'nullable_type_declaration_for_default_null_value' => [
        'use_nullable_type_declaration' => true,
    ],
    'object_operator_without_whitespace' => true,
//    'octal_notation'
    'operator_linebreak' => [
        'only_booleans' => true,
        'position' => 'beginning',
    ],
//    'ordered_class_elements' => [
//        'order' => [
//            'use_trait',
//            'public',
//            'protected',
//            'private',
//            'case',
//            'constant',
//            'constant_public',
//            'constant_protected',
//            'constant_private',
//            'property',
//            'property_static',
//            'property_public',
//            'property_protected',
//            'property_private',
//            'property_public_readonly',
//            'property_protected_readonly',
//            'property_private_readonly',
//            'property_public_static',
//            'property_protected_static',
//            'property_private_static',
//            'method',
//            'method_abstract',
//            'method_static',
//            'method_public',
//            'method_protected',
//            'method_private',
//            'method_public_abstract',
//            'method_protected_abstract',
//            'method_private_abstract',
//            'method_public_abstract_static',
//            'method_protected_abstract_static',
//            'method_private_abstract_static',
//            'method_public_static',
//            'method_protected_static',
//            'method_private_static',
//            'construct',
//            'destruct',
//            'magic',
//            'phpunit',
//        ],
//        'sort_algorithm' => \PhpCsFixer\Fixer\ClassNotation\OrderedClassElementsFixer::SORT_ALPHA,
//    ],
    'ordered_imports' => [
        'imports_order' => [
            \PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_CLASS,
            \PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_FUNCTION,
            \PhpCsFixer\Fixer\Import\OrderedImportsFixer::IMPORT_TYPE_CONST,
        ],
        'sort_algorithm' => \PhpCsFixer\Fixer\Import\OrderedImportsFixer::SORT_ALPHA,
    ],
//    'ordered_interfaces' => [
//        'direction' => 'ascend',
//        'order' => 'alpha'
//    ],
    'ordered_traits' => false,
//    'php_unit_construct'
//    'php_unit_dedicate_assert'
//    'php_unit_dedicate_assert_internal_type'
//    'php_unit_expectation'
//    'php_unit_fqcn_annotation'
//    'php_unit_internal_class'
    'php_unit_method_casing' => [
        'case' => \PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer::CAMEL_CASE,
    ],
//    'php_unit_mock'
//    'php_unit_mock_short_will_return'
//    'php_unit_namespaced'
//    'php_unit_no_expectation_annotation'
//    'php_unit_set_up_tear_down_visibility'
//    'php_unit_size_class'
//    'php_unit_strict' =>
    'php_unit_test_annotation' => [
        'style' => 'prefix',
    ],
    'php_unit_test_case_static_method_calls' => [
        'call_type' => \PhpCsFixer\Fixer\PhpUnit\PhpUnitTestCaseStaticMethodCallsFixer::CALL_TYPE_STATIC,
        'methods' => [
        ],
    ],
//    'php_unit_test_class_requires_covers'
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align' => [
        'align' => \PhpCsFixer\Fixer\Phpdoc\PhpdocAlignFixer::ALIGN_LEFT,
    ],
//    'phpdoc_annotation_without_dot' => false, // conflict with phpdoc_summary
    'phpdoc_indent' => true,
//    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_line_span' => [
        'const' => 'multi',
        'method' => 'multi',
        'property' => 'multi',
    ],
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => true,
    'phpdoc_no_empty_return' => true,
//    'phpdoc_no_package' => true,
    'phpdoc_no_useless_inheritdoc' => true,
    'phpdoc_order' => true,
    'phpdoc_order_by_value' => [
        'annotations' => [
            'author',
            'covers',
            'coversNothing',
            'dataProvider',
            'depends',
            'group',
            'internal',
            'method',
            'property',
            'property-read',
            'property-write',
            'requires',
            'throws',
            'uses',
        ],
    ],
    'phpdoc_return_self_reference' => [
        'replacements' => [
            'this' => '$this',
            '@this' => '$this',
            '$self' => 'self',
            '@self' => 'self',
            '$static' => 'static',
            '@static' => 'static',
        ]
    ],
    'phpdoc_scalar' => [
        'types' => [
            'boolean',
            'callback',
            'double',
            'integer',
            'real',
            'str',
        ],
    ],
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_tag_casing' => [
        'tags' => [
            'inheritDoc',
        ],
    ],
    'phpdoc_tag_type' => [
        'tags' => [
            'api' => 'annotation',
            'author' => 'annotation',
            'copyright' => 'annotation',
            'deprecated' => 'annotation',
            'example' => 'annotation',
            'global' => 'annotation',
            'inheritDoc' => 'annotation',
            'internal' => 'annotation',
            'license' => 'annotation',
            'method' => 'annotation',
            'package' => 'annotation',
            'param' => 'annotation',
            'property' => 'annotation',
            'return' => 'annotation',
            'see' => 'annotation',
            'since' => 'annotation',
            'throws' => 'annotation',
            'todo' => 'annotation',
            'uses' => 'annotation',
            'var' => 'annotation',
            'version' => 'annotation',
        ],
    ],
//    'phpdoc_to_comment' => true,
//    'phpdoc_to_param_type' => false,
//    'phpdoc_to_property_type' => false,
//    'phpdoc_to_return_type' => false // option
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => [
        'groups' => [
            'simple',
            'alias',
            'meta',
        ],
    ],
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha',
    ],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    'pow_to_exponentiation' => false,
    'protected_to_private' => false,
//    'psr_autoloading' => false,
    'random_api_migration' => [
        'replacements' => [
            'rand' => 'random_int',
//            'getrandmax' => 'mt_getrandmax',
//            'rand' => 'mt_rand',
//            'srand' => 'mt_srand',
        ],
    ],
//    'regular_callable_call' => false,
    'return_assignment' => true, // optional
    'return_type_declaration' => [
        'space_before' => 'none',
    ],
//    'self_accessor' => true,
//    'self_static_accessor' => false,
    'semicolon_after_instruction' => true,
//    'set_type_to_cast' => false,
    'short_scalar_cast' => true,
    'simple_to_complex_string_variable' => true,
    'simplified_if_return' => true,
//    'simplified_null_return' => false, // disabled by Shift
    'simplified_null_return' => true, // disabled by Shift
    'single_blank_line_at_eof' => true,
    'single_blank_line_before_namespace' => true,
    'single_class_element_per_statement' => [
        'elements' => [
            'const',
            'property',
        ],
    ],
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    'single_line_comment_spacing' => false,
    'single_line_comment_style' => [
        'comment_types' => [
//            'asterisk',
            'hash',
        ],
    ],
//    'single_line_throw' => false, // option
    'single_quote' => [
        'strings_containing_single_quote_chars' => false,
    ],
    'single_space_after_construct' => [
        'constructs' => [
            'abstract',
            'as',
            'attribute',
            'break',
            'case',
            'catch',
            'class',
            'clone',
            'comment',
            'const',
            'const_import',
            'continue',
            'do',
            'echo',
            'else',
            'elseif',
            'enum',
            'extends',
            'final',
            'finally',
            'for',
            'foreach',
            'function',
            'function_import',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'match',
            'named_argument',
            'namespace',
            'new',
            'open_tag_with_echo',
            'php_doc',
            'php_open',
            'print',
            'private',
            'protected',
            'public',
            'readonly',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'use',
            'use_lambda',
            'use_trait',
            'var',
            'while',
            'yield',
            'yield_from',
        ],
    ],
    'single_trait_insert_per_statement' => true, // option
    'space_after_semicolon' => true,
//    'standardize_increment' => false,
    'standardize_not_equals' => true,
//    'static_lambda' => false,
//    'strict_comparison' => false, // option
    'strict_param' => false,
//    'string_length_to_empty' => false,
//    'string_line_ending' => false,
    'switch_case_semicolon_to_colon' => true,
    'switch_case_space' => true,
    'switch_continue_to_break' => true,
    'ternary_operator_spaces' => true,
//    'ternary_to_elvis_operator' => true,
    'ternary_to_null_coalescing' => true,
    'trailing_comma_in_multiline' => [
        'after_heredoc' => true,
        'elements' => [
            \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::ELEMENTS_ARRAYS,
//            \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::ELEMENTS_ARGUMENTS, apply late has issue
            ...(version_compare(phpversion(), '8.0+', '>=') ? [
                \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::ELEMENTS_PARAMETERS,
            ] : []),
        ],
    ],
    'trim_array_spaces' => true,
    'types_spaces' => [
        'space' => 'none',
        'space_multiple_catch' => 'none',
    ],
    'unary_operator_spaces' => true,
    'use_arrow_functions' => false,
    'visibility_required' => [
        'elements' => [
            'property',
            'method',
            'const',
        ],
    ],
    'void_return' => false,
    'whitespace_after_comma_in_array' => true,
    'yoda_style' => [
        'always_move_variable' => false,
        'equal' => false,
        'identical' => false,
        'less_and_greater' => false,
    ],
    /*done*/
];

$finder = Finder::create()
    ->in(
        array_merge(
            [],
            is_dir('app') ? ['app'] : [],
            is_dir('config') ? ['config'] : [],
            is_dir('database') ? ['database'] : [],
            is_dir('resources') ? ['resources'] : [],
            is_dir('routes') ? ['routes'] : [],
            is_dir('tests') ? ['tests'] : [],
            is_dir('src') ? ['src'] : [],
        )
    )
//    ->in(
//        [
//
//        __DIR__.'/app',
//        __DIR__.'/config',
//        __DIR__.'/database',
//        __DIR__.'/resources',
//        __DIR__.'/routes',
//        __DIR__.'/tests',
//        __DIR__.'/src',
//    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);


return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
