<?php

declare(strict_types=1);

namespace App\Services;

use Exception;

class IPTC
{
    const OBJECT_NAME = '005';
    const EDIT_STATUS = '007';
    const PRIORITY = '010';
    const CATEGORY = '015';
    const SUPPLEMENTAL_CATEGORY = '020';
    const FIXTURE_IDENTIFIER = '022';
    const KEYWORDS = '025';
    const RELEASE_DATE = '030';
    const RELEASE_TIME = '035';
    const SPECIAL_INSTRUCTIONS = '040';
    const REFERENCE_SERVICE = '045';
    const REFERENCE_DATE = '047';
    const REFERENCE_NUMBER = '050';
    const CREATED_DATE = '055';
    const CREATED_TIME = '060';
    const ORIGINATING_PROGRAM = '065';
    const PROGRAM_VERSION = '070';
    const OBJECT_CYCLE = '075';
    const BYLINE = '080';
    const BYLINE_TITLE = '085';
    const CITY = '090';
    const PROVINCE_STATE = '095';
    const COUNTRY_CODE = '100';
    const COUNTRY = '101';
    const ORIGINAL_TRANSMISSION_REFERENCE = '103';
    const HEADLINE = '105';
    const CREDIT = '110';
    const SOURCE = '115';
    const COPYRIGHT_STRING = '116';
    const CAPTION = '120';
    const LOCAL_CAPTION = '121';
    const CAPTION_WRITER = '122';

    /**
     * variable that stores the IPTC tags
     *
     * @var array
     */
    private $_meta = [];

    /**
     * This variable was checks whether any tag class setada
     *
     * @var boolean
     */
    private $_hasMeta = false;


    /**
     * allowed extensions
     *
     * @var array
     */
    private $_allowedExt = ['jpg', 'jpeg', 'pjpeg'];

    /**
     * Image name ex. /home/user/image.jpg
     *
     * @var String
     */
    private $_filename;

    /**
     * Constructor class
     *
     * @param string $filename - Name of file
     *
     * @throws Exception
     * @see iptcparse
     * @see getimagesize
     * @see http://php.net/manual/en/book.image.php - PHP GD
     */
    public function __construct($filename)
    {
        /**
         * Check PHP version
         * @since 2.0.1
         */
        if (version_compare(phpversion(), '5.1.3', '<') === true) {
            throw new Exception(
                'ERROR: Your PHP version is ' . phpversion() .
                '. Iptc class requires PHP 5.1.3 or newer.'
            );
        }

        if (!extension_loaded('gd')) {
            throw new Exception(
                'Since PHP 4.3 there is a bundled version of the GD lib.'
            );
        }

        if (!file_exists($filename)) {
            throw new Exception(
                'Image not found!'
            );
        }

        if (!is_writable($filename)) {
            throw new Exception(
                "File \"{$filename}\" is not writable!"
            );
        }

        $parts = explode('.', strtolower($filename));

        if (!in_array(end($parts), $this->_allowedExt)) {
            throw new Exception(
                'Support only for the following extensions: ' .
                implode(',', $this->_allowedExt)
            );
        }

        $size = getimagesize($filename, $imageinfo);

        if (empty($size['mime']) || $size['mime'] != 'image/jpeg') {
            throw new Exception(
                'Support only JPEG images'
            );
        }

        $this->_hasMeta = isset($imageinfo["APP13"]);
        if ($this->_hasMeta) {
            $this->_meta = iptcparse($imageinfo["APP13"]);
        }

        $this->_filename = $filename;
    }


    /**
     * Set parameters you want to record in a particular tag "IPTC"
     *
     * @param Integer|const $tag  - Code or const of tag
     * @param array|mixed   $data - Value of tag
     *
     * @return Iptc object
     * @access public
     */
    public function set($tag, $data)
    {
        $data = $this->_charset_decode($data);
        $this->_meta["2#{$tag}"] = [$data];
        $this->_hasMeta = true;
        return $this;
    }

    /**
     * adds an item at the beginning of the array
     *
     * @param Integer|const $tag  - Code or const of tag
     * @param array|mixed   $data - Value of tag
     *
     * @return Iptc object
     * @access public
     */
    public function prepend($tag, $data)
    {
        $data = $this->_charset_decode($data);
        if (!empty($this->_meta["2#{$tag}"])) {
            array_unshift($this->_meta["2#{$tag}"], $data);
            $data = $this->_meta["2#{$tag}"];
        }
        $this->_meta["2#{$tag}"] = [$data];
        $this->_hasMeta = true;
        return $this;
    }

