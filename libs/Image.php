<?php
    require_once "bytes.php";
    class Image extends Image {
        public String $alt = "";

        public function toHTML() : String {
            $html = "<img";
            if($this->alt){
                $html .= " alt=\"$this->alt\"";
            }
            $html .= " src=\"data:".$this->getMimeType().";";
            $html .= "base64,".base64_encode($this->getBytes())."\"";
            $html .= ">";
            return $html;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=|, initial-scale=1.0">
    <title>EEEEE</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="file">
        <input type="submit">
    </form>
    <?php
        if(isset($_FILES["file"])){
            $img = new Image($_FILES["file"]["tmp_name"]);
            echo $img->toHTML();
        }
    ?>
</body>
</html>