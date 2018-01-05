<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace orm;

/**
 * Description of Post
 *
 * @author olga
 * @Entity @Table(name="posts")
 */
class Post implements Restable
{
	/**
     * @var int
	 * @Id @Column(type="integer") @GeneratedValue
     */
    protected $id;

	/**
     * @var Category
	 * @ManyToOne(targetEntity="Category", inversedBy="categories")
     **/
    protected $category;
	
	/**
	 * @var \DateTime
     * @Column(type="datetime")
     **/
    protected $created;

	/**
	 * @var string
     * @Column(type="string")
     **/
    protected $title;
	
    /**
	 * @var string
     * @Column(type="text")
     **/
    protected $text;
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setCategory(Category $category)
    {
        $this->category = $category;
    }
	
	public function getCategory() : Category
	{
		return $this->category;
	}
	
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    public function getCreated() : \DateTime
    {
        return $this->created;
    }

	public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getTitle() : string
    {
        return $this->title;
    }
	
    public function setText(string $text)
    {
        $this->text = $text;
    }

    public function getText() : string
    {
        return $this->text;
    }

	public function toResult(): array {
		return [
			'id' => $this->getId(),
			'title' => $this->getTitle(),
			'text' => $this->getText(),
			'created' => $this->getCreated()->format('Y-m-d'),
			'categoryId' => $this->getCategory()->getId()
		];	
	}
}
