<?php

namespace AMBERSIVE\KeepAChangelog\Tests\Unit\Classes;

use Tests\TestCase;

use Config;
use File;

use AMBERSIVE\KeepAChangelog\Classes\ChangelogHelper;

class ChangelogHelperTest extends TestCase
{
    
    public String $testFilesPath;
    public String $testFile;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('keepachangelog.repositories.default.path', __dir__.'/../../Files');

        $this->testFilesPath = ChangelogHelper::path('default');
        $this->testFile = ChangelogHelper::path('default', true);

        File::exists($this->testFile) == true ? File::delete($this->testFile) : null;

    }

    /**
     * Test if the returned path is equals to the provided path in the config
     */
    public function testIfChangelogHelperPathMethodReturnsAValidPath():void {

        $path = ChangelogHelper::path('default');
        $this->assertEquals($path, config('keepachangelog.repositories.default.path'));

    }

    /**
     * Test if the returned path is equals to a CHANGELOG.md file path
     */
    public function testIfChangelogHelperPathMethodReturnsAValidFilePath():void {

        $path = ChangelogHelper::path('default', true);
        $this->assertEquals($path, config('keepachangelog.repositories.default.path')."/CHANGELOG.md");

    }

    /**
     * Test if the path method returns the correct path from the config
     */
    public function testIfChangelogHelperPathMethodReturnsTheCorrectPath(): void {

        Config::set('keepachangelog.repositories.test.path', __dir__.'/../../XXX');
        $path = ChangelogHelper::path('test', true);
        $this->assertNotEquals($path, config('keepachangelog.repositories.default.path')."/CHANGELOG.md");
    }

    /**
     * Test if the template for the changelog can be loaded from the files folder
     */
    public function testIfChangelogHelperTemplateMethodReturnsAChangelogTemplate():void {

        $template = ChangelogHelper::template();

        $this->assertNotNull($template);

        $this->assertNotFalse(strpos($template, "# Changelog"));
        $this->assertNotFalse(strpos($template, "{{CHANGELOG-LINES}}"));

    }

    /**
     * Test if the prepare method will create a CHANGELOG.md file in the folder provided
     * by the config
     */
    public function testIfChangelogHelperPrepareMethodWillCreateAFile():void {

        $result = ChangelogHelper::prepare('default');

        $this->assertTrue($result);       
        $this->assertTrue(File::exists($this->testFile));
        
    }

    /**
     * Test if add line method will the text message to the CHANGELOG FILE
     */
    public function testIfChangelogHelperAddLineWillALineToTheFile():void {

        $result = ChangelogHelper::addLine('default', 'added', 'XXX - XXX');

        $content = File::get($this->testFile);

        $this->assertTrue($result);
        $this->assertTrue(File::exists($this->testFile));

        $this->assertNotFalse(strpos($content, '## [Unreleased]'));
        $this->assertNotFalse(strpos($content, '### Added'));
        $this->assertNotFalse(strpos($content, '- XXX - XXX'));

    }

    /**
     * Test if the method can be executed multiple times and will add the
     * line to the unreleased area multiple times
     */
    public function testIfChangelogHelperAddLineCanBeExecutedMutipleTimes():void {

        $result = ChangelogHelper::addLine('default', 'added', 'XXX - XXX');
        $result2 = ChangelogHelper::addLine('default', 'added', 'XXX - XXX');

        $content = File::get($this->testFile);
        preg_match_all("/\-\sXXX\s\-\sXXX/", $content, $matches);

        $this->assertTrue($result);
        $this->assertTrue($result2);
        $this->assertNotFalse(strpos($content, '## [Unreleased]'));
        $this->assertNotFalse(strpos($content, '### Added'));
        $this->assertEquals(2, sizeOf($matches[0]));

    }

}