    /**
     * adds an item at the end of the array
     *
     * @param Integer|const $tag  - Code or const of tag
     * @param array|mixed   $data - Value of tag
     *
     * @return Iptc object
     * @access public
     */
    public function append($tag, $data)
    {
        $data = $this->_charset_decode($data);
        if (!empty($this->_meta["2#{$tag}"])) {
            array_push($this->_meta["2#{$tag}"], $data);
            $data = $this->_meta["2#{$tag}"];
        }
        $this->_meta["2#{$tag}"] = [$data];
        $this->_hasMeta = true;
        return $this;
    }

    /**
     * Return fisrt IPTC tag by tag name
     *
     * @param Integer|const $tag - Name of tag
     *
     * @return mixed|false
     * @example $iptc->fetch(Iptc::KEYWORDS);
     *
     * @access  public
     */
    public function fetch($tag)
    {
        if (isset($this->_meta["2#{$tag}"])) {
            return $this->_charset_encode($this->_meta["2#{$tag}"][0]);
        }
        return false;
    }

    /**
     * Return all IPTC tags by tag name
     *
     * @param Integer|const $tag - Name of tag
     *
     * @return mixed|false
     * @example $iptc->fetchAll(Iptc::KEYWORDS);
     *
     * @access  public
     */
    public function fetchAll($tag)
    {
        if (isset($this->_meta["2#{$tag}"])) {
            return $this->_charset_encode($this->_meta["2#{$tag}"]);
        }
        return false;
    }

    /**
     * debug that returns all the IPTC tags already in the image
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        return $this->_charset_encode(print_r($this->_meta, true));
    }

    /**
     * returns a string with the binary code
     *
     * @access public
     * @return string
     */
    public function binary()
    {
        $iptc = '';
        foreach (array_keys($this->_meta) as $key) {
            $tag = str_replace("2#", "", $key);
            foreach ($this->_meta[$key] as $value) {
                $iptc .= $this->iptcMakeTag(2, $tag, $value);
            }
        }
        return $iptc;
    }

    /**
     * Assemble the tags "IPTC" in character "ascii"
     *
     * @param Integer $rec - Type of tag ex. 2
     * @param Integer $dat - code of tag ex. 025 or 000 etc
     * @param mixed   $val - any character
     *
     * @access public
     * @return string binary source
     */
    public function iptcMakeTag($rec, $dat, $val)
    {
        //beginning of the binary string
        $iptcTag = chr(0x1c) . chr((int)$rec) . chr((int)$dat);

        if (is_array($val)) {
            $src = '';
            foreach ($val as $item) {
                $len = strlen($item);
                $src .= $iptcTag . $this->_testBitSize($len) . $item;
            }
            return $src;
        }

        $len = strlen($val);
        $src = $iptcTag . $this->_testBitSize($len) . $val;
        return $src;
    }

    /**
     * create the new image file already
     * with the new "IPTC" recorded
     *
     * @access public
     * @return string binary source
     * @throws Exception
     */
    public function write()
    {
        //@see http://php.net/manual/en/function.iptcembed.php
        $content = iptcembed($this->binary(), $this->_filename, 0);
        if ($content === false) {
            throw new Exception(
                'Failed to save IPTC data into file'
            );
        }

        unlink($this->_filename);

        if ($file = fopen($this->_filename, "w")) {
            fwrite($file, $content);
            //fwrite($file, pack("CCC",0xef,0xbb,0xbf));
            fclose($file);
            return true;
        }
        return false;
    }

    /**
     * completely remove all tags "IPTC" image
     *
     * @access public
     * @return string binary source
     */
    public function removeAllTags()
    {
        $this->_hasMeta = false;
        $this->_meta = [];
        $impl = implode(file($this->_filename));
        $img = imagecreatefromstring($impl);
        unlink($this->_filename);
        imagejpeg($img, $this->_filename, 100);
    }

    /**
     * It proper test to ensure that
     * the size of the values are supported within the
     *
     * @param Integer $len - size of the character
     *
     * @access public
     * @return string binary source
     */
    private function _testBitSize($len)
    {
        if ($len < 0x8000) {
            return
                chr($len >> 8) .
                chr($len & 0xff);
        }

        return
            chr(0x1c) . chr(0x04) .
            chr(($len >> 24) & 0xff) .
            chr(($len >> 16) & 0xff) .
            chr(($len >> 8) & 0xff) .
            chr(($len) & 0xff);
    }

    /**
     * Decode charset utf8 before being saved
     *
     * @param String $data
     * @access private
     * @return string decoded string
     */
    private function _charset_decode($data)
    {
        $result = [];
        if (is_array($data)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
            foreach ($iterator as $key => $value) {
                $result[] = $value;
            }
        } else {
            return $data;
        }
        return $result;
    }

    /**
     * Encode charset to utf8 before being saved
     *
     * @param String $data
     * @access private
     * @return string encoded string
     */
    private function _charset_encode($data)
    {
        $result = [];
        if (is_array($data)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
            foreach ($iterator as $key => $value) {
                $result[] = $value;
            }
        } else {
            return $data;
        }
        return $result;
    }
}