<?php 

namespace AMBERSIVE\KeepAChangelog\Classes;

use File;
use Str;

use Carbon\Carbon;

class ChangelogHelper {

    public static $identifierUnreleased = '## [Unreleased]';

    public static function path(String $repository, bool $addFileName = false):String {
        $repositories = config('keepachangelog.repositories', [
            'default' => [
                'path' => base_path()
            ]
        ]);

        $path = data_get($repositories, $repository.'.path', null);

        return $path . ($addFileName ? '/CHANGELOG.md' : '');
    }
        
    /**
     * Get the template from the template
     *
     * @return String
     */
    public static function template():String{
        return File::get(__dir__.'/../Files/CHANGELOG.md');
    }

    /**
     * Checks if the CHANGELOG file in the given repository is available
     *
     * @param  mixed $repository
     * @return bool
     */
    public static function prepare(String $repository = null):bool {

        $success = false;

        if ($repository === null) {
            return $success;
        }

        $path = self::path($repository, true);

        if (File::exists($path) === false || File::get($path) === "") {
            $content = str_replace('{{CHANGELOG-LINES}}', '', self::template());
            File::put($path,  $content);
        }

        $success = File::exists($path);

        return $success;

    }
    
    /**
     * Add a line to the CHANGELOG file
     *
     * @param  mixed $repository
     * @param  mixed $type
     * @param  mixed $text
     * @return bool
     */
    public static function addLine(String $repository, String $type, String $text):bool {

        $success = false;
        $path = self::path($repository, true);

        $fileExists = File::exists($path);

        if ($fileExists === false) {
            $fileExists = self::prepare($repository);
        }

        if ($fileExists === false) {
            return $success;
        }

        $content = self::parse($repository);

        if (empty($content)) {
            $content[self::$identifierUnreleased] = [];
        }

        if (isset($content[self::$identifierUnreleased][ucfirst($type)]) === false) {
            $content[self::$identifierUnreleased][ucfirst($type)] = [];
        }

        $content[self::$identifierUnreleased][ucfirst($type)][] = $text;

        ksort($content[self::$identifierUnreleased]);

        // Save the changes
        $success = self::toMarkdown($repository, $content);

        return $success;
    }
    
    /**
     * Save the array to markdown format
     *
     * @param  mixed $repository
     * @param  mixed $content
     * @return bool
     */
    public static function toMarkdown(String $repository, array $content = []):bool {

        $success = false;

        $lines = [];

        foreach($content as $release => $area){

            $lines[] = "${release}\n";

            foreach($area as $type => $areaLines) {

                $lines[] = "### ${type}\n";

                foreach($areaLines as $index => $line){
                    if ($line != "") {
                        $lines[] = in_array("- ${line}", $lines) === false ? "- ${line}\n" : "\n";
                    }
                }

                $lines[] = $lines[sizeOf($lines) - 1] != "\n" ? "\n" : "";

            }

            $lines[] = "";

        }

        $content = implode('', $lines);
        $template = self::template();

        $template = str_replace("{{CHANGELOG-LINES}}", $content, $template);


        // Save the markdown file
        File::put(self::path($repository, true),  $template);

        if (File::exists(self::path($repository, true))) {
            $success = true;
        }

        return $success;

    }
    
    /**
     * Convert a changelog file into an array
     *
     * @param  mixed $repository
     * @return array
     */
    public static function parse(String $repository): array {
        
        $path = self::path($repository, true);

        $content = [];
        $contentFile = File::exists($path) ? File::get($path) : "";

        preg_match_all("/##\s{0,}\[Unreleased\]|\#\#\s{0,}\[\d{1,}\.\d{1,}\.\d{1,}\]\s\-\s\d{4}\-\d{2}-\d{2}/", $contentFile, $result);

        $releases = $result[0];

        if (empty($releases) === true) {
            return [
                self::$identifierUnreleased => []
            ];
        }

        $reduced = substr($contentFile, strpos($contentFile, self::$identifierUnreleased));
        $parts = [];

        if (strripos($reduced, '[Unreleased]:') != false) {
            $reduced = substr($reduced, 0, strripos($reduced, '[Unreleased]:'));
        }

        foreach($releases as $index => $release) {
            $content[$release] = [];

            $term = isset($releases[$index + 1]) ? $releases[$index + 1] : "\n";
            $pos = strpos($reduced, $term);

            if ($pos !== false) {
                $part = substr($reduced, strpos($reduced, $release) + strlen($release), strripos($reduced, $term) - strpos($reduced, $release) - strlen($release));
                $part = trim(preg_replace("/\n{2,}/", "\n", $part));
                
                preg_match_all("/###\s[a-zA-Z]{1,}/", $part, $types);

                $typesList = [];

                // Extract from the file
                if (!empty($types[0])) {

                    collect($types[0])->each(function($type, $typeIndex) use ($release, $types, $part, &$content, &$typesList){

                        $start = strpos($part, $type);
                        $stop  = isset($types[0][$typeIndex + 1]) ? strripos($part, $types[0][$typeIndex + 1]) : strlen($part);
                        $typeSimple = substr($type, 4);

                        $partType = trim(str_replace("${type}", "", substr($part, $start, $stop - $start)));

                        $typeEntries = implode('', array_map(function($item){
                            if (Str::startsWith($item, '-') === true) {
                                $item = '[~/-/~]'.substr($item, 1);
                            }
                            return $item;
                        }, preg_split("/\n/", $partType)));

                        if (isset($typesList[$type]) == false) {
                            $typesList[$type] = [];
                        }

                        $typesOfList = collect($typeEntries)->filter(function($item) {
                            if ($item != "" && $item != "\n"){
                                return $item;
                            }
                        })->map(function($item){
                            return trim($item);
                        });

                        if (isset($content[$release][$typeSimple]) === false) {
                            $content[$release][$typeSimple] = [];
                        }

                        $typeEntriesSplitted = preg_split("/\[~\/-\/~\]/", $typesOfList->first());

                        $entries = collect($typeEntriesSplitted)->filter(function($item){
                            if ($item != "" && $item != "\n"){
                                return $item;
                            }
                        })->map(function($item){
                            return trim($item);
                        })->toArray();

                        $content[$release][$typeSimple] = $entries;

                    });

                }
            }
        }

        return $content;

    }
    
    /**
     * Release the unreleased parts of a changelog.md file
     *
     * @param  mixed $repository
     * @param  mixed $major
     * @param  mixed $minor
     * @param  mixed $patch
     * @return bool
     */
    public static function release(String $repository, Int $major = 0, Int $minor = 0, Int $patch = 0): bool {

        $success = false;

        $content = self::parse($repository);
        $date = Carbon::now()->format('Y-m-d');

        if (isset($content["## [${major}.${minor}.${patch}] - ${date}"]) == false) {
            $content["## [${major}.${minor}.${patch}] - ${date}"] = $content['## [Unreleased]'];
            $content['## [Unreleased]'] = [];
        }
        else {
            $content["## [${major}.${minor}.${patch}] - ${date}"] = array_merge($content["## [${major}.${minor}.${patch}] - ${date}"], $content['## [Unreleased]']);
            $content['## [Unreleased]'] = [];
        }

        if (File::exists(self::path($repository)) == false) {
            return $success;
        }

        uksort($content, function($a, $b){
            if ($a === '## [Unreleased]') {
                return -1;
            }
            else if ($b === '## [Unreleased]') {
                return -1;
            }
            return strcasecmp($b, $a);
        });

        $success = self::toMarkdown($repository, $content);

        return $success;

    }

}