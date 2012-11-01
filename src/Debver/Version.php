<?php
/**
 * debver - Debian/Ubuntu packager version helper
 * Simple PHP helper class for comparing Debian/Ubuntu package version strings.
 *
 * Copyright (c) 2012, Stephen Rees-Carter <http://stephen.rees-carter.net/>
 * New BSD Licence, see LICENCE.txt
 */

namespace Debver;

require_once __DIR__ ."/VersionException.php";

class Version
{
    /**
     * @var String
     */
    const EXPLODE = "/^(\d+:)?(\d+(?:\.\d+)*)([a-z0-9]+)?(?:\.([a-z]+[a-z0-9\.]*))?(?:(?:-|\+|~|_)(?:(\d*(?:\.\d+)*)([a-z]*))?(\d*(?:\.\d+)*))?(?:(?:-|\+|~)?(?:(\d*(?:\.\d+)*)([a-z]*))?(\d*(?:\.\d+)*))??(?:(?:-|\+|~|\.)?(?:(\d*(?:\.\d+)*)([a-z0-9]*))?(\d*(?:\.\d+)*))??(?:(?:-|\+|~)?(?:(\d*(?:\.\d+)*)([a-z0-9]*))?(\d*(?:\.\d+)*))??$/i";
    const BOOKEND = '#';
    const STRPAD  = ' ';

    /**
     * @var Integer
     */
    const ZEROPADLEN = 3;


    /**
     * Compares two Debian\Ubuntu package verison strings and returns:
     *  -1 => $version1 <  $version1
     *   0 => $version1 == $version2
     *  +1 => $version1 >  $version1
     *
     * @param  String  $version1
     * @param  String  $version2
     * @return Integer
     */
    static public function compare($version1, $version2)
    {
        /**
         * Check equal
         */
        if ($version1 == $version2) {
            return 0;
        }


        /**
         * Explode versions
         */
        $version1 = self::explode($version1);
        $version2 = self::explode($version2);


        /**
         * Loop and compare
         */
        foreach ($version1 as $key => $value) {

            /**
             * If no more v2 components, we assume v1 is better
             */
            if (!isset($version2[$key])) {
                return 1;
            }


            /**
             * String compare values
             */
            $compare = strcmp($value, $version2[$key]);
            if ($compare < 0) {
                return -1;
            } elseif ($compare > 0) {
                return 1;
            }
        }


        /**
         * Check for left over v2
         */
        if (count($version2)) {
            return -1;
        }


        /**
         * Else, must be equal
         */
        return 0;
    }


    /**
     * Compares two Debian\Ubuntu package verison strings using DPKG via command
     * line and returns:
     *  -1 => $version1 <  $version1
     *   0 => $version1 == $version2
     *  +1 => $version1 >  $version1
     *
     * @param  String  $version1
     * @param  String  $version2
     * @return Integer
     */
    static public function compareWithDpkg($version1, $version2)
    {
        if ($version1 == $version2) {
            return 0;
        }

        $cmd = str_replace("_", "", "dpkg --compare-versions {$version1} lt {$version2}");
        $cmd = escapeshellcmd($cmd);
        system($cmd, $status);

        return $status ? 1 : -1;
    }


    /**
     * Returns a verbose interpretation of $version which can be used
     * in basic string comparison operations (>, <, ==).
     *
     * $padding is used to pad out the different version components, and MUST
     * be te same value for each string being compared. It is a good idea to set
     * this to the length of largest version number.
     *
     * @param  String  $version  Version string
     * @param  Integer $padding  Length of padding to use in string
     * @return String
     */
    static public function getCompareString($version, $padding = 20)
    {
        /**
         * Sanity check
         */
        if (in_array($version, Array(null, "", ".", "-"))) {
            return "";
        }


        /**
         * Loop components and pad
         */
        $output     = "";
        $components = self::explode($version);
        foreach ($components as $value) {
            $dir     = is_numeric($value) ? STR_PAD_RIGHT : STR_PAD_LEFT;
            $output .= str_pad($value, $padding, self::STRPAD, $dir);
        }


        /**
         * Add bookends to stop accidental trim() and return
         */
        return self::BOOKEND.rtrim($output).self::BOOKEND;
    }


    /**
     * Explodes a version string into the useful components
     *
     * @param  String $version
     * @return Array
     * @throws VersionException
     */
    static protected function explode($version)
    {
        /**
         * Throw exception if super regex fails
         */
        $version = str_replace("_", "", $version);
        if (!preg_match(self::EXPLODE, $version, $matches)) {
            $msg = "Unable to parse version string: {$version}";
            throw new VersionException($msg);
        }


        /**
         * Loop matches
         */
        $components = Array();
        array_shift($matches);
        foreach ($matches as $match) {

            if (!preg_match("/^([0-9\.]+)$/", $match)) {
                $components[] = $match;
                continue;
            }

            $parts = explode(".", $match);
            foreach ($parts as $key => $value) {
                $parts[$key] = str_pad($value, self::ZEROPADLEN, "0", STR_PAD_LEFT);
            }

            $match        = implode($parts);
            $components[] = str_pad($match, self::ZEROPADLEN * 4, "0");
        }

        return $components;
    }
}
