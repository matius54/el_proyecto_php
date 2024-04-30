<?php
    $base = $base ?? "../";  
    require_once $base . "libs/db.php";
    require_once $base . "libs/utils.php";
    
    class Paginator {
        private int $itemsPerPage;
        private string $pageKey;

        private ?int $lastPage = null;
        private ?int $page = null;
        private ?int $itemCount = null;
        public array $items = [];

        public function __construct(string $sql, array $args = [], int $itemsPerPage = 10, string $pageKey = "page"){
            $name = self::sqlSelect2tableName($sql);
            $this->itemsPerPage = $itemsPerPage;
            $this->pageKey = $pageKey;
            if($name === null) throw new Exception("paginator: name for the table not found");
            $this->getTotalPages($name);
            $this->getPage();
            $this->items = $this->lastPage ? $this->paginateQuery($sql, $args) : [];
        }
        
        private function getPage() : void {
            $lastPage = $this->lastPage;
            $page = URL::decode($this->pageKey);
            if(is_string($page) || is_double($page)) $page = intval($page);
            if($page === null || !is_int($page)) $page = 1;
            if($page < 1) $page = 1;
            if($page > $lastPage) $page = $lastPage;
            $this->page = $page;
        }

        private function paginateQuery(string $sql, array $args = []) : array {
            $limit = $this->itemsPerPage;
            $offset = ($this->page - 1) * $this->itemsPerPage;

            $db = DB::getInstance();
            $db->execute($sql." LIMIT ? OFFSET ?",array_merge($args,[$limit, $offset]));
            return $db->fetchAll(htmlspecialchars: true);
        }

        private function getTotalPages(string $name) : void {
            $db = DB::getInstance();
            $db->execute("SELECT COUNT(*) FROM $name");
            if($response = $db->fetch()){
                $itemCount = $response["COUNT(*)"];
                $lastPage = ceil($itemCount / $this->itemsPerPage);
                $this->itemCount = $itemCount;
                $this->lastPage = $lastPage;
            }
        }
        
        private static function sqlSelect2tableName($sql) : string|null {
            $sql = explode(" ",$sql);
            foreach ($sql as $key => $value) {
                if(strtoupper($value) === "FROM") return $sql[$key + 1];
            }
            return null;
        }
        
        public function toArray() : array {
            return [
                "items" => $this->items,
                "isLast" => $this->lastPage === $this->page,
                "page" => $this->page,
                "lastPage" => $this->lastPage,
                "itemsPerPage" => $this->itemsPerPage,
                "itemCount" => $this->itemCount
            ];
        }
    
        public function htmlMenu() : string {
            if($this->lastPage === 0) return "<i>Lista vacía.</i>";
            $html = "<i>Mostrando página ".$this->page." de ".$this->lastPage;
            if($this->itemCount !== null) $html .= ", con un total de ".$this->itemCount." elementos";
            $html .= ".</i>";
            $html .= "<ul class=\"navigator\">";
            if($this->page > 1){
                $html .= "<li><a href=\"".URL::URIquery([$this->pageKey => 1])."\" title=\"Primera página\">&lt;&lt;</a></li>";
                $html .= "<li><a href=\"".URL::URIquery([$this->pageKey => $this->page - 1])."\" title=\"Página anterior\">&lt;</a></li>";
            }
            for($i = 1; $i <= $this->lastPage; $i++) {
                $html .= "<li><a ";
                if($this->page === $i){
                    $html .= "class=selected ";
                }else{
                    $html .= "href=\"".URL::URIquery([$this->pageKey => $i])."\" ";
                }
                $html .= "title=\"Página $i\">$i</a></li>";
            }
            if($this->page < $this->lastPage){
                $html .= "<li><a href=\"".URL::URIquery([$this->pageKey => $this->page + 1])."\" title=\"Página siguiente\">&gt;</a></li>";
                $html .= "<li><a href=\"".URL::URIquery([$this->pageKey => $this->lastPage])."\" title=\"Última página\">&gt;&gt;</a></li>";
            }
            $html .= "</ul>";
            return $html;
        }
    }
?>