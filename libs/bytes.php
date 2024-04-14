<?php
    class Bytes {
        protected String $bytes;
        //MySQL MEDIUMBLOB = 16.78 MB - 16777215 bytes
        protected int $maxSize = 16777215;
        function __construct(string $bytes){
            if(strlen($bytes) > $this->maxSize) throw new OverflowException("Limite maximo de bytes superado de 16.78 MB (".$this->maxSize.")");
            $this->bytes = $bytes;
        }
        public function getBytes() : String {
            return $this->bytes;
        }
        public function getHash($algo = "sha256") : String {
            return hash($algo,$this->getBytes());
        }
        public function getSize() : int {
            return strlen($this->bytes);
        }
        public function getSizeKB(): float {
            return $this->getSize() / 1000;
        }
        public function getSizeMB(): float {
            return $this->getSizeKB() / 1000;
        }
        public function getSizeGB(): float {
            return $this->getSizeMB() / 1000;
        }
        public function __toString() : string {
            return $this->getBytes();
        }
    }
    //TODO adaptar File para funcionar mejor con Bytes
    class File extends Bytes {
        protected String $path;
        public function getPath() : String {
            return $this->path;
        }
        public function isValid() : bool {
            return ($this->bytes && $this->path);
        }
        public function setPath($path) : void {
            $this->path = $path;
        }
        public function getMimeType() : String {
            return mime_content_type($this->getPath());
        }
        public function getName() : String {
            return basename($this->getPath());
        }
        public function getExtension() : String {
            $fileName = explode(".",$this->getName());
            return end($fileName);
        }
    }
    class Image extends File {

    }
?>