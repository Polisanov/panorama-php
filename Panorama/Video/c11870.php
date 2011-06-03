<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * Wrapper class for 11870 videos
 *
 * @author Fran Diéguez <fran@openhost.es>
 * @version \$Id\$
 * @copyright OpenHost S.L., Mér Xuñ 01 15:58:58 2011
 * @package Panorama\Video
 **/
namespace Panorama\Video;

class c11870  {
    
    /*
     * __construct()
     * @param $url
     */
    public function __construct($url)
    {

        $this->url = $url;
        $this->getHash();

    }
    
    /*
     * Fetchs the contents of the 11870 video page
     * 
     */
    public function getPage()
    {
        if (!isset($this->page)) {
            $this->page = file_get_contents($this->url);
        }
        return $this->page;
    }
    
    /*
     * Sets the page contents, useful for using mocking objects
     * 
     * @param $arg
     */
    public function setPage($page = '')
    {
        if (!empty($page)) {
            $this->page = $page;
        }
        return $this;
    }
    
    /*
     * Returns the video id, allways null, not applicable
     * 
     */
    public function getVideoId()
    {
        return null;
    }
    
    /*
     * Returns the title for this 11870 video
     * 
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            
            //(Iconv.iconv 'utf-8', 'iso-8859-1', @page.search("//title").inner_html.split(" - www.11870.com")[0]).to_s
            preg_match('@<title>(.*)</title>@', $this->getPage(), $matches);
            $title = preg_split('@ - www.11870.com@', $matches[1]);
            $title = $title[0];
            $this->title = iconv('ISO-8859-1', 'UTF-8', (string) $title);
            
        }
        return $this->title;
    }
    
    /*
     * Returns the thumbnail for this 11870 video
     * 
     */
    public function getThumbnail()
    {
        if (!isset($this->thumbnail)) {
            $hash = $this->getHash();
            $this->thumbnail = $hash['image'];
        }
        return $this->thumbnail;
    }
    
    /*
     * Returns the duration in secs for this 11870 video
     * 
     */
    public function getDuration()
    {
        return null;
    }
    
    /*
     * Returns the embed url for this 11870 video
     * 
     */
    public function getEmbedUrl()
    {
        if (!isset($this->embedUrl)) {
            $hash = $this->getHash();
            $this->embedUrl = "http://11870.com/multimedia/flvplayer.swf?" . $this->getFlashVars() . "&logo=" . $hash['logo'];
        }
        return $this->embedUrl;
    }
    
    /*
     * Returns the HTML object to embed for this 11870 video
     * 
     */
    public function getEmbedHTML($options = array())
    {
        $defaultOptions = array(
              'width' => 560,
              'height' => 349 
              );
        
        $options = array_merge($defaultOptions, $options);
        unset($options['width']);
        unset($options['height']);
        
        // convert options into 
        $htmlOptions = "";
        if (count($options) > 0) {
            foreach ($options as $key => $value ) {
                $htmlOptions .= "&" . $key . "=" . $value;
            }
        }
        
        return "<object
                        width='{$defaultOptions['width']}' height='{$defaultOptions['height']}'
                        classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'
                        codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0'>
                    <param name='movie' value='{$this->getEmbedUrl()}' />
                    <param name='quality' value='high' />
                    <embed
                        src='{$this->getEmbedUrl()}'
                        width='{$defaultOptions['width']}' height='{$defaultOptions['height']}'
                        quality='high'
                        type='application/x-shockwave-flash'
                        pluginspage='http://www.macromedia.com/go/getflashplayer'/>
                </object>";
    
    }
    
    /*
     * Returns the FLV url for this 11870 video
     * 
     */
    public function getFLV()
    {
        //"http://videos.11870.com/contenidos3/#{CGI::parse(URI::parse(embed_url).query)['file']}"
        if (!isset($this->FLV)) {
            $hash = $this->getHash();
            $this->FLV = $hash['file'];
        }
        return $this->FLV;
    }
    
    /*
     * Returns the Download url for this 11870 video
     * 
     */
    public function getDownloadUrl()
    {
        return $this->getFLV();
    }
    
    /*
     * Returns the name of the Video service
     * 
     */
    public function getService()
    {
        return "11870";
    }
    
    /*
     * Returns the flashvars
     * 
     * @param $arg
     */
    public function getFlashVars()
    {
        if (!isset($this->flashvars)) {
            preg_match('@flashvars=&quot;(\S+)&quot;@', $this->getPage(), $matches);
            $this->flashvars = $matches[1];
        }
        return $this->flashvars;
    }
    
    /*
     * Calculates the Video ID from an 11870 URL
     * 
     * @param $url
     */
    public function getHash()
    {

        if (!isset($this->hash)) {
            $matches = $this->getFlashVars();
            $matches =  preg_split('@&@', $matches);
            foreach ($matches as $match) {
                $partialMatch = preg_split('@=@', $match);
                $this->hash[$partialMatch[0]] = $partialMatch[1];
            }
            unset($this->hash['displaywidth']);
        }
        return $this->hash;

    }
}