<?php

namespace SocolaDaiCa\LaravelAudit\Audit;

class TokenParser extends \Doctrine\Common\Annotations\TokenParser
{
    /**
     * Parses a single use statement.
     *
     * @return array<string, string> A list with all found class names for a use statement.
     */
    public function parseUseFuntion()
    {
        $groupRoot = '';
        $class = '';
        $alias = '';
        $statements = [];
        $explicitAlias = false;

        if (is_null($token = $this->next()) || $token[0] !== T_FUNCTION) {
            return $statements;
        }

        while (($token = $this->next())) {
            if (!$explicitAlias && $token[0] === T_STRING) {
                $class .= $token[1];
                $alias = $token[1];
            } elseif ($explicitAlias && $token[0] === T_STRING) {
                $alias = $token[1];
            } elseif (
                PHP_VERSION_ID >= 80000
                && ($token[0] === T_NAME_QUALIFIED || $token[0] === T_NAME_FULLY_QUALIFIED)
            ) {
                $class .= $token[1];

                $classSplit = explode('\\', $token[1]);
                $alias = $classSplit[count($classSplit) - 1];
            } elseif ($token[0] === T_NS_SEPARATOR) {
                $class .= '\\';
                $alias = '';
            } elseif ($token[0] === T_AS) {
                $explicitAlias = true;
                $alias = '';
            } elseif ($token === ',') {
                $statements[mb_strtolower($alias)] = $groupRoot.$class;
                $class = '';
                $alias = '';
                $explicitAlias = false;
            } elseif ($token === ';') {
                $statements[mb_strtolower($alias)] = $groupRoot.$class;

                break;
            } elseif ($token === '{') {
                $groupRoot = $class;
                $class = '';
            } elseif ($token === '}') {
                continue;
            } else {
                break;
            }
        }

        return $statements;
    }

    /**
     * Gets all use statements.
     *
     * @param string $namespaceName The namespace name of the reflected class.
     *
     * @return array<string, string> A list with all found use statements.
     */
    public function parseUseFuntions($namespaceName)
    {
        $statements = [];

        while (($token = $this->next())) {
            if ($token[0] === T_USE) {
                $statements = array_merge($statements, $this->parseUseFuntion());

                continue;
            }

            if ($token[0] !== T_NAMESPACE || $this->parseNamespace() !== $namespaceName) {
                continue;
            }

            // Get fresh array for new namespace. This is to prevent the parser to collect the use statements
            // for a previous namespace with the same name. This is the case if a namespace is defined twice
            // or if a namespace with the same name is commented out.
            $statements = [];
        }

        return $statements;
    }
}
