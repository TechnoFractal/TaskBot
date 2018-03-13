<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace orm;

/**
 * Description of Category
 *
 * @author olga
 * @Entity
 */
class Category implements \interfaces\Restable
{
	/**
     * @var int
	 * @Id @Column(type="integer")
     */
    protected $id;
    
	/**
     * @var string
	 * @Column(type="string")
     */
    protected $title;
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId(int $id)
	{
		$this->id = $id;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setTitle(string $title)
	{
		$this->title = $title;
	}

	public function toResult(): array {
		return [
			'id' => $this->getId(),
			'title' => $this->getTitle()
		];
	}

	public function __toString() 
	{
		return $this->getId() . ": " . $this->getTitle();
	}
}
