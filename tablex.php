<?php

class Table {
    //propiedades
    private ?string $name;
    private ?Array $columns;
    private ?Array $footer;
    private Array $data;

    //constructor
    function __construct($data){
        $this->setData($data);
    }

    //getters y setters
    public function getName() : string {
        return $this->name;
    }
    public function setName($name) : void {
        if(isset($name) && is_string($name)) $this->name = $name;
    }
    public function getColumns() : Array {
        return $this->columns;
    }
    public function setColumns($columns) : void {
        $this->columns = $columns;
    }
    public function getData() : Array {
        return $this->data;
    }
    public function setData($data) : void {
        $this->data = $data;
    }
    public function getFooter() : Array {
        return $this->footer;
    }
    public function setFooter($footer) : void {
        $this->footer = $footer;
    }
    public function getHeigh() : int {
        return sizeof($this->data);
    }
    public function getWidth() : int {
        return sizeof($this->data[0]);
    }

    //metodos para convertir a html
    private function tableForeach($list) : string {
        $payload = "";
        foreach($list as $value)$payload .= "<th>$value</th>";
        return $payload;
    }
    public function toThead() : string {
        if(!isset($this->columns))return "";
        return "<thead><tr>".$this->tableForeach($this->columns)."</tr></thead>";
    }
    public function toFooter() : string {
        if(!isset($this->footer))return "";
        return "<tfoot><tr>".$this->tableForeach($this->footer)."</tr></tfoot>";
    }
    public function toTbody() : string {
        $payload = "<tbody>";
        foreach($this->data as $value)$payload .= "<tr>".$this->tableForeach($value)."</tr>";
        return "$payload</tbody>";
    }
    public function toHtmlTable() : string {
        return "<table>".$this->toThead().$this->toTbody().$this->toFooter()."</table>";
    }
    public function toHtmlTableWithClass($class) : string {
        if(!isset($class))return $this->toHtmlTable();
        return "<table class=\"$class\">".$this->toThead().$this->toTbody().$this->toFooter()."</table>";
    }
    public function toJson() : string {
        $payload = ["data"=>$this->data];
        if(isset($this->name))$payload["name"]=$this->name;
        if(isset($this->columns))$payload["columns"]=$this->columns;
        if(isset($this->footer))$payload["footer"]=$this->footer;
        return json_encode($payload);
    }
    public function fromJson($json) : void {
        //$data = json_decode($json);
        trigger_error("el metodo fromJson() aun no ha sido implementado",E_USER_WARNING);
        //TODO
    }
}

?>