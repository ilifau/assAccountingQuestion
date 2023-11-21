<?php

use ILIAS\Repository\BaseGUIRequest;
use Psr\Http\Message\UploadedFileInterface;

class assAccountingQuestionRequest
{
    use BaseGUIRequest;

    protected \ILIAS\HTTP\Services $http;
    protected \ILIAS\FileUpload\FileUpload $upload;

    public function __construct(
        \ILIAS\HTTP\Services $http,
        \ILIAS\Refinery\Factory $refinery,
        \ILIAS\FileUpload\FileUpload $upload
    ) {
        $this->initRequest(
            $http,
            $refinery
        );
        $this->upload = $upload;
    }

    /**
     * Get a string value from the request (HTML is stripped)
     */
    public function getInt(string $key) : int
    {
        return $this->int($key);
    }

    /**
     * Get an array of integers from the request
     * @return int[]
     */
    public function getIntArray(string $key) : array
    {
        return $this->intArray($key);
    }
    
    
    /**
     * Get a string value from the request (HTML is stripped)
     */
    public function getString(string $key) : string
    {
        return $this->str($key);
    }

    /**
     * Get an xml string from the request (HTML is NOT stripped)
     */
    public function getXml(string $key) : string 
    {
        if ($this->isArray($key)) {
            return "";
        }
        $t = $this->refinery->kindlyTo()->string();
        return \ilUtil::stripOnlySlashes((string) ($this->get($key, $t) ?? ""));
    }
    
    

    /**
     * Check if a file is uploaded with a key
     *
     * @param string $key      key of the $_FILES array, should not be nested
     * @return string
     */
    public function hasFile(string $key) : string 
    {
        $uploaded_files = $this->http->request()->getUploadedFiles();
        
        return isset($uploaded_files[$key]) 
            && $uploaded_files[$key] instanceof UploadedFileInterface
            && $uploaded_files[$key]->getError() == UPLOAD_ERR_OK;
    }

    /**
     * Get the contents of a file that is upladed with a key
     *
     * @param string $key      key of the $_FILES array, should not be nested
     * @return string
     */
    public function getFileContent(string $key) : string
    {
        $uploaded_files = $this->http->request()->getUploadedFiles();

        if (isset($uploaded_files[$key])
            && $uploaded_files[$key] instanceof UploadedFileInterface
            && $uploaded_files[$key]->getError() == UPLOAD_ERR_OK) {
            
            return (string) $uploaded_files[$key]->getStream();
        }
        return '';
    }
}

