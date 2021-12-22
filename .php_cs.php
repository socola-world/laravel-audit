<?php
/*
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.0
 *
 * */
use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PSR12' => true,
    '@PSR2' => true,
    'align_multiline_comment' => true,
    'array_indentation' => true,
    'array_push' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => [
        'default' => 'single_space',
//        'operators' => ['=>' => null],
    ],
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => [
        'statements' => ['return'],
    ],
    'braces' => true,
    'cast_spaces' => true,
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
        ],
    ],
    'class_definition' => [
        'multi_line_extends_each_single_line' => true,
        'single_item_single_line' => true,
        'single_line' => true,
    ],

    'clean_namespace' => true,
    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'concat_space' => [
        'spacing' => 'none',
    ],
    'constant_case' => [
        'case' => 'lower'
    ],
    'declare_equal_normalize' => true,
    'dir_constant' => true,
    'elseif' => true,
    'encoding' => true,
    'ereg_to_preg' => true,
    'explicit_indirect_variable' => true,
    'explicit_string_variable' => true,
    'fopen_flag_order' => true,
    'full_opening_tag' => true,
    'fully_qualified_strict_types' => true,
    'function_declaration' => true,
    'function_to_constant' => true,
    'function_typehint_space' => true,
//    'general_phpdoc_annotation_remove' => []
    'general_phpdoc_tag_rename' => true,
//    'group_import' => false,
//    'header_comment'
//    'heredoc_indentation'
    'heredoc_to_nowdoc' => true,
//    'implode_call',
    'include' => true,
    'increment_style' => ['style' => 'post'],
    'indentation_type' => true,
    'linebreak_after_opening_tag' => true,
    'list_syntax' => ['syntax' => 'short'],
    'line_ending' => true,
    'lowercase_cast' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_method_casing' => true,
    'magic_constant_casing' => true,
    'mb_str_functions' => true,
    'method_argument_space' => true,
    'method_chaining_indentation' => true,
//    'modernize_types_casting' => false,
    'multiline_whitespace_before_semicolons' => true,
    'native_function_type_declaration_casing' => true,
    'native_function_casing' => true,
    'no_alias_functions' => true,
    'no_alternative_syntax' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
//    'no_blank_lines_before_namespace' => true, conflict with "single_blank_line_before_namespace"
    'no_break_comment' => true,
    'no_closing_tag' => true,
    'no_empty_comment' => true,
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
    'no_homoglyph_names' => true,
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
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => [
        'positions' => ['inside', 'outside'],
    ],
    'no_spaces_inside_parenthesis' => true,
    'no_superfluous_elseif' => true,
//    no_superfluous_phpdoc_tags
//    'no_trailing_comma_in_list_call' => true,
    'no_trailing_comma_in_singleline_array' => true,
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,
    'no_unneeded_control_parentheses' => true,
    'no_unreachable_default_argument_value' => true,
    'no_unset_cast' => true,
    'no_unused_imports' => true,
    'no_useless_else' => true,
    'no_useless_return' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    'normalize_index_brace' => true,
//    'not_operator_with_successor_space' => true, // optional
    'nullable_type_declaration_for_default_null_value' => [
        'use_nullable_type_declaration' => true,
    ],
    'object_operator_without_whitespace' => true,
    'operator_linebreak' => [
        'only_booleans' => false,
        'position' => 'beginning',
    ],
//    'ordered_class_elements' => true,
    'ordered_imports' => [
        'imports_order' => [
            'class',
            'function',
            'const',
        ],
        'sort_algorithm' => 'alpha',
    ],
    'ordered_interfaces' => [
        'direction' => 'ascend',
        'order' => 'alpha'
    ],
    'ordered_traits' => true,
    'php_unit_method_casing' => [
        'case' => 'camel_case',
    ],
    'php_unit_test_annotation' => [
        'style' => 'prefix',
    ],
    'php_unit_test_case_static_method_calls' => [
        'call_type' => 'static',
    ],
    'phpdoc_add_missing_param_annotation' => true,
    'phpdoc_align' => [
        'align' => 'left',
    ],
    'phpdoc_indent' => true,
    'phpdoc_inline_tag_normalizer' => true,
    'phpdoc_line_span' => [
        'const' => 'multi',
        'method' => 'multi',
        'property' => 'multi',
    ],
    'phpdoc_no_access' => true,
    'phpdoc_no_alias_tag' => true,
    'phpdoc_no_empty_return' => true,
    'phpdoc_no_package' => true,
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
    'phpdoc_scalar' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_summary' => true,
    'phpdoc_tag_casing' => true,
    'phpdoc_tag_type' => true,
    'phpdoc_to_comment' => true,
//    'phpdoc_to_property_type' => true,
//    'phpdoc_to_return_type' => true // option
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => [
        'null_adjustment' => 'always_last',
        'sort_algorithm' => 'alpha',
    ],
    'phpdoc_var_annotation_correct_order' => true,
    'phpdoc_var_without_name' => true,
    'protected_to_private' => false,
    'psr_autoloading' => true,
    'random_api_migration' => [
        'replacements' => [
            'rand' => 'random_int',
        ],
    ],
    'return_type_declaration' => [
        'space_before' => 'none',
    ],
    'self_accessor' => true,
    'semicolon_after_instruction' => true,
    'short_scalar_cast' => true,
    'simplified_null_return' => false, // disabled by Shift
    'single_blank_line_at_eof' => true,
    'single_blank_line_before_namespace' => true,
    'single_class_element_per_statement' => true,
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    'single_line_comment_style' => [
        'comment_types' => ['hash'],
    ],
//    'single_line_throw' => false, // option
    'single_quote' => true,
    'single_space_after_construct' => true,
//    'single_trait_insert_per_statement' => false, // option
    'space_after_semicolon' => true,
    'standardize_not_equals' => true,
//    'strict_comparison' => false, // option
    'switch_case_semicolon_to_colon' => true,
    'switch_case_space' => true,
    'switch_continue_to_break' => true,
    'ternary_operator_spaces' => true,
    'ternary_to_null_coalescing' => true,
    'trailing_comma_in_multiline' => [
        'elements' => [
            'arrays',
        ],
    ],
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'visibility_required' => [
        'elements' => [
            'property',
            'method',
            'const',
        ],
    ],
    'whitespace_after_comma_in_array' => true,
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
