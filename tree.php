<?php 
class link{
	public $headNode;
	public $endNode;
	public $currentNode;
	public $lenght;
	
	public function link(){
		$this->lenght=0;
		$this->headNode=new node("headNode");
		$this->endNode=new node("endNode");
		$this->headNode->next=$this->endNode;
		$this->headNode->prv=$this->endNode;
		$this->endNode->next=$this->headNode;
		$this->endNode->prv=$this->headNode;
		}
	public function addNode($data){
		$this->currentNode=$this->headNode;
		$this->currentNode->prv=new node($data);
		$this->currentNode->prv->next=$this->headNode;
		$this->currentNode->prv->prv=$this->currentNode;
		$this->lenght++;
	}

}
class node{
	public $data;
	public $prv;
	public $next;

	public function node($data){
		$this->data=$data;
		$this->prv=null;
		$this->next=null;
	}
}

$mylink=new link();
// $mylink->addNode("y1");
// $mylink->addNode("y2");
// $mylink->addNode("y3");
// var_dump($mylink->headNode->next->next->next);
var_dump($mylink->endNode);
var_dump($mylink->headNode);


?